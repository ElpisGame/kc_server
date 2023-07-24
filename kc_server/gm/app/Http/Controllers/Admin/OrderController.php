<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AccountHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Requests\OrderRequest;
use App\Http\Controllers\Admin\Resources\OrderResource;
use App\Models\Account;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(OrderRequest $request)
    {
        $query = Order::query();
        #search
        $id = $request->input('id');
        if (isset($id)) {
            if (strlen($id) == 20) {
                $query->where("barcode", $id);
            } else {
                $query->where("id", $id);
            }
        }

        $user_id = $request->input('user_id');
        if (isset($user_id)) {
            $query->where("user_id", $user_id);
        }

        $openid = $request->input('openid');
        if (isset($openid)) {
            $query->where("openid", 'like', "%{$openid}%");
        }

        $pet_type = $request->input('pet_type');
        if (isset($pet_type)) {
            $query->where("pet_type", 'like', "%{$pet_type}%");
        }

        $pet_brand = $request->input('pet_brand');
        if (isset($pet_brand)) {
            $query->where("pet_brand", 'like', "%{$pet_brand}%");
        }

        $pet_name = $request->input('pet_name');
        if (isset($pet_name)) {
            $query->where("pet_name", 'like', "%{$pet_name}%");
        }

        $pet_quantity = $request->input('pet_quantity');
        if (isset($pet_quantity)) {
            $query->where("pet_quantity", $pet_quantity);
        }

        $pet_weight = $request->input('pet_weight');
        if (isset($pet_weight)) {
            $query->where("pet_weight", $pet_weight);
        }

        $start_province = $request->input('start_province');
        if (isset($start_province)) {
            $query->where("start_province", 'like', "%{$start_province}%");
        }

        $start_city = $request->input('start_city');
        if (isset($start_city)) {
            $query->where("start_city", 'like', "%{$start_city}%");
        }

        $start_district = $request->input('start_district');
        if (isset($start_district)) {
            $query->where("start_district", 'like', "%{$start_district}%");
        }

        $start_address = $request->input('start_address');
        if (isset($start_address)) {
            $query->where("start_address", 'like', "%{$start_address}%");
        }

        $start_linkman = $request->input('start_linkman');
        if (isset($start_linkman)) {
            $query->where("start_linkman", 'like', "%{$start_linkman}%");
        }

        $start_linkphone = $request->input('start_linkphone');
        if (isset($start_linkphone)) {
            $query->where("start_linkphone", 'like', "%{$start_linkphone}%");
        }

        $start_lat = $request->input('start_lat');
        if (isset($start_lat)) {
            $query->where("start_lat", $start_lat);
        }

        $start_lng = $request->input('start_lng');
        if (isset($start_lng)) {
            $query->where("start_lng", $start_lng);
        }

        $dest_province = $request->input('dest_province');
        if (isset($dest_province)) {
            $query->where("dest_province", 'like', "%{$dest_province}%");
        }

        $dest_city = $request->input('dest_city');
        if (isset($dest_city)) {
            $query->where("dest_city", 'like', "%{$dest_city}%");
        }

        $dest_district = $request->input('dest_district');
        if (isset($dest_district)) {
            $query->where("dest_district", 'like', "%{$dest_district}%");
        }

        $dest_address = $request->input('dest_address');
        if (isset($dest_address)) {
            $query->where("dest_address", 'like', "%{$dest_address}%");
        }

        $dest_linkman = $request->input('dest_linkman');
        if (isset($dest_linkman)) {
            $query->where("dest_linkman", 'like', "%{$dest_linkman}%");
        }

        $dest_linkphone = $request->input('dest_linkphone');
        if (isset($dest_linkphone)) {
            $query->where("dest_linkphone", 'like', "%{$dest_linkphone}%");
        }

        $dest_lat = $request->input('dest_lat');
        if (isset($dest_lat)) {
            $query->where("dest_lat", $dest_lat);
        }

        $dest_lng = $request->input('dest_lng');
        if (isset($dest_lng)) {
            $query->where("dest_lng", $dest_lng);
        }

        $distance = $request->input('distance');
        if (isset($distance)) {
            $query->where("distance", $distance);
        }

        $service_cage = $request->input('service_cage');
        if (isset($service_cage)) {
            $query->where("service_cage", $service_cage);
        }

        $service_safe = $request->input('service_safe');
        if (isset($service_safe)) {
            $query->where("service_safe", $service_safe);
        }

        $service_s_home = $request->input('service_s_home');
        if (isset($service_s_home)) {
            $query->where("service_s_home", $service_s_home);
        }

        $service_d_home = $request->input('service_d_home');
        if (isset($service_d_home)) {
            $query->where("service_d_home", $service_d_home);
        }

        $service_healthy = $request->input('service_healthy');
        if (isset($service_healthy)) {
            $query->where("service_healthy", $service_healthy);
        }

        $service_favorite = $request->input('service_favorite');
        if (isset($service_favorite)) {
            $query->where("service_favorite", 'like', "%{$service_favorite}%");
        }

        $status = $request->input('status');
        if (isset($status)) {
            $query->where("status", $status);
        }

        $bid_user_id = $request->input('bid_user_id');
        if (isset($bid_user_id)) {
            $query->where("bid_user_id", $bid_user_id);
        }

        $created_at = $request->input('created_at');
        if (isset($created_at) && count($created_at) == 2) {
            $query->whereBetween("created_at", [$created_at[0], $created_at[1]]);
        }

        $updated_at = $request->input('updated_at');
        if (isset($updated_at) && count($updated_at) == 2) {
            $query->whereBetween("updated_at", [$updated_at[0], $updated_at[1]]);
        }

        #如果不是超级管理员只能查询代理本人和子机构的记录
        $account = auth('admin')->user();
        if (!$account->hasRole(['超级管理员', '管理员'])) {
            $treeAccounts = AccountHelper::treeAccounts($account->id);
            AccountHelper::flatAccounts($treeAccounts, $flatAccounts);
            $accountIds = collect($flatAccounts)->pluck("id");
            $query->whereIn("account_id", $accountIds);
        }

        #pagination
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);
        $query->orderBy("id", "desc");
        $query->with("user");
        $query->with("account");
        $query->with("bids");
        $orders = $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = $currentPage);
        #return
        return OrderResource::collection($orders);
    }


    public function store(OrderRequest $request)
    {
        $fields = $request->all();
        $order = Order::create($fields);
        return new OrderResource($order);
    }

    public function show($id)
    {
        $order = Order::query()
            ->with('account')
            ->with('user')
            ->with("bids")
            ->findOrFail($id);
        return new OrderResource($order);
    }

    public function update(OrderRequest $request, $id)
    {
        $order = Order::findOrFail($id);
        $fields = $request->all();
        $order->update($fields);
        return new OrderResource($order);
    }

    public function destroy($id)
    {
        Order::destroy($id);
        return new JsonResource(null);
    }
}
