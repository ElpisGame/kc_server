<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Requests\PlayerRequest;
use App\Http\Controllers\Admin\Resources\PlayerResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Player;

class PlayerController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:超级管理员|管理员']);
    }

    public function index(PlayerRequest $request)
    {
        $query = Player::query();
        #search
        $dbid = $request->input('dbid');
        if (isset($dbid)) {
            $query->where("dbid", $dbid);
        }

        $account = $request->input('account');
        if (isset($account)) {
            $query->where("account", 'like', "%{$account}%");
        }

        $serverid = $request->input('serverid');
        if (isset($serverid)) {
            $query->where("serverid", $serverid);
        }

        $name = $request->input('name');
        if (isset($name)) {
            $query->where("name", 'like', "%{$name}%");
        }

        $sealed = $request->input('sealed');
        if (isset($sealed)) {
            $query->where("sealed", $sealed);
        }

        $silent = $request->input('silent');
        if (isset($silent)) {
            $query->where("silent", $silent);
        }

        $createtime = $request->input('createtime');
        if (isset($createtime)) {
            $query->where("createtime", $createtime);
        }

        $lastonlinetime = $request->input('lastonlinetime');
        if (isset($lastonlinetime)) {
            $query->where("lastonlinetime", $lastonlinetime);
        }

        $createip = $request->input('createip');
        if (isset($createip)) {
            $query->where("createip", 'like', "%{$createip}%");
        }

        $lastloginip = $request->input('lastloginip');
        if (isset($lastloginip)) {
            $query->where("lastloginip", 'like', "%{$lastloginip}%");
        }

        $job = $request->input('job');
        if (isset($job)) {
            $query->where("job", $job);
        }

        $sex = $request->input('sex');
        if (isset($sex)) {
            $query->where("sex", $sex);
        }

        $level = $request->input('level');
        if (isset($level)) {
            $query->where("level", $level);
        }

        $totalpower = $request->input('totalpower');
        if (isset($totalpower)) {
            $query->where("totalpower", $totalpower);
        }

        $exp = $request->input('exp');
        if (isset($exp)) {
            $query->where("exp", $exp);
        }

        $gold = $request->input('gold');
        if (isset($gold)) {
            $query->where("gold", $gold);
        }

        $yuanbao = $request->input('yuanbao');
        if (isset($yuanbao)) {
            $query->where("yuanbao", $yuanbao);
        }

        $byb = $request->input('byb');
        if (isset($byb)) {
            $query->where("byb", $byb);
        }

        $recharge = $request->input('recharge');
        if (isset($recharge)) {
            $query->where("recharge", $recharge);
        }

        $rechargenotice = $request->input('rechargenotice');
        if (isset($rechargenotice)) {
            $query->where("rechargenotice", $rechargenotice);
        }

        $recharge_maxone = $request->input('recharge_maxone');
        if (isset($recharge_maxone)) {
            $query->where("recharge_maxone", $recharge_maxone);
        }

        $recharge_lasttime = $request->input('recharge_lasttime');
        if (isset($recharge_lasttime)) {
            $query->where("recharge_lasttime", $recharge_lasttime);
        }

        $vip = $request->input('vip');
        if (isset($vip)) {
            $query->where("vip", $vip);
        }

        $vipstate = $request->input('vipstate');
        if (isset($vipstate)) {
            $query->where("vipstate", $vipstate);
        }

        $vipaddedreward = $request->input('vipaddedreward');
        if (isset($vipaddedreward)) {
            $query->where("vipaddedreward", $vipaddedreward);
        }

        $bagnum = $request->input('bagnum');
        if (isset($bagnum)) {
            $query->where("bagnum", $bagnum);
        }

        $guildid = $request->input('guildid');
        if (isset($guildid)) {
            $query->where("guildid", $guildid);
        }

        $contrib = $request->input('contrib');
        if (isset($contrib)) {
            $query->where("contrib", $contrib);
        }

        $clientvalue = $request->input('clientvalue');
        if (isset($clientvalue)) {
            $query->where("clientvalue", $clientvalue);
        }

        $clientvaluelist = $request->input('clientvaluelist');
        if (isset($clientvaluelist)) {
            $query->where("clientvaluelist", $clientvaluelist);
        }

        $global_mails = $request->input('global_mails');
        if (isset($global_mails)) {
            $query->where("global_mails", $global_mails);
        }

        $exchange_data = $request->input('exchange_data');
        if (isset($exchange_data)) {
            $query->where("exchange_data", $exchange_data);
        }

        $escort_data = $request->input('escort_data');
        if (isset($escort_data)) {
            $query->where("escort_data", $escort_data);
        }

        $friend_data = $request->input('friend_data');
        if (isset($friend_data)) {
            $query->where("friend_data", $friend_data);
        }

        $welcome = $request->input('welcome');
        if (isset($welcome)) {
            $query->where("welcome", $welcome);
        }

        $openfuncstate = $request->input('openfuncstate');
        if (isset($openfuncstate)) {
            $query->where("openfuncstate", $openfuncstate);
        }

        $rankworship = $request->input('rankworship');
        if (isset($rankworship)) {
            $query->where("rankworship", $rankworship);
        }

        $renamecount = $request->input('renamecount');
        if (isset($renamecount)) {
            $query->where("renamecount", $renamecount);
        }

        $head = $request->input('head');
        if (isset($head)) {
            $query->where("head", $head);
        }

        $marry = $request->input('marry');
        if (isset($marry)) {
            $query->where("marry", $marry);
        }

        $chapter = $request->input('chapter');
        if (isset($chapter)) {
            $query->where("chapter", $chapter);
        }

        $task = $request->input('task');
        if (isset($task)) {
            $query->where("task", $task);
        }

        $catchpet = $request->input('catchpet');
        if (isset($catchpet)) {
            $query->where("catchpet", $catchpet);
        }

        $material = $request->input('material');
        if (isset($material)) {
            $query->where("material", $material);
        }

        $treasuremap = $request->input('treasuremap');
        if (isset($treasuremap)) {
            $query->where("treasuremap", $treasuremap);
        }

        $wildgeeseFb = $request->input('wildgeeseFb');
        if (isset($wildgeeseFb)) {
            $query->where("wildgeeseFb", $wildgeeseFb);
        }

        $heavenFb = $request->input('heavenFb');
        if (isset($heavenFb)) {
            $query->where("heavenFb", $heavenFb);
        }

        $publicboss = $request->input('publicboss');
        if (isset($publicboss)) {
            $query->where("publicboss", $publicboss);
        }

        $vipboss = $request->input('vipboss');
        if (isset($vipboss)) {
            $query->where("vipboss", $vipboss);
        }

        $arena = $request->input('arena');
        if (isset($arena)) {
            $query->where("arena", $arena);
        }

        $crossTeamFb = $request->input('crossTeamFb');
        if (isset($crossTeamFb)) {
            $query->where("crossTeamFb", $crossTeamFb);
        }

        $eightyOneHard = $request->input('eightyOneHard');
        if (isset($eightyOneHard)) {
            $query->where("eightyOneHard", $eightyOneHard);
        }

        $totalloginday = $request->input('totalloginday');
        if (isset($totalloginday)) {
            $query->where("totalloginday", $totalloginday);
        }

        $lastloginday = $request->input('lastloginday');
        if (isset($lastloginday)) {
            $query->where("lastloginday", $lastloginday);
        }

        $xianlv = $request->input('xianlv');
        if (isset($xianlv)) {
            $query->where("xianlv", $xianlv);
        }

        $pet = $request->input('pet');
        if (isset($pet)) {
            $query->where("pet", $pet);
        }

        $tiannv = $request->input('tiannv');
        if (isset($tiannv)) {
            $query->where("tiannv", $tiannv);
        }

        $tianshen = $request->input('tianshen');
        if (isset($tianshen)) {
            $query->where("tianshen", $tianshen);
        }

        $formation = $request->input('formation');
        if (isset($formation)) {
            $query->where("formation", $formation);
        }

        $baby = $request->input('baby');
        if (isset($baby)) {
            $query->where("baby", $baby);
        }

        $daily_task = $request->input('daily_task');
        if (isset($daily_task)) {
            $query->where("daily_task", $daily_task);
        }

        $welfare = $request->input('welfare');
        if (isset($welfare)) {
            $query->where("welfare", $welfare);
        }

        $Advanced = $request->input('Advanced');
        if (isset($Advanced)) {
            $query->where("Advanced", $Advanced);
        }

        $answer = $request->input('answer');
        if (isset($answer)) {
            $query->where("answer", $answer);
        }

        $shop = $request->input('shop');
        if (isset($shop)) {
            $query->where("shop", $shop);
        }

        $guild_data = $request->input('guild_data');
        if (isset($guild_data)) {
            $query->where("guild_data", $guild_data);
        }

        $brother = $request->input('brother');
        if (isset($brother)) {
            $query->where("brother", $brother);
        }

        $welfare_data = $request->input('welfare_data');
        if (isset($welfare_data)) {
            $query->where("welfare_data", $welfare_data);
        }

        $recharger_data = $request->input('recharger_data');
        if (isset($recharger_data)) {
            $query->where("recharger_data", $recharger_data);
        }

        $activity_record = $request->input('activity_record');
        if (isset($activity_record)) {
            $query->where("activity_record", $activity_record);
        }

        $luck = $request->input('luck');
        if (isset($luck)) {
            $query->where("luck", $luck);
        }

        $auction = $request->input('auction');
        if (isset($auction)) {
            $query->where("auction", $auction);
        }

        $totems = $request->input('totems');
        if (isset($totems)) {
            $query->where("totems", $totems);
        }

        $redeemcode = $request->input('redeemcode');
        if (isset($redeemcode)) {
            $query->where("redeemcode", $redeemcode);
        }

        $enhance = $request->input('enhance');
        if (isset($enhance)) {
            $query->where("enhance", $enhance);
        }

        $cashCow = $request->input('cashCow');
        if (isset($cashCow)) {
            $query->where("cashCow", $cashCow);
        }

        $recharge_holyshit = $request->input('recharge_holyshit');
        if (isset($recharge_holyshit)) {
            $query->where("recharge_holyshit", $recharge_holyshit);
        }

        $recharge_godlike = $request->input('recharge_godlike');
        if (isset($recharge_godlike)) {
            $query->where("recharge_godlike", $recharge_godlike);
        }

        $position = $request->input('position');
        if (isset($position)) {
            $query->where("position", $position);
        }


        #pagination
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);
        $query->orderBy("dbid", "desc");
        $players = $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = $currentPage);
        #return
        return PlayerResource::collection($players);
    }

    public function store(PlayerRequest $request)
    {
        $fields = $request->all();
        $player = Player::create($fields);
        return new PlayerResource($player);
    }

    public function show($id)
    {
        $player = Player::findOrFail($id);
        return new PlayerResource($player);
    }

    public function update(PlayerRequest $request, $id)
    {
        $player = Player::findOrFail($id);
        $fields = $request->all();
        $player->update($fields);
        return new PlayerResource($player);
    }

    public function destroy($id)
    {
        Player::destroy($id);
        return new JsonResource(null);
    }
}
