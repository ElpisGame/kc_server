<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Requests\CargoScheduleRequest;
use App\Http\Controllers\Admin\Resources\CargoScheduleResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\CargoSchedule;

class CargoScheduleController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:超级管理员|管理员']);
    }

    public function index(CargoScheduleRequest $request)
    {
        $query = CargoSchedule::query();
        #search
		$id = $request->input('id');
		if(isset($id)) {
			$query->where("id", $id);
		}

		$user_id = $request->input('user_id');
		if(isset($user_id)) {
			$query->where("user_id", $user_id);
		}

		$name = $request->input('name');
		if(isset($name)) {
			$query->where("name",'like', "%{$name}%");
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
        $cargoschedules = $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = $currentPage);
        #return
        return CargoScheduleResource::collection($cargoschedules);
    }

    public function store(CargoScheduleRequest $request)
    {
        $fields = $request->all();
        $cargoschedule = CargoSchedule::create($fields);
        return new CargoScheduleResource($cargoschedule);
    }

    public function show($id)
    {
        $cargoschedule = CargoSchedule::findOrFail($id);
        return new CargoScheduleResource($cargoschedule);
    }

    public function update(CargoScheduleRequest $request, $id)
    {
        $cargoschedule = CargoSchedule::findOrFail($id);
        $fields = $request->all();
        $cargoschedule->update($fields);
        return new CargoScheduleResource($cargoschedule);
    }

    public function destroy($id)
    {
        CargoSchedule::destroy($id);
        return new JsonResource(null);
    }
}
