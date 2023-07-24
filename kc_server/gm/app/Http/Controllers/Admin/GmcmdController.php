<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Requests\gmcmdRequest;
use App\Http\Controllers\Admin\Resources\gmcmdResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Gmcmd;

class GmcmdController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:超级管理员|管理员']);
    }

    public function index(gmcmdRequest $request)
    {
        $query = Gmcmd::query();
        #search

        #pagination
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);
        $query->orderBy("id","desc");
        $gmcmds = $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = $currentPage);
        #return
        return gmcmdResource::collection($gmcmds);
    }

    public function store(gmcmdRequest $request)
    {
        $fields = $request->all();
        $gmcmd = Gmcmd::create($fields);
        return new gmcmdResource($gmcmd);
    }

    public function show($id)
    {
        $gmcmd = Gmcmd::findOrFail($id);
        return new gmcmdResource($gmcmd);
    }

    public function update(gmcmdRequest $request, $id)
    {
        $gmcmd = Gmcmd::findOrFail($id);
        $fields = $request->all();
        $gmcmd->update($fields);
        return new gmcmdResource($gmcmd);
    }

    public function destroy($id)
    {
        Gmcmd::destroy($id);
        return new JsonResource(null);
    }
}
