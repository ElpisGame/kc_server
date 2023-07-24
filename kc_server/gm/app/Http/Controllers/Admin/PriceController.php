<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AccountHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Requests\PriceRequest;
use App\Http\Controllers\Admin\Resources\PriceResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Price;
use Illuminate\Support\Facades\DB;

class PriceController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:超级管理员|管理员']);
    }

    public function index(PriceRequest $request)
    {
        $query = Price::query();
        #search
        $id = $request->input('id');
        if (isset($id)) {
            $query->where("id", $id);
        }

        $startCityCode = $request->input('startCityCode');
        if (isset($startCityCode)) {
            $query->where("startCityCode", $startCityCode);
        }

        $startCityName = $request->input('startCityName');
        if (isset($startCityName)) {
            $query->where("startCityName", 'like', "%{$startCityName}%");
        }

        $destCityCode = $request->input('destCityCode');
        if (isset($destCityCode)) {
            $query->where("destCityCode", $destCityCode);
        }

        $destCityName = $request->input('destCityName');
        if (isset($destCityName)) {
            $query->where("destCityName", 'like', "%{$destCityName}%");
        }

        $startWeight = $request->input('startWeight');
        if (isset($startWeight)) {
            $query->where("startWeight", $startWeight);
        }

        $startPrice = $request->input('startPrice');
        if (isset($startPrice)) {
            $query->where("startPrice", $startPrice);
        }

        $nextPrice = $request->input('nextPrice');
        if (isset($nextPrice)) {
            $query->where("nextPrice", $nextPrice);
        }

        $vehicle = $request->input('vehicle');
        if (isset($vehicle)) {
            $query->where("vehicle", $vehicle);
        }

        $comment = $request->input('comment');
        if (isset($comment)) {
            $query->where("comment", 'like', "%{$comment}%");
        }

        #如果不是超级管理员只能查询代理本人和子机构的记录
//        $account = auth('admin')->user();
//        if (!$account->hasRole(['超级管理员', '管理员'])) {
//            $treeAccounts = AccountHelper::treeAccounts($account->id);
//            AccountHelper::flatAccounts($treeAccounts, $flatAccounts);
//            $accountIds = collect($flatAccounts)->pluck("id");
//            $query->whereIn("account_id", $accountIds);
//        }

        #pagination
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);
        $query->orderBy("updated_at", "desc");
        $prices = $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = $currentPage);
        #return
        return PriceResource::collection($prices);
    }

    public function store(PriceRequest $request)
    {
        $fields = $request->all();
        $fields['account_id'] = auth('admin')->id();

        $price = Price::query()
            ->where("startCityCode", $fields['startCityCode'])
            ->where("destCityCode", $fields['destCityCode'])
            ->where("vehicle", $fields['vehicle'])
            ->first();
        if ($price) {
            $price->update($fields);
        } else {
            $price = Price::create($fields);
        }
        return new PriceResource($price);
    }

    public function show($id)
    {
        $price = Price::findOrFail($id);
        return new PriceResource($price);
    }

    public function update(PriceRequest $request, $id)
    {
        $price = Price::findOrFail($id);
        $fields = $request->all();
        $fields['account_id'] = auth('admin')->id();
        $price->update($fields);
        return new PriceResource($price);
    }

    public function destroy($id)
    {
        Price::destroy($id);
        return new JsonResource(null);
    }
}
