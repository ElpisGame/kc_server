<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\WechatHelper;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use Spatie\Permission\Models\Role;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $phone = $request->input("username");
        $password = $request->input("password");

        $account = Account::query()->where("phone_number", $phone)->first();
        if ($account && $account->password == $password) {
            $token = $account->login();
            return $this->respondWithToken($token);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function me()
    {
        $account = auth('admin')->user();
        $account = Account::with('user')->findOrFail($account->id);
        return new JsonResource($account);
    }

    public function logout()
    {
        auth('admin')->logout();

        return new JsonResource(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('admin')->refresh());
    }

    protected function respondWithToken($token)
    {
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admin')->factory()->getTTL() * 60
        ];
        return response()->json($data, 200);
    }


    public function wechatQrcode(Request $request)
    {
        $event_key = Uuid::uuid4()->toString();
        $expire_seconds = 60 * 5;
        $data = WechatHelper::qrcodeTemporary($event_key, $expire_seconds);
        return new JsonResource($data);
    }

    public function wechatQrcodeStatus(Request $request)
    {
        $uuid = $request->get("uuid");
        if (!Cache::has("qrcode.uuid.{$uuid}")) {
            return new JsonResource(["cache is empty"]);
        }
        #查询用户信息
        $payload = Cache::get("qrcode.uuid.{$uuid}");
        $openid = $payload['FromUserName'];
        $user = User::query()->where('openid', $openid)->first();
        $account = Account::query()->where("openid", $user->openid)->first();
        if (empty($account)) {
            return new JsonResource("user not found");
        } else {
            $token = $account->login();
            return $this->respondWithToken($token);
        }
    }

    public function wechatOauth(Request $request)
    {
        $redirect_uri = $request->get('redirect_uri'); #用户提供的redirect_uri
        $factory = Factory::officialAccount(config("wechat.officialAccount"));
        $params = ['redirect_uri' => $redirect_uri];
        $oauth_redirect_url = route('wechat.admin.oauth.callback', $params);
        $url = $factory->oauth->scopes(['snsapi_userinfo'])->redirect($oauth_redirect_url);
        return redirect($url);
    }

    public function wechatOauthCallback(Request $request)
    {
        $redirect_uri = $request->get('redirect_uri');
        $code = $request->get('code');
        $factory = Factory::officialAccount(config("wechat.officialAccount"));
        $oauth = $factory->oauth;
        $wechatUser = $oauth->userFromCode($code);
        $raw = $wechatUser->getRaw();
        //保存用户信息
        $openid = $raw['openid'];
        $user = User::query()->where('openid', $openid)->first();
        if (empty($user)) {
            $fields = collect($raw)->only(["openid", "nickname", "headimgurl", "language", "sex", "province", "city", "country"])->toArray();
            $user = new User($fields);
            $user->uuid = Uuid::uuid4()->toString();
            $user->qrscene = 'oauth';
            $user->save();
        }
        $token = 'account is null';
        $account = Account::query()->where("openid", $openid)->first();
        if ($account) {
            $token = $account->login();
        }
        $url = $redirect_uri . "?token={$token}";
        return redirect($url);
    }

    public function menu()
    {
        $account = auth('admin')->user();
        //生成菜单
        $menu['router'] = 'root';
        $menu['children'][0]['router'] = 'dashboard';
        $menu['children'][0]['children'] = ["analysis"];
        //核心功能
        $menu['children'][1]['router'] = 'core';
        $menu['children'][1]['children'] = ["orders", "bids", 'policy', "priceSearch"];
        //系统设置
        $menu['children'][2]['router'] = 'setting';
        if ($account->hasRole(['超级管理员', '管理员'])) {
            $menu['children'][2]['children'] = ["accounts", "wechat", "payment", "priceSetting", "article", "otherSetting"];
        } else {
            $menu['children'][2]['children'] = ["accounts", "wechat"];
        }
        return $menu;
    }

    /**
     * 给Account分配角色
     */
    public function role()
    {
        Role::findOrCreate("超级管理员");
        Role::findOrCreate("管理员");
        Role::findOrCreate("客服");
        Role::findOrCreate("商户");
        #给全部账号分配商户权限
        Account::query()->get()->each(function ($account) {
            $account->assignRole("商户");
        });
        #分配超级管理员
        $account = Account::query()->findOrFail(1);
        if ($account) $account->assignRole("超级管理员");
        #分配管理员
        $account = Account::query()->where("real_name", '石可可')->first();
        if ($account) $account->assignRole("管理员");
        $account = Account::query()->where("real_name", '戴文中')->first();
        if ($account) $account->assignRole("管理员");
        #TODO 分配客服
        return true;
    }

    public function test()
    {
        $account = auth('admin')->user();

        return $account->hasRole(['超级管理员', '管理员']);
    }
}
