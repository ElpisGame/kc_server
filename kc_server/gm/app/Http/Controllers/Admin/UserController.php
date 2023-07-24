<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Requests\UserRequest;
use App\Http\Controllers\Admin\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:超级管理员|管理员']);
    }

    public function index(UserRequest $request)
    {
        $query = User::query();
        #search
		$id = $request->input('id');
		if(isset($id)) {
			$query->where("id", $id);
		}
        $account_id = $request->input('account_id');
        if(isset($account_id)) {
            $query->where("account_id", $account_id);
        }

		$uuid = $request->input('uuid');
		if(isset($uuid)) {
			$query->where("uuid",'like', "%{$uuid}%");
		}

		$gh_id = $request->input('gh_id');
		if(isset($gh_id)) {
			$query->where("gh_id",'like', "%{$gh_id}%");
		}

		$openid = $request->input('openid');
		if(isset($openid)) {
			$query->where("openid",'like', "%{$openid}%");
		}

		$nickname = $request->input('nickname');
		if(isset($nickname)) {
			$query->where("nickname",'like', "%{$nickname}%");
		}

		$headimgurl = $request->input('headimgurl');
		if(isset($headimgurl)) {
			$query->where("headimgurl",'like', "%{$headimgurl}%");
		}

		$language = $request->input('language');
		if(isset($language)) {
			$query->where("language",'like', "%{$language}%");
		}

		$sex = $request->input('sex');
		if(isset($sex)) {
			$query->where("sex", $sex);
		}

		$country = $request->input('country');
		if(isset($country)) {
			$query->where("country",'like', "%{$country}%");
		}

		$province = $request->input('province');
		if(isset($province)) {
			$query->where("province",'like', "%{$province}%");
		}

		$city = $request->input('city');
		if(isset($city)) {
			$query->where("city",'like', "%{$city}%");
		}

		$district = $request->input('district');
		if(isset($district)) {
			$query->where("district",'like', "%{$district}%");
		}

		$phone = $request->input('phone');
		if(isset($phone)) {
			$query->where("phone",'like', "%{$phone}%");
		}

		$is_merchant = $request->input('is_merchant');
		if(isset($is_merchant)) {
			$query->where("is_merchant", $is_merchant);
		}

		$company = $request->input('company');
		if(isset($company)) {
			$query->where("company",'like', "%{$company}%");
		}

		$name = $request->input('name');
		if(isset($name)) {
			$query->where("name",'like', "%{$name}%");
		}

		$email = $request->input('email');
		if(isset($email)) {
			$query->where("email",'like', "%{$email}%");
		}

		$email_verified_at = $request->input('email_verified_at');
		if(isset($email_verified_at) && count($email_verified_at)==2) {
			$query->whereBetween("email_verified_at", [$email_verified_at[0], $email_verified_at[1]]);
		}

		$password = $request->input('password');
		if(isset($password)) {
			$query->where("password",'like', "%{$password}%");
		}

		$remember_token = $request->input('remember_token');
		if(isset($remember_token)) {
			$query->where("remember_token",'like', "%{$remember_token}%");
		}

		$lng = $request->input('lng');
		if(isset($lng)) {
			$query->where("lng", $lng);
		}

		$lat = $request->input('lat');
		if(isset($lat)) {
			$query->where("lat", $lat);
		}

		$qrscene = $request->input('qrscene');
		if(isset($qrscene)) {
			$query->where("qrscene",'like', "%{$qrscene}%");
		}

		$unsubscribe = $request->input('unsubscribe');
		if(isset($unsubscribe)) {
			$query->where("unsubscribe", $unsubscribe);
		}

		$created_at = $request->input('created_at');
		if(isset($created_at) && count($created_at)==2) {
			$query->whereBetween("created_at", [$created_at[0], $created_at[1]]);
		}

		$updated_at = $request->input('updated_at');
		if(isset($updated_at) && count($updated_at)==2) {
			$query->whereBetween("updated_at", [$updated_at[0], $updated_at[1]]);
		}


        #pagination
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);
        $query->orderBy("id","desc");
        $users = $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = $currentPage);
        #return
        return UserResource::collection($users);
    }

    public function store(UserRequest $request)
    {
        $fields = $request->all();
        $user = User::create($fields);
        return new UserResource($user);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user);
    }

    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $fields = $request->all();
        $user->update($fields);
        return new UserResource($user);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return new JsonResource(null);
    }
}
