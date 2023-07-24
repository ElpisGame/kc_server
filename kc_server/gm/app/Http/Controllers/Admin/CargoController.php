<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Requests\CargoRequest;
use App\Http\Controllers\Admin\Resources\CargoResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Cargo;

class CargoController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:超级管理员|管理员']);
    }

    public function index(CargoRequest $request)
    {
        $query = Cargo::query();
        #search
		$id = $request->input('id');
		if(isset($id)) {
			$query->where("id", $id);
		}

		$user_id = $request->input('user_id');
		if(isset($user_id)) {
			$query->where("user_id", $user_id);
		}

		$schedule_id = $request->input('schedule_id');
		if(isset($schedule_id)) {
			$query->where("schedule_id", $schedule_id);
		}

		$pet_type = $request->input('pet_type');
		if(isset($pet_type)) {
			$query->where("pet_type",'like', "%{$pet_type}%");
		}

		$pet_quantity = $request->input('pet_quantity');
		if(isset($pet_quantity)) {
			$query->where("pet_quantity", $pet_quantity);
		}

		$pet_comment = $request->input('pet_comment');
		if(isset($pet_comment)) {
			$query->where("pet_comment",'like', "%{$pet_comment}%");
		}

		$start_province = $request->input('start_province');
		if(isset($start_province)) {
			$query->where("start_province",'like', "%{$start_province}%");
		}

		$start_city = $request->input('start_city');
		if(isset($start_city)) {
			$query->where("start_city",'like', "%{$start_city}%");
		}

		$start_district = $request->input('start_district');
		if(isset($start_district)) {
			$query->where("start_district",'like', "%{$start_district}%");
		}

		$start_address = $request->input('start_address');
		if(isset($start_address)) {
			$query->where("start_address",'like', "%{$start_address}%");
		}

		$start_linkman = $request->input('start_linkman');
		if(isset($start_linkman)) {
			$query->where("start_linkman",'like', "%{$start_linkman}%");
		}

		$start_linkphone = $request->input('start_linkphone');
		if(isset($start_linkphone)) {
			$query->where("start_linkphone",'like', "%{$start_linkphone}%");
		}

		$start_lat = $request->input('start_lat');
		if(isset($start_lat)) {
			$query->where("start_lat", $start_lat);
		}

		$start_lng = $request->input('start_lng');
		if(isset($start_lng)) {
			$query->where("start_lng", $start_lng);
		}

		$dest_province = $request->input('dest_province');
		if(isset($dest_province)) {
			$query->where("dest_province",'like', "%{$dest_province}%");
		}

		$dest_city = $request->input('dest_city');
		if(isset($dest_city)) {
			$query->where("dest_city",'like', "%{$dest_city}%");
		}

		$dest_district = $request->input('dest_district');
		if(isset($dest_district)) {
			$query->where("dest_district",'like', "%{$dest_district}%");
		}

		$dest_address = $request->input('dest_address');
		if(isset($dest_address)) {
			$query->where("dest_address",'like', "%{$dest_address}%");
		}

		$dest_linkman = $request->input('dest_linkman');
		if(isset($dest_linkman)) {
			$query->where("dest_linkman",'like', "%{$dest_linkman}%");
		}

		$dest_linkphone = $request->input('dest_linkphone');
		if(isset($dest_linkphone)) {
			$query->where("dest_linkphone",'like', "%{$dest_linkphone}%");
		}

		$dest_lat = $request->input('dest_lat');
		if(isset($dest_lat)) {
			$query->where("dest_lat", $dest_lat);
		}

		$dest_lng = $request->input('dest_lng');
		if(isset($dest_lng)) {
			$query->where("dest_lng", $dest_lng);
		}

		$distance = $request->input('distance');
		if(isset($distance)) {
			$query->where("distance", $distance);
		}

		$status = $request->input('status');
		if(isset($status)) {
			$query->where("status", $status);
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
        $cargos = $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = $currentPage);
        #return
        return CargoResource::collection($cargos);
    }

    public function store(CargoRequest $request)
    {
        $fields = $request->all();
        $cargo = Cargo::create($fields);
        return new CargoResource($cargo);
    }

    public function show($id)
    {
        $cargo = Cargo::findOrFail($id);
        return new CargoResource($cargo);
    }

    public function update(CargoRequest $request, $id)
    {
        $cargo = Cargo::findOrFail($id);
        $fields = $request->all();
        $cargo->update($fields);
        return new CargoResource($cargo);
    }

    public function destroy($id)
    {
        Cargo::destroy($id);
        return new JsonResource(null);
    }
}
