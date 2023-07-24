<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Requests\PolicyRequest;
use App\Http\Controllers\Admin\Resources\PolicyResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Policy;

class PolicyController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:超级管理员|管理员']);
    }

    public function index(PolicyRequest $request)
    {
        $query = Policy::query();
        #search
		$id = $request->input('id');
		if(isset($id)) {
			$query->where("id", $id);
		}

		$order_id = $request->input('order_id');
		if(isset($order_id)) {
			$query->where("order_id",'like', "%{$order_id}%");
		}

		$account_id = $request->input('account_id');
		if(isset($account_id)) {
			$query->where("account_id", $account_id);
		}

		$user_id = $request->input('user_id');
		if(isset($user_id)) {
			$query->where("user_id", $user_id);
		}

		$source = $request->input('source');
		if(isset($source)) {
			$query->where("source",'like', "%{$source}%");
		}

		$requestUrl = $request->input('requestUrl');
		if(isset($requestUrl)) {
			$query->where("requestUrl",'like', "%{$requestUrl}%");
		}

		$requestBody = $request->input('requestBody');
		if(isset($requestBody)) {
			$query->where("requestBody", $requestBody);
		}

		$response = $request->input('response');
		if(isset($response)) {
			$query->where("response", $response);
		}

		$policyNo = $request->input('policyNo');
		if(isset($policyNo)) {
			$query->where("policyNo",'like', "%{$policyNo}%");
		}

		$ePolicy = $request->input('ePolicy');
		if(isset($ePolicy)) {
			$query->where("ePolicy", $ePolicy);
		}

		$eInvoice = $request->input('eInvoice');
		if(isset($eInvoice)) {
			$query->where("eInvoice", $eInvoice);
		}

		$isDeleted = $request->input('isDeleted');
		if(isset($isDeleted)) {
			$query->where("isDeleted", $isDeleted);
		}

		$status = $request->input('status');
		if(isset($status)) {
			$query->where("status", $status);
		}

		$settle_status = $request->input('settle_status');
		if(isset($settle_status)) {
			$query->where("settle_status", $settle_status);
		}

		$updated_at = $request->input('updated_at');
		if(isset($updated_at) && count($updated_at)==2) {
			$query->whereBetween("updated_at", [$updated_at[0], $updated_at[1]]);
		}

		$created_at = $request->input('created_at');
		if(isset($created_at) && count($created_at)==2) {
			$query->whereBetween("created_at", [$created_at[0], $created_at[1]]);
		}

		$deleted_at = $request->input('deleted_at');
		if(isset($deleted_at) && count($deleted_at)==2) {
			$query->whereBetween("deleted_at", [$deleted_at[0], $deleted_at[1]]);
		}


        #pagination
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);
        $query->orderBy("id","desc");
        $policies = $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = $currentPage);
        #return
        return PolicyResource::collection($policies);
    }

    public function store(PolicyRequest $request)
    {
        $fields = $request->all();
        $policy = Policy::create($fields);
        return new PolicyResource($policy);
    }

    public function show($id)
    {
        $policy = Policy::findOrFail($id);
        return new PolicyResource($policy);
    }

    public function update(PolicyRequest $request, $id)
    {
        $policy = Policy::findOrFail($id);
        $fields = $request->all();
        $policy->update($fields);
        return new PolicyResource($policy);
    }

    public function destroy($id)
    {
        Policy::destroy($id);
        return new JsonResource(null);
    }
}
