<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Requests\MailRequest;
use App\Http\Controllers\Admin\Resources\MailResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Mail;

class MailController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:超级管理员|管理员']);
    }

    public function index(MailRequest $request)
    {
        $query = Mail::query();
        #search
		$dbid = $request->input('dbid');
		if(isset($dbid)) {
			$query->where("dbid", $dbid);
		}

		$playerid = $request->input('playerid');
		if(isset($playerid)) {
			$query->where("playerid", $playerid);
		}

		$readstatus = $request->input('readstatus');
		if(isset($readstatus)) {
			$query->where("readstatus", $readstatus);
		}

		$sendtime = $request->input('sendtime');
		if(isset($sendtime)) {
			$query->where("sendtime", $sendtime);
		}

		$head = $request->input('head');
		if(isset($head)) {
			$query->where("head",'like', "%{$head}%");
		}

		$context = $request->input('context');
		if(isset($context)) {
			$query->where("context",'like', "%{$context}%");
		}

		$award = $request->input('award');
		if(isset($award)) {
			$query->where("award", $award);
		}

		$awardstatus = $request->input('awardstatus');
		if(isset($awardstatus)) {
			$query->where("awardstatus", $awardstatus);
		}

		$log_type = $request->input('log_type');
		if(isset($log_type)) {
			$query->where("log_type", $log_type);
		}

		$log = $request->input('log');
		if(isset($log)) {
			$query->where("log",'like', "%{$log}%");
		}


        #pagination
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);
        $query->orderBy("dbid","desc");
        $mails = $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = $currentPage);
        #return
        return MailResource::collection($mails);
    }

    public function store(MailRequest $request)
    {
        $fields = $request->all();
        $mail = Mail::create($fields);
        return new MailResource($mail);
    }

    public function show($id)
    {
        $mail = Mail::findOrFail($id);
        return new MailResource($mail);
    }

    public function update(MailRequest $request, $id)
    {
        $mail = Mail::findOrFail($id);
        $fields = $request->all();
        $mail->update($fields);
        return new MailResource($mail);
    }

    public function destroy($id)
    {
        Mail::destroy($id);
        return new JsonResource(null);
    }
}
