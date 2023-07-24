<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AccountHelper;
use App\Helpers\WechatHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Requests\BidRequest;
use App\Http\Controllers\Admin\Resources\BidResource;
use App\Models\WechatPayment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Bid;

class BidController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:超级管理员|管理员']);
    }

    public function index(BidRequest $request)
    {
        $query = Bid::query();
        #search
        $id = $request->input('id');
        if (isset($id)) {
            $query->where("id", $id);
        }

        $order_id = $request->input('order_id');
        if (isset($order_id)) {
            $query->where("order_id", $order_id);
        }

        $user_id = $request->input('user_id');
        if (isset($user_id)) {
            $query->where("user_id", $user_id);
        }

        $title = $request->input('title');
        if (isset($title)) {
            $query->where("title", 'like', "%{$title}%");
        }

        $price = $request->input('price');
        if (isset($price)) {
            $query->where("price", $price);
        }

        $description = $request->input('description');
        if (isset($description)) {
            $query->where("description", 'like', "%{$description}%");
        }

        $trade_no = $request->input('trade_no');
        if (isset($trade_no)) {
            $query->where("trade_no", 'like', "%{$trade_no}%");
        }

        $openid = $request->input('openid');
        if (isset($openid)) {
            $query->where("openid", 'like', "%{$openid}%");
        }

        $nickname = $request->input('nickname');
        if (isset($nickname)) {
            $query->where("nickname", 'like', "%{$nickname}%");
        }

        $company = $request->input('company');
        if (isset($company)) {
            $query->where("company", 'like', "%{$company}%");
        }

        $headimgurl = $request->input('headimgurl');
        if (isset($headimgurl)) {
            $query->where("headimgurl", 'like', "%{$headimgurl}%");
        }

        $status = $request->input('status');
        if (isset($status)) {
            $query->where("status", $status);
        }

        $paid_at = $request->input('paid_at');
        if (isset($paid_at) && count($paid_at) == 2) {
            $query->whereBetween("paid_at", [$paid_at[0], $paid_at[1]]);
        }

        $paid_status = $request->input('paid_status');
        if (isset($paid_status)) {
            $query->where("paid_status", 'like', "%{$paid_status}%");
        }

        $refund = $request->input('refund');
        if (isset($refund)) {
            $query->where("refund", $refund);
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
        $query->with("order");
        $bids = $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = $currentPage);
        #return
        return BidResource::collection($bids);
    }

    public function store(BidRequest $request)
    {
        $fields = $request->all();
        $fields['trade_no'] = WechatHelper::generateTradeNo(); //商户订单号不能重复哦
        $bid = Bid::create($fields);
        return new BidResource($bid);
    }

    public function show($id)
    {
        $bid = Bid::findOrFail($id);
        return new BidResource($bid);
    }

    public function update(BidRequest $request, $id)
    {
        $bid = Bid::findOrFail($id);
        $fields = $request->all();
        $bid->update($fields);
        return new BidResource($bid);
    }

    public function destroy($id)
    {
        Bid::destroy($id);
        return new JsonResource(null);
    }

    /**
     * 退款
     * @param Request $request
     * @return \App\Models\WechatRefund
     * @throws \Exception
     */
    public function refund(Request $request)
    {
        $id = $request->input("id");
        $refundFee = $request->input("refundFee");

        $account = auth('admin')->user();

        if ($account->hasRole(['超级管理员', '管理员'])) {
            $bid = Bid::query()->findOrFail($id);
            $payment = WechatPayment::query()->where("out_trade_no", $bid->trade_no)->first();
            if ($bid && $payment) {
                $wechatRefund = WechatHelper::refundPayment($payment, $refundFee * 100, "退款说明");
                #if($payment) 检查是否退款成功
                if ($wechatRefund['result_code'] == 'SUCCESS') {
                    $bid->refund = $bid->refund + $refundFee;
                    $bid->save();
                }
                //response
                return $wechatRefund;
            }
        } else {
            throw new \Exception("This Bid is not yours");
        }
    }
}
