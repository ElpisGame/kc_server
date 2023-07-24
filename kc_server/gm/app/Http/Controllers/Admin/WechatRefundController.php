<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Requests\WechatRefundRequest;
use App\Http\Controllers\Admin\Resources\WechatRefundResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\WechatRefund;

class WechatRefundController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:超级管理员|管理员']);
    }

    public function index(WechatRefundRequest $request)
    {
        $query = WechatRefund::query();
        #search
		$id = $request->input('id');
		if(isset($id)) {
			$query->where("id", $id);
		}

		$app_id = $request->input('app_id');
		if(isset($app_id)) {
			$query->where("app_id",'like', "%{$app_id}%");
		}

		$mch_id = $request->input('mch_id');
		if(isset($mch_id)) {
			$query->where("mch_id",'like', "%{$mch_id}%");
		}

		$transaction_id = $request->input('transaction_id');
		if(isset($transaction_id)) {
			$query->where("transaction_id",'like', "%{$transaction_id}%");
		}

		$out_trade_no = $request->input('out_trade_no');
		if(isset($out_trade_no)) {
			$query->where("out_trade_no",'like', "%{$out_trade_no}%");
		}

		$out_refund_no = $request->input('out_refund_no');
		if(isset($out_refund_no)) {
			$query->where("out_refund_no",'like', "%{$out_refund_no}%");
		}

		$refund_id = $request->input('refund_id');
		if(isset($refund_id)) {
			$query->where("refund_id",'like', "%{$refund_id}%");
		}

		$refund_fee = $request->input('refund_fee');
		if(isset($refund_fee)) {
			$query->where("refund_fee", $refund_fee);
		}

		$coupon_refund_fee = $request->input('coupon_refund_fee');
		if(isset($coupon_refund_fee)) {
			$query->where("coupon_refund_fee", $coupon_refund_fee);
		}

		$total_fee = $request->input('total_fee');
		if(isset($total_fee)) {
			$query->where("total_fee", $total_fee);
		}

		$cash_fee = $request->input('cash_fee');
		if(isset($cash_fee)) {
			$query->where("cash_fee", $cash_fee);
		}

		$coupon_refund_count = $request->input('coupon_refund_count');
		if(isset($coupon_refund_count)) {
			$query->where("coupon_refund_count", $coupon_refund_count);
		}

		$cash_refund_fee = $request->input('cash_refund_fee');
		if(isset($cash_refund_fee)) {
			$query->where("cash_refund_fee", $cash_refund_fee);
		}

		$result_code = $request->input('result_code');
		if(isset($result_code)) {
			$query->where("result_code",'like', "%{$result_code}%");
		}

		$err_code = $request->input('err_code');
		if(isset($err_code)) {
			$query->where("err_code",'like', "%{$err_code}%");
		}

		$err_code_des = $request->input('err_code_des');
		if(isset($err_code_des)) {
			$query->where("err_code_des",'like', "%{$err_code_des}%");
		}

		$created_at = $request->input('created_at');
		if(isset($created_at) && count($created_at)==2) {
			$query->whereBetween("created_at", [$created_at[0], $created_at[1]]);
		}

		$updated_at = $request->input('updated_at');
		if(isset($updated_at) && count($updated_at)==2) {
			$query->whereBetween("updated_at", [$updated_at[0], $updated_at[1]]);
		}

		$deleted_at = $request->input('deleted_at');
		if(isset($deleted_at) && count($deleted_at)==2) {
			$query->whereBetween("deleted_at", [$deleted_at[0], $deleted_at[1]]);
		}


        #pagination
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);
        $query->orderBy("id","desc");
        $wechatrefunds = $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = $currentPage);
        #return
        return WechatRefundResource::collection($wechatrefunds);
    }

    public function store(WechatRefundRequest $request)
    {
        $fields = $request->all();
        $wechatrefund = WechatRefund::create($fields);
        return new WechatRefundResource($wechatrefund);
    }

    public function show($id)
    {
        $wechatrefund = WechatRefund::findOrFail($id);
        return new WechatRefundResource($wechatrefund);
    }

    public function update(WechatRefundRequest $request, $id)
    {
        $wechatrefund = WechatRefund::findOrFail($id);
        $fields = $request->all();
        $wechatrefund->update($fields);
        return new WechatRefundResource($wechatrefund);
    }

    public function destroy($id)
    {
        WechatRefund::destroy($id);
        return new JsonResource(null);
    }
}
