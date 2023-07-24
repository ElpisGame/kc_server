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

class UserAuthController extends Controller
{
    public function login(Request $request)
    {

    }

    public function me()
    {
        $user = auth('api')->user();
        return new JsonResource($user);
    }

    public function logout()
    {
        auth('api')->logout();

        return new JsonResource(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken($token)
    {
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
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
        if (empty($user)) {
            return new JsonResource("user not found");
        } else {
            $token = $user->login();
            return new JsonResource(["user" => $user, "token" => $token]);
        }
    }

    public function wechatOauth(Request $request)
    {
        $redirect_uri = $request->get('redirect_uri'); #用户提供的redirect_uri
        $factory = Factory::officialAccount(config("wechat.officialAccount"));
        $params = ['redirect_uri' => $redirect_uri];
        $oauth_redirect_url = route('wechat.api.oauth.callback', $params);
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
        $token = $user->login();
        $url = $redirect_uri . "?token={$token}";
        return redirect($url);
    }

    public function miniProgram(Request $request)
    {
        $jscode = $request->get("jscode");
        $app = WechatHelper::miniProgram();
        $session = $app->auth->session($jscode);

        $user = User::query()->where('openid', $session['openid'])->first();
        if (empty($user)) {
            $user = new User();
            $user->openid = $session['openid'];
            $user->uuid = Uuid::uuid4()->toString();
            $user->save();
        }
        $token = $user->login();
        return new JsonResource(["user" => $user, "token" => $token]);
    }
}
