<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Requests\AccountRequest;
use App\Http\Controllers\Admin\Resources\AccountResource;
use Faker\Factory;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Account;
use Illuminate\Support\Facades\Storage;
use function EasyWeChat\Kernel\Support\str_random;

class AccountController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:超级管理员|管理员']);
    }

    public function index(AccountRequest $request)
    {
        $query = Account::query();
        #search
        $id = $request->input('id');
        if (isset($id)) {
            $query->where("id", $id);
        }

        $pid = $request->input('pid');
        if (isset($pid)) {
            $query->where("pid", $pid);
        }

        $role = $request->input('role');
        if (isset($role)) {
            $query->where("role", $role);
        }

        $city_code = $request->input('city_code');
        if (isset($city_code)) {
            $query->where("city_code", $city_code);
        }
        $node_type = $request->input('node_type');
        if (isset($node_type)) {
            $query->where("node_type", $node_type);
        }

        $real_name = $request->input('real_name');
        if (isset($real_name)) {
            $query->where("real_name", 'like', "%{$real_name}%");
        }

        $phone_number = $request->input('phone_number');
        if (isset($phone_number)) {
            $query->where("phone_number", 'like', "%{$phone_number}%");
        }

        $password = $request->input('password');
        if (isset($password)) {
            $query->where("password", 'like', "%{$password}%");
        }

        $created_at = $request->input('created_at');
        if (isset($created_at) && count($created_at) == 2) {
            $query->whereBetween("created_at", [$created_at[0], $created_at[1]]);
        }

        $updated_at = $request->input('updated_at');
        if (isset($updated_at) && count($updated_at) == 2) {
            $query->whereBetween("updated_at", [$updated_at[0], $updated_at[1]]);
        }
        #pagination
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);
        $query->orderBy("id", "desc");
        $accounts = $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = $currentPage);
        #return
        return AccountResource::collection($accounts);
    }

    public function store(AccountRequest $request)
    {
        $fields = $request->all();
        $fields['password'] = str_random(8); #随机生成一个密码
        $account = Account::query()->create($fields);
        $account->wechatQrcode();
        return new AccountResource($account);
    }

    public function show($id)
    {
        $account = Account::findOrFail($id);
        $account->with("user");
        return new AccountResource($account);
    }

    public function update(AccountRequest $request, $id)
    {
        $fields = $request->all();
        #检查是否有重复的电话号码
        $exists = Account::query()
            ->where("id", "!=", $id)
            ->where("phone_number", $fields["phone_number"])
            ->exists();

        if (!$exists) {
            $account = Account::findOrFail($id);
            $account->update($fields);
            $account->wechatQrcode();
            return new AccountResource($account);
        } else {
            return response()->json(['error' => '保存失败，重复的手机号']);
        }
    }

    public function destroy($id)
    {
        Account::destroy($id);
        return new JsonResource(null);
    }

    /**
     * 下载二维码
     * @param $id
     * @return mixed
     */
    public function qrcode($id)
    {
        $account = Account::findOrFail($id);
        $account->wechatQrcode();
        return Storage::download("qrcode/{$account->id}.jpg", 'legal filename', [
            'Content-Disposition' => "attachment;filename={$account->real_name}{$account->city_code}-{$account->id}.jpg"
        ]);
    }

    public function tree()
    {
        $account = auth('admin')->user();
        $rootId = $account->id;
        if ($account->hasRole(['超级管理员', '管理员'])) {
            $rootId = 1;
        }
        $accounts = Account::query()
            ->where("id", $rootId)
            ->orderByDesc("id")
            ->with(['children' => function ($query) {
                $query->orderByDesc("id");
            }])
            ->get();
        return AccountResource::collection($accounts);
    }
}
