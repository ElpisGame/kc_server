<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\WechatHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Requests\WechatPaymentRequest;
use App\Http\Controllers\Admin\Resources\WechatPaymentResource;
use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use
    App\Models\WechatPayment;

class WechatPaymentController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:超级管理员|管理员']);
    }

    public function index(WechatPaymentRequest $request)
    {
        $query = WechatPayment::query();
        #search
        $id = $request->input('id');
        if (isset($id)) {
            $query->where("id", $id);
        }

        $app_id = $request->input('app_id');
        if (isset($app_id)) {
            $query->where("app_id", 'like', "%{$app_id}%");
        }

        $mch_id = $request->input('mch_id');
        if (isset($mch_id)) {
            $query->where("mch_id", 'like', "%{$mch_id}%");
        }

        $openid = $request->input('openid');
        if (isset($openid)) {
            $query->where("openid", 'like', "%{$openid}%");
        }

        $out_trade_no = $request->input('out_trade_no');
        if (isset($out_trade_no)) {
            $query->where("out_trade_no", 'like', "%{$out_trade_no}%");
        }

        $trade_type = $request->input('trade_type');
        if (isset($trade_type)) {
            $query->where("trade_type", 'like', "%{$trade_type}%");
        }

        $body = $request->input('body');
        if (isset($body)) {
            $query->where("body", 'like', "%{$body}%");
        }

        $total_fee = $request->input('total_fee');
        if (isset($total_fee)) {
            $query->where("total_fee", $total_fee);
        }

        $prepay_id = $request->input('prepay_id');
        if (isset($prepay_id)) {
            $query->where("prepay_id", 'like', "%{$prepay_id}%");
        }

        $transaction_id = $request->input('transaction_id');
        if (isset($transaction_id)) {
            $query->where("transaction_id", 'like', "%{$transaction_id}%");
        }

        $paid_at = $request->input('paid_at');
        if (isset($paid_at) && count($paid_at) == 2) {
            $query->whereBetween("paid_at", [$paid_at[0], $paid_at[1]]);
        }

        $status = $request->input('status');
        if (isset($status)) {
            $query->where("status", 'like', "%{$status}%");
        }

        $refund = $request->input('refund');
        if (isset($refund)) {
            $query->where("refund", $refund * 100);
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
        $query->with('user');
        $wechatpayments = $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = $currentPage);
        #return
        return WechatPaymentResource::collection($wechatpayments);
    }

    public function store(WechatPaymentRequest $request)
    {
        $fields = $request->all();
        $wechatpayment = WechatPayment::create($fields);
        return new WechatPaymentResource($wechatpayment);
    }

    public function show($id)
    {
        $wechatpayment = WechatPayment::findOrFail($id);
        return new WechatPaymentResource($wechatpayment);
    }

    public function update(WechatPaymentRequest $request, $id)
    {
        $wechatpayment = WechatPayment::findOrFail($id);
        $fields = $request->all();
        $wechatpayment->update($fields);
        return new WechatPaymentResource($wechatpayment);
    }

    public function destroy($id)
    {
        WechatPayment::destroy($id);
        return new JsonResource(null);
    }

    /**
     * 检查是否支付成功
     * @param $id
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function check($id)
    {
        $wechatPayment = WechatPayment::query()->findOrFail($id);
        return $wechatPayment->checkPayStatus();
    }
}
