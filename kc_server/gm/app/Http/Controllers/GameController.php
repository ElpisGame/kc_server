<?php

namespace App\Http\Controllers;

use App\Models\Gmcmd;
use App\Models\Pay;
use App\Models\Order;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    function items()
    {
        $text = Storage::disk('onekey')->get("item5.txt");
        $array = explode("\n", $text);
        $data = [];
        foreach ($array as $line) {
            $txts = explode(";", $line);
            if (count($txts) == 2) {
                $data[] = [
                    'value' => $txts[0],
                    'label' => $txts[1],
                ];
            }
        }
        return $data;
    }

    function giftPacks()
    {
        $files = Storage::disk('onekey')->allFiles();
        $files = collect($files)->filter(function ($value) {
            return Str::contains($value, "yz");
        });
        $files = $files->map(function ($value) {
            return [
                "value" => $value,
                "label" => "套餐礼包 " . str_replace(['yz', '.txt'], "", $value),
                'gifts' => $this->_getGiftPackItems($value),
                'raw' => Storage::disk('onekey')->get($value)
            ];
        });
        return $files->values();
    }

    public function sendMail(Request $request)
    {
        $fields = $request->only(['username', 'userid', 'itemtype', 'item', 'num']);
        $player = Player::query()->where("account", $fields['username'])->first();
        //按account和dbid查找角色
        // $account = $fields['username'];
        // $userId = $fields['userid'];
        // $playerSql = DB::select('select * from players where account = ? and dbid = ?', [$account, $userId]);

        // if (empty($playerSql)) return 0;
        // $player = $playerSql[0];

        $cmd = Gmcmd::create([
            'serverid' => $player->serverid,
            'cmd' => 'mail',
            'param1' => $player->dbid,
            'param2' => $fields['itemtype'],
            'param3' => $fields['item'],
            'param4' => $fields['num'],
        ]);
        return $cmd;
    }

    public function go(Request $request)
    {
        $fields = $request->only(['username', 'chapterlevel']);
        $fields['chapterlevel'] = min(intval($fields['chapterlevel']), 3000);//最大只有3000关
        $fields['chapterlevel'] = max($fields['chapterlevel'], 1);

        $player = Player::query()->where("account", $fields['username'])->first();
        if (empty($player)) return 0;

        $cmd = Gmcmd::create([
            'serverid' => $player->serverid,
            'cmd' => 'RunCMD',
            'param1' => $player->dbid,
            'param2' => "go {$fields['chapterlevel']}",
        ]);

        return [
            "cmd" => $cmd,
            "player" => $player
        ];
    }

    public function shell(Request $request)
    {
        $cmd = $request->input("cmd");

        //更新资源
        switch ($cmd) {
            case 'updateall':
                $cmd = "cd /server/game/sh && sh gamectl.sh updateall";
                break;
            case 'startbase':
                $cmd = "cd /server/game/sh && sh gamectl.sh startbase && echo 'hi'";
                break;
            case 'restartbase':
                $cmd = "cd /server/game/sh && sh gamectl.sh restartbase";
                break;
            case 'forcestopall':
                $cmd = "cd /server/game/sh && sh gamectl.sh forcestopall";
                break;
            default:
                $cmd = '';
                break;
        }
        if (empty($cmd)) return 0;

        //执行指令
        $output = array();
        $return_var = -100;
        exec($cmd, $output, $return_var);
        return [$cmd, $output, $return_var, exec('whoami')];
    }

    //充值：往pay表里插入一条记录，游戏服会轮询使用数据充值并删除记录
    public function charge(Request $request)
    {
        $fields = $request->only(['username', 'goodsid']);
        $player = Player::query()->where("account", $fields['username'])->first();
        if (empty($player)) return 0;

        $pay = Pay::create([
            'serverid' => $player->serverid,
            'playerid' => $player->dbid,
            'goodsid' => $fields['goodsid']
        ]);

        DB::update("update pay set dbid = $player->dbid where playerid =?", [$player->dbid]);
        return $pay;
    }

    private function _getGiftPackItems($fileName)
    {
        $text = Storage::disk('onekey')->get($fileName);
        $gifts = explode("\n", $text);
        $gifts = collect($gifts)->map(function ($value) {
            $data = explode(";", $value);
            if (count($data) == 3)
                return $data;
        });
        return $gifts;
    }

    public function sendGiftPack(Request $request)
    {
        $fields = $request->only(['username', 'giftPack']);
        $player = Player::query()->where("account", $fields['username'])->first();
        if (empty($player)) return 0;

        $gifts = $this->_getGiftPackItems($fields['giftPack']);

        $response = [];
        foreach ($gifts as $gift) {
            if (!$gift) continue;
            $response[] = Gmcmd::create([
                'serverid' => $player->serverid,
                'cmd' => 'mail',
                'param1' => $player->dbid,//roleid?
                'param2' => 1,
                'param3' => $gift[0],
                'param4' => $gift[1],
            ]);
        }
        return $response;
    }

    public function saveGiftPack(Request $request)
    {
        $fields = $request->only(['raw', 'giftPack']);
        if ($fields['giftPack']) {
            $result = Storage::disk('onekey')->put($fields['giftPack'], $fields['raw']);
            return $result;
        }
        return 0;
    }

    public function silent(Request $request)
    {
        $time = time() + 60 * 60 * 24;//'1608568913';

        $fields = $request->only(['username']);
        $player = Player::query()->where("account", $fields['username'])->first();
        if (empty($player)) return 0;

        $cmd = Gmcmd::create([
            'serverid' => $player->serverid,
            'cmd' => 'Silent',//Silent->禁言 Sealed->封玩家，并且踢下线
            'param1' => $player->dbid,//roleid?
            'param2' => $time
        ]);
        return $cmd;
    }

    public function unsilent(Request $request)
    {
        $time = '0';
        $fields = $request->only(['username']);
        $player = Player::query()->where("account", $fields['username'])->first();
        if (empty($player)) return 0;

        $cmd = Gmcmd::create([
            'serverid' => $player->serverid,
            'cmd' => 'Silent',//Silent->禁言 Sealed->封玩家，并且踢下线
            'param1' => $player->dbid,//roleid?
            'param2' => $time
        ]);
        return $cmd;
    }

    public function sealed(Request $request)
    {
        $time = time() + 60 * 60 * 24;//'1608568913';

        $fields = $request->only(['username']);
        $player = Player::query()->where("account", $fields['username'])->first();
        if (empty($player)) return 0;

        $cmd = Gmcmd::create([
            'serverid' => $player->serverid,
            'cmd' => 'Sealed',//Silent->禁言 Sealed->封玩家，并且踢下线
            'param1' => $player->dbid,//roleid?
            'param2' => $time
        ]);
        return $cmd;
    }

    public function unsealed(Request $request)
    {
        $time = '0';
        $fields = $request->only(['username']);
        $player = Player::query()->where("account", $fields['username'])->first();
        if (empty($player)) return 0;

        $cmd = Gmcmd::create([
            'serverid' => $player->serverid,
            'cmd' => 'Sealed',//Silent->禁言 Sealed->封玩家，并且踢下线
            'param1' => $player->dbid,//roleid?
            'param2' => $time
        ]);
        return $cmd;
    }

    //游戏内广播
    public function broadCast(Request $request)
    {
        // echo ("broadCast");
        $fields = $request->only(['noticeContent', 'noticeType']);
        $cmd = Gmcmd::create([
            'serverid' => 1,
            'cmd' => 'notice',
            'param1' => $fields['noticeContent'],
            'param2' => $fields['noticeType'],
        ]);
        return $cmd;
    }

    //验证白名单ip
    function verifyWhiteListByIp(Request $request)
    {
        $input = $request->all();
        $ip = $input['ip'];

        $whiteIp = DB::select('select * from whitelist where ip = ?', [$ip]);

        if (!empty($whiteIp)) {
            return ['result' => 1];
        } else {
            return ['result' => 0];
        }
    }


    function getServerlist()
    {
        $filePath = "/gameserver/serverlist.txt";
        $data = [];
        if (!Storage::disk('onekey')->exists($filePath)) {
            return $data;
        }
        $text = Storage::disk('onekey')->get($filePath);
        $serverlist = explode(";", $text);

        $array = explode("\n", $text);

        foreach ($array as $line) {
            $txts = explode(";", $line);
            if (count($txts) == 6) {
                $data[] = [
                    'id' => $txts[0],
                    'name' => $txts[1],
                    'ip' => $txts[2],
                    'port_1' => $txts[3],
                    'port_2' => $txts[4],
                    'platformid' => $txts[5],
                ];
            }
        }
        return $data;
    }

    //发货
    function delivergoods(Request $request)
    {
        $input = $request->all();

        $dbid = $input['dbid'];
        $serverid = $input['serverid'];
        $goodsid = $input['goodsid'];

        // DB::insert('insert into pay (dbid,serverid,playerid,goodsid) values (?,?,?,?)', [$dbid, $serverid, $dbid, $goodsid]);
        DB::insert('insert into pay (dbid,serverid,playerid,goodsid) values (?,?,?,?)', [$dbid, 1, $dbid, $goodsid]);
    }

      //查询账号下是否有多个角色（合服后会出现）
    function checkRoleInfo(Request $request)
    {
        $input = $request->all();

        //查找account下的角色
        $playerSql = DB::select('select * from players where account = ?', [$input['account']]);

        $roleData_1 = "";
        if (!empty($playerSql)) {
                // $player->dbid
            $cnt = count($playerSql);
            if ($cnt == 2) {
                $player_1 = $playerSql[0];
                $player_2 = $playerSql[1];
                $roleData_1 = $player_1->dbid . ";" . $player_1->level . ";" . $player_1->totalpower . ";" . $player_1->name;
                $roleData_2 = $player_2->dbid . ";" . $player_2->level . ";" . $player_2->totalpower . ";" . $player_2->name;

                return ['result' => 1, 'data1' => $roleData_1, 'data2' => $roleData_2];
            }
        } else {
                // echo("找不到account的玩家---".$userId);
            return ['result' => 0];
        }
    }

    //检索日期
    function statisticalGet(Request $request)
    {
        $input = $request->all();

        // //serverid
        // $serverid = $input['serverid'];
        // //server Ip
        // $serverIp = $input['ip'];
        
        // //amount单位
        // $priceUnit = $input['priceUnit'] || 1;
        $priceUnit = 1;

        //开服日期
        $Sql = DB::select('select * from datalist where 1');
        $timers = $Sql[0]->timers;
        $timers = str_replace("{", "", $timers);
        $timers = str_replace("}", "", $timers);
        $array = explode(",", $timers);

        $serverRunDay = 0;

        // $newStr = "{s014serverOpenTime=1642730420,s012serverRunDay=4}";
        foreach ($array as $line) {
            $num = substr_count($line, 's012serverRunDay');
            if ($num > 0) {
                $array = explode("=", $line);
                // print("serverRunDay= " . $array[1]);
                if ($array[1] > 0) {
                    $serverRunDay = (int)$array[1];
                    // var_dump($serverRunDay);
                    break;
                }
            }
        }

        // print("=========================");
        // $serverRunDay = 15;

        $todaytime = date('Y-m-d H:i:s', strtotime(date("Y-m-d"), time()));//今天零点
        $ystd = strtotime($todaytime) + 86400; // 今天24点整毫秒时间

        $endTime = $ystd;

        // $playersSql = DB::select('select * from players where 1');
        // //当前玩家数量
        // $totalCnt = count($playersSql);
        // print("serverRunDay= " . $serverRunDay);

        $totalCnt = 0;

        $players = [];

        //玩家注册表
        $roleCreateList = [];
        //玩家登录表
        $roleLoginList = [];

        //玩家最高等级
        for ($i = 1; $i <=$serverRunDay; $i++) {
            // $oldtoday=date('Y-m-d H:i:s',strtotime($todaytime)-86400*$i);//昨日零点 

            $startTime = $ystd - 86400 * $i;//昨日零点 

            //日期
            $dateStr = date("Y-m-d", $startTime);
            // print("dateStr= " . $dateStr);

            //查找一天的新注册玩家
            $playersCreateSql = DB::select('select * from players where `createtime`  >= ? and `createtime`  <  ?', [$startTime, $endTime]);
            sleep(0.1);
            
            //查找一天的登录玩家
            $playersLoginSql = DB::select('select * from players where `lastonlinetime`  >= ? and `lastonlinetime`  <  ?', [$startTime, $endTime]);
            sleep(0.1);

            //查找一天的用户行为登出
            $playersLogOutSql = DB::select('select * from behavior where `curTime`  = ?', [$startTime]);
            sleep(0.1);

            // $roleCreateList[$dateStr] = $playersCreateSql;
            // $roleLoginList[$dateStr]  = $playersLogOutSql;

           //当天总注册人数
            $createCnt = count($playersCreateSql);
           //当天登录用户数
            $loginCnt = count($playersLoginSql);
            // $loginCnt = 0;

            //当天总登出人数
            $logoutCnt = count($playersLogOutSql);

            $totalCnt = $totalCnt + $createCnt;

            $data["value"] = $dateStr;
            $data["label"] = $dateStr;
            $data["raw"] = "创建角色：" . $createCnt . "\n" . "最后登录角色：" . $loginCnt. "\n" . "活跃角色：" . $logoutCnt;

            $datas = [];

            array_push($datas, "今天创建角色：" . $createCnt);
            array_push($datas, "今天最后登录：" . $loginCnt);
            array_push($datas, "今天登录角色：" . $logoutCnt);

            //---
            $liucunArr = [];
            $playerSql = [];
            for ($j = 0; $j < $logoutCnt; $j++) {
                $sql = $playersLogOutSql[$j];
                $playerSql = DB::select('select * from players where dbid = ?', [$sql->dbid]);

                if(!empty($playerSql)){
                    $dateStr = date("Y-m-d", $playerSql[0]->createtime);
                    if(array_key_exists($dateStr,$liucunArr)){
                        $liucunArr[$dateStr] = $liucunArr[$dateStr] + 1;
                    }
                    else{
                        $liucunArr[$dateStr] = 1;
                    }
                }
            }

            //释放内存
            unset($playersCreateSql);
            unset($playersLoginSql);
            unset($playersLogOutSql);
            unset($playerSql);

            // var_dump($liucunArr);

            // print("#################################");
            // var_dump($data["gifts"]);
            foreach($liucunArr as $key=>$value)
            {
                array_push($datas,"创建时间：" . $key." 人 ：".$value);
            }
            unset($liucunArr);

            // print("######################");
            // print($logoutCnt);
            $gifts = [];

            array_push($gifts, $datas);

            $data["gifts"] = $gifts;

            // $data["create"] = $createCnt;
            // $data["login"] = $loginCnt;
            array_push($players, $data);
            $endTime = $startTime;
        }

        //--------------------------------列出每天登录的玩家注册的日期------------------//
        // for ($i = 0; $i <$serverRunDay; $i++) {
        //     $data = $players[$i];

        //     $playersLogOutSql = $roleLoginList[$data["value"]];
        //     $logoutCnt = count($playersLogOutSql);
            
        //     // print($data["value"]." logoutCnt ".$logoutCnt);
        //     $liucunArr = [];

        //     for ($j = 0; $j < $logoutCnt; $j++) {
        //         $sql = $playersLogOutSql[$j];
        //         //查找登录时间
        //         foreach($roleCreateList as $key=>$value)
        //         {
        //             $_cnt = count($value);
        //             for ($k=0; $k < $_cnt; $k++) { 
        //                 $_sql = $value[$k];
        //                 if($_sql->dbid==$sql->dbid){
        //                     if(array_key_exists($key,$liucunArr)){
        //                         $liucunArr[$key] = $liucunArr[$key] + 1;
        //                     }
        //                     else{
        //                         $liucunArr[$key] = 1;
        //                     }
        //                     break;
        //                 }
        //             }
        //         }
        //     }

        //     // var_dump($liucunArr);

        //     // print("#################################");
        //     // var_dump($data["gifts"]);
        //     foreach($liucunArr as $key=>$value)
        //     {
        //         array_push($data["gifts"][0],"创建时间：" . $key." 人 ：".$value);
        //     }
        //     // var_dump($data["gifts"]);
        //     $players[$i] = $data;
        // }

        //-----------------------------------------------------------------------------//


        //----------------去90端，查找订单数据-----------------------//
        // $arr_post = array(
        //     'serverid' => $serverid,
        //     'priceUnit' => $priceUnit,
        //     'serverRunDay' => $serverRunDay
        // );

        // $url = "http://{$serverIp}:90/api/game/dayOrdersGet";

        // print("##################");
        // echo ($url);
        // print("##################");

        // $post_data = http_build_query($arr_post);
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        // $data = curl_exec($ch);
        // curl_close($ch);

        // $dataArray = json_decode($data, true);
        // $orders = isset($dataArray['orders']) ? $dataArray['orders'] : [];

        // var_dump($orders);

        // foreach($orders as $key=>$value)
        // {
        //     $players[$key] =  array_merge($value,$orders[$key]);
        // }

        return ['totalCnt' => $totalCnt, 'serverRunDay' => $serverRunDay, 'players' => $players];
    }

    function dayOrdersGet(Request $request)
    {
        $input = $request->all();
        //amount单位
        $priceUnit = $input['priceUnit'] || 1;
        //开服日期
        $serverRunDay = $input['serverRunDay'];

        // http://116.205.141.55:91
        $host = $input['host'];
 
        $array = explode(":", urldecode($host));

        $ip = str_replace("//", "", $array[1]);
        $port = $array[2];

        // print( $ip ." ". $port ." ". $priceUnit . " ".$serverRunDay  );

        // ----------------去90端，查找订单数据-----------------------//
        $arr_post = array(
            'ip' => urlencode($ip),
            'port' => $port,
            'priceUnit' => $priceUnit,
            'serverRunDay' => $serverRunDay
        );
 	   $ip  = "116.205.141.55";
        $url = "http://{$ip}:90/api/game/dayOrders";

        // print("##################");
        // echo ($url);
        // print("##################");

        $post_data = http_build_query($arr_post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($ch);
        curl_close($ch);

        $dataArray = json_decode($data, true);

        $result = isset($dataArray['result']) ? $dataArray['result'] : [];
        $orders = isset($dataArray['orders']) ? $dataArray['orders'] : [];

        // var_dump($orders);

        return ['result' => $result, 'orders' => $orders];
    }


    //获取指定日期的充值数据
    function dayOrders(Request $request)
    {
        $input = $request->all();
        
        //查找服务器id
        $serverid = 0;

        $ip = $input['ip'];
        $port = $input['port'];

        $serverlist = $this->getServerlist();

        if (empty($serverlist)) {
            return ['result' => 0];
        }

        foreach ($serverlist as $server) {
            $str = substr($server["id"], 3);
            $id = (int)$str;
            if ($server['ip'] == $ip && $server['port_2'] == $port) {
                $serverid = $id;
                // print("##################");
                // echo($serverid);
                // print("##################");
                break;
            }
        }

        if ($serverid == 0) {
            return ['result' => 3];
        }
        //  //serverid
        // $serverid = $input['serverid'];
        //amount单位
        $priceUnit = $input['priceUnit'] || 1;
        //开服日期
        $serverRunDay = $input['serverRunDay'];
        
        //当日零点 
        $todaytime = date('Y-m-d H:i:s', strtotime(date("Y-m-d"), time()));//今天零点
        $ystd = strtotime($todaytime) + 86400; // 今天24点整毫秒时间

        $endTime = $ystd;

        $orders = [];

        for ($i = 1; $i <= $serverRunDay; $i++) {
            $startTime = $ystd - 86400 * $i;//昨日零点 
            //日期
            $dateStr = date("Y-m-d", $startTime);

            //查找一天的订单
            $ordersSql = DB::select('select * from `orders` where `ct_time` >= ? and `ct_time` < ?  and `serverid` = ?', [$startTime, $endTime, $serverid]);

            sleep(1);

            $orderCompleteSql = DB::select('select * from `orders` where `ct_time` >= ? and `ct_time` < ? and `complete` = 1 and `serverid` = ? order by `dbid` desc', [$startTime, $endTime, $serverid]);

            //当天总发起订单数
            $totalCnt = count($ordersSql);
            //当天完成订单数
            $completeCnt = 0;
            //当天完成订单总金额
            $completeAmount = 0;
            //当天充值人数
            $orderPlayerNum = 0;
            if (!empty($ordersSql)) {
                if (!empty($orderCompleteSql)) {
                    $completeCnt = count($orderCompleteSql);
                    $orderPlayerId = 0;
                    for ($j = 0; $j < $completeCnt; $j++) {
                        $sql = $orderCompleteSql[$j];
                        $completeAmount = $completeAmount + $sql->amount * $priceUnit;
                        if ($sql->dbid != $orderPlayerId) {
                            $orderPlayerNum++;
                            $orderPlayerId = $sql->dbid;
                        }
                    }
                }
            }
            $endTime = $startTime;
            $array = ['dateStr' => $dateStr, 'totalCnt' => $totalCnt, 'completeCnt' => $completeCnt, 'completeAmount' => $completeAmount, 'orderPlayerNum' => $orderPlayerNum];

            $orders[$dateStr] = $array;
            unset($ordersSql);
            // array_push($orders,$array);
        }
        return ['result' => 1, 'orders' => $orders];
    }

    function OnlineCount(Request $request)
    {
        $Sql = DB::select("select * from `log` order by `dbid` desc limit 1");

        if (!empty($Sql)) {
            // $player->dbid
            $cnt = count($Sql);
            $logTime = $Sql[0]->log_time;
            $online = $Sql[0]->value1;

            //指定时间戳转日期
            $date = date("Y-m-d H:i", $logTime);

            return [
                "date" => $date,
                "online" => $online
            ];
        }
    }

    //发送短信
    // 新的短信产品
    // 企业ID：1032
    // 企业名称：酷畅验证码
    // 计费企业：
    // 是否强制签名：否
    // 账户类型：行业用户
    // 是否模板发送：否
    // 绑定IP地址：
    // 敏感词审核：否
    // 用户登录帐号：kuchangyzm
    // 用户登录密码：0pZ3FVTad
    // api密码：0pZ3FVTad
    // 请参考本文档步骤2
    function SendMs($business_id,$phone_number,$code)
    {
        $SpCode = "1032";
        $LoginName = "kuchangyzm";
        $Password = "0pZ3FVTad";
        $MessageContent = "【上海拾运】您的验证码为：" . $code . "，请在5分钟内进行下一步操作。请妥善保管，不要透露给任何人。如非本人操作请忽略。";
        $sms_url = "http://106.15.52.63:8513/sms/Api/ReturnJson/Send.do";
        // url = "http://106.15.52.63:8513/sms/Api/searchNumber.do"

        //保存本次记录
        $arr_post = array(
            'SpCode' => $SpCode,
            'LoginName' => $LoginName,
            'Password' => $Password,
            'MessageContent' => $MessageContent,
            'UserNumber' => $phone_number,
            'SerialNumber' => $business_id,
        );

        $post_data = http_build_query($arr_post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sms_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($ch);
        curl_close($ch);

        $dataArray = json_decode($data, true);
        $resultCode = isset($dataArray['result']) ? $dataArray['result'] : 0;
        $resultDesc = isset($dataArray['description']) ? $dataArray['description'] : "";
        // var_dump($data);
        return ['result' => $resultCode, 'data' => $resultDesc];
    }


    function getCodeMS(Request $request)
    {
        $input = $request->all();
        $dbid = $input['dbid'];
        $phone_number = $input['phone_number'];
        $serverid = $input['serverid'];

        $dateTime = new \DateTime();
        $now_time = $dateTime->getTimestamp();

        //按手机号查询已有的验证码,取最新创建的一条
        $Sqls = DB::select('select * from phone_code where dbid = ? and phoneN= ? order by `ct_time` desc limit 1', [$dbid,$phone_number]);

        if (!empty($Sqls)) {
            //已有数据
            $sql = $Sqls[0];

            if($sql ->complete)
            {
                //已经完成绑定
                return ['result' => 2, 'resultDesc' => "已经完成绑定"];
            }

            $ct_time = $sql ->ct_time;

            $delta_time = $now_time - $ct_time;
            
            if($delta_time < 60){
                //间隔小于60s
                return ['result' => 3, 'resultDesc' => "请稍后再试"];
            }

            //随机code
            $code = mt_rand(1,9) * 1000 + mt_rand(0,9) * 100 + mt_rand(0,9) * 10 + mt_rand(0,9);
            
            $business_id = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

            //重新发送短信验证码
            $response = $this->SendMs($business_id,$phone_number,$code);
            if($response['result']==0){
                //更新
                DB::update("update `phone_code` set `ct_time`=$now_time,`code`=$code where `phoneN` =?", [$phone_number]);
            }
            return ['result' => $response['result'], 'resultDesc' => $response['data']];
        }
        else
        {
            //随机code
            $code = mt_rand(1,9) * 1000 + mt_rand(0,9) * 100 + mt_rand(0,9) * 10 + mt_rand(0,9);

            $business_id = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

            //第一条
            $response = $this->SendMs($business_id,$phone_number,$code);

            if($response['result']==0){
                //插入
                DB::insert('insert into phone_code (dbid,phoneN,code,ct_time,serverid) values (?,?,?,?,?)', [$dbid, $phone_number, $code, $now_time,$serverid]);
            }
            return ['result' => $response['result'], 'resultDesc' => $response['data']];
        }
    }


    //验证短信验证码，绑定手机
    function verifyMS(Request $request)
    {
        $input = $request->all();
        $dbid = $input['dbid'];
        $phone_number = $input['phone_number'];
        $serverid = $input['serverid'];
        $code = $input['code'];

        //按手机号查询已有的验证码,取最新创建的一条
        $Sqls = DB::select('select * from phone_code where dbid = ? and phoneN= ? order by `ct_time` desc limit 1', [$dbid,$phone_number]);
        if (!empty($Sqls)) {
            $sql = $Sqls[0];
            
            $dateTime = new \DateTime();
            $now_time = $dateTime->getTimestamp();
            $ct_time = $sql ->ct_time;

            $delta_time = $now_time - $ct_time;
            
            if($delta_time > 60 * 5){ //5分钟超时
                return ['result' => 0, 'resultDesc' => "验证码超时，请重新请求"];
            }

            if($sql->phoneN==$phone_number && $sql->code==$code){
                //更新
                DB::update("update phone_code set end_time=$now_time,complete=1 where phoneN =?", [$phone_number]);
                return ['result' => 1, 'resultDesc' => "绑定成功"];
            }
            else
            {
                return ['result' => 2, 'resultDesc' => "绑定失败，验证码错误"];
            }
        }
        else{
            return ['result' => 0, 'resultDesc' => "验证码不存在"];
        }
    }

    //检查当前平台下有多少服
    function getTotalServerListByPlat($pid)
    {
        $serverlist = $this->getServerlist();
        if (empty($serverlist)) {
            return ['result' => 0];
        }
        $cnt = 0;
        $datas = [];
        foreach ($serverlist as $server) {
            $str = substr($server["platformid"], 0);
            $id = (int)$str;
            if ($id==$pid) 
            {
                $cnt = $cnt + 1;
                $url = "http://{$server['ip']}:{$server['port_2']}";
                array_push($datas,$url);
            }
        }

        return ['result' => 1, 'count' => $cnt,'data' => $datas];
    }

   //查询平台下所有服的活跃人数
    function getTotalActivePlayerNum(Request $request)
    {
        $input = $request->all();
        //平台id
        $pid = $input['platformid'];

        $response = $this->getTotalServerListByPlat($pid);
        $server_cnt = 0;
        $server_list = [];

        if($response['result'] && $response['result']==1)
        {   
            $server_cnt = $response['count'];
            $server_list = $response['data'];
            $wait = $server_cnt;

            $cnt = 0;
            for ($i=0; $i < $server_cnt; $i++) { 
                $url = $server_list[$i]."/api/game/queryActivePlayerNumByServer";
                sleep(1);
                $response = $this->getTotalActivePlayerNumServer($url,$pid);
                // var_dump($response);
                $wait = $wait - 1;
                if($response && $response['result'] &&  $response['result']==100 && $response['count']){
                    $cnt  = $cnt + $response['count'];
                }
                if($wait==0){
                    //返回
                    return ['result' => 100, 'count' => $cnt];
                }
            }
        }
        return ['result' => 0, 'count' => 0];
    }
    
    function getTotalActivePlayerNumServer($url,$pid)
    {
        $arr_post = array(
            'platformid' => $pid,
        );

        $post_data = http_build_query($arr_post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($ch);
        curl_close($ch);

        $dataArray = json_decode($data, true);

        $resultCode = isset($dataArray['result']) ? $dataArray['result'] : 0;
        $resultCount = isset($dataArray['count']) ? $dataArray['count'] : 0;
        return ['result' => $resultCode, 'count' => $resultCount];
    }

    function queryActivePlayerNumByServer(Request $request)
    {
        $input = $request->all();
        $pid = $input['platformid'];
        
        $Sql = DB::select('select * from log where serverid = ?', [$pid]);

        //daily_active
        if (empty($Sql)) return ['result' => 0, 'count' => 0];
        $datalist = $Sql[0];

        // $daily_active = $datalist->daily_active;
        // $daily_active = str_replace("{", "", $daily_active);
        // $daily_active = str_replace("}", "", $daily_active);
        // $array = explode(",", $daily_active);

        // // $newStr = "{s005total=0,s006target=100,s004list={}}";
        // $cnt = 0;
        // foreach ($array as $line) {
        //     $num = substr_count($line, 's005total');
        //     if ($num > 0) {
        //         $array = explode("=", $line);
        //         // print("total= " . $array[1]);
        //         // var_dump($array[1]);
        //         $cnt = $array[1];
        //     }
        // }
        return ['result' => 100, 'count' => $datalist->value1];
    }

    function setActivityTaskTime(Request $request)
    {
        $input = $request->all();
        $delta = $input['time'];
        
        $Sql = DB::select('select * from activity_task where 1');

        //daily_active
        if (empty($Sql)) return ['result' => 0, 'count' => 0];

        $cnt = count($Sql);
        $orderPlayerId = 0;
        for ($j = 0; $j < $cnt; $j++) {
            $sql = $Sql[$j];
            $ct_time = $sql->ct_time + $delta;
            //更新时间
            DB::update("update activity_task set ct_time = $ct_time where id =?", [$sql->id]);
        }
    }

    //查询是否还有余额，表示可以领取;显示剩余数量
    function qureyBalanceFromChain(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }

        //从数据库读取记录

        //从公司账户读取资产
        $url = "https://api.bitverse.zone/bitverse/wallet/v1/public/asset/nft/query/bitverse/wallet/v1/public/asset/nft/query";
        
        // $address = "0xC135bA226e17B295af3bCAb3a2e0Fb27008e4ed2";

        $address = "0xa9e9412AE9761B6c8AF6Bc76498208F773c5aEc3";
        $contract = "0xb1Be6182F6633B79eDFe03D3A2D76B7E25e032D2";

        $arr_post = array(
            'page' => 0,
            'size' => 100,
            'chainId' => 137, //eth : 1 bsc: 56 polygon: 137
            'address' => $address,
            'contract' => $contract,
        );

        $post_data = http_build_query($arr_post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($ch);
        curl_close($ch);

        $dataArray = json_decode($data, true);
        $resultCode = isset($dataArray['retCode']) ? $dataArray['retCode'] : 1;
        $resultDesc = isset($dataArray['retMsg']) ? $dataArray['retMsg'] : "";

        if($resultCode==0){
            //查询是否有指定tokenid的NFT
            $result = isset($dataArray['result']) ? $dataArray['result'] : "";
            var_dump($result);
            if (!empty($result)) {
                $coinId = isset($dataArray['coinId']) ? $dataArray['coinId'] : 0;
                $symbol = isset($dataArray['symbol']) ? $dataArray['symbol'] : "";
                $name = isset($dataArray['name']) ? $dataArray['name'] : "";
                // if($coinId==966 && $symbol=="cp" && $name == "KnifeCrashSkin"){
                    //游戏皮肤铸造的NFT
                    $list = isset($result['list']) ? $result['list'] : "";
                    if (!empty($list)) {
                        $ownCnt = count($list);
                        for ($i = 0; $i < $ownCnt; $i++) {
                            $list_data = $list[$i];
                            print($list_data["tokenId"]);
                            $tmp[] = $i . '=' . $list_data["tokenId"];
                        }
                        $tokenId_str = implode('_', $tmp);
                        //不可以领取的按钮显示已售罄。
                        return ['result' => $resultCode, 'data' => $tokenId_str];
                    }                    
                // }
            }
            return ['result' => $resultCode, 'data' => ""];
        }
        else{
            return ['result' => $resultCode, 'data' => $resultDesc];
        }
    }

    //从DB检查余额
    function qureyBalanceFromDB(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }

        $complete = 1;

        $paySqlArray = DB::select('select * from orders where complete = ?', [$complete]);

        if (empty($paySqlArray)) {
            return ['result' => 1, 'data' => ""];
        }   

        $cnt = count($paySqlArray);
        for ($i = 0; $i < $cnt; $i++) {
            $paySql = $paySqlArray[$i];
            //验签成功
            $tmp[] = $i . '=' . $paySql->goodsid;
        }
        $tokenId_str = implode('_', $tmp);
        // $tokenId_str = implode('_', $tmp) . $md5key;
        // $signure =  md5($tokenId_str)
        
        //玩家已经拥有的NFT，不可以领取按钮置灰。
        return ['result' => 0, 'data' => $tokenId_str]; 
    }


    //从链上检查玩家的nft
    function checkNFTFormChain(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }
        $page = $input['page']; // >=0
        $size = $input['size']; //数量, 默认20 范围 ： < 1000
        $chainId = $input['chainId']; //公链id： eth : 1 bsc: 56 polygon: 137
        $address = $input['address']; // 钱包地址
        $contract = $input['contract']; //合约地址
        //签名
        $md5Sign = $input['sign'];
        //-------验签------//
        $md5key = '3ef80b3354494c8f29e78030b70ad9d4';
        ksort($input);
        foreach ($input as $k => $v) {
            if ($k == 'sign') {
                continue;
            }
            $tmp[] = $k . '=' . urlencode($v);
        }
        $str = implode('&', $tmp) . $md5key;
        print(md5($str));

        print("#########");

        print($md5Sign);

        $url = "https://api.bitverse.zone/bitverse/wallet/v1/public/asset/nft/query/bitverse/wallet/v1/public/asset/nft/query";
   
        $arr_post = array(
            'page' => $page,
            'size' => $size,
            'chainId' => $chainId,
            'address' => $address,
            'contract' => $contract,
        );

        $post_data = http_build_query($arr_post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($ch);
        curl_close($ch);

        $dataArray = json_decode($data, true);
        $resultCode = isset($dataArray['retCode']) ? $dataArray['retCode'] : 0;
        $resultDesc = isset($dataArray['retMsg']) ? $dataArray['retMsg'] : "";

        if($resultCode==0){
            //查询是否有指定tokenid的NFT
            $result = isset($dataArray['result']) ? $dataArray['result'] : "";

            if (!empty($result)) {
                $coinId = isset($dataArray['coinId']) ? $dataArray['coinId'] : 0;
                $symbol = isset($dataArray['symbol']) ? $dataArray['symbol'] : "";
                $name = isset($dataArray['name']) ? $dataArray['name'] : "";
                if($coinId==966 && $symbol=="cp" && $name == "KnifeCrashSkin"){
                    //游戏皮肤铸造的NFT
                    $list = isset($result['list']) ? $result['list'] : "";
                    if (!empty($list)) {
                        $ownCnt = count($list);
                        for ($i = 0; $i < $ownCnt; $i++) {
                            $list_data = $list[$i];
                            print($list_data["tokenId"]);
                            $tmp[] = $i . '=' . $list_data["tokenId"];
                        }
                        $tokenId_str = implode('_', $tmp) . $md5key;
                        $signure =  md5($tokenId_str);
                        //玩家已经拥有的NFT，不可以领取按钮置灰。
                        return ['result' => $resultCode, 'data' => $signure];
                    }                    
                }
            }
            return ['result' => $resultCode, 'data' => ""];
        }
        else{
            return ['result' => $resultCode, 'data' => $resultDesc];
        }
        // var_dump($data);
    }


    //从DB检查玩家领取过的nft
    function checkNFTFormDB(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }
        $uuid = $input['uuid'] || 0; //uuid

        $country = $input['country'] || ""; //country

        $adress = $input['adress']; //玩家钱包地址
        
        $paySqlArray = DB::select('select * from orders where account = ?', [$adress]);

        if (empty($paySqlArray)) {
            return ['result' => 0, 'data' => ""];
        }   

        $cnt = count($paySqlArray);
        $has = false;
        for ($i = 0; $i < $cnt; $i++) {
            $paySql = $paySqlArray[$i];
            //验签成功
            if ($paySql->complete == 1) {
                $has  = true;
                $tmp[] = $i . '=' . $paySql->goodsid;
            } 
        }

        if($has){
            $tokenId_str = implode('_', $tmp);
            // $tokenId_str = implode('_', $tmp) . $md5key;
            // $signure =  md5($tokenId_str)
            
            //玩家已经拥有的NFT，不可以领取按钮置灰。
            return ['result' => 200, 'data' => $tokenId_str]; 
        }
        else{
            return ['result' => 0, 'data' => ""]; 
        }
    }


    //发起领取nft
    function requestNFT(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }

        $success = 0; // 0 : 成功 1： 失败

        $uuid = $input['uuid']; //uuid

        $country = $input['country']; //country

        $adress = $input['adress']; //玩家钱包地址
        
        $goodsId = $input['id']; //nft皮肤id

        $goodsName = $input['name']; //nft皮肤名称
        
        $nftName = $input['name2']; //nft名称

        $app_trade_no = $input['token']; //nft系列tokenId

        $paySqlArray = DB::select('select * from orders where account = ? and goodsId = ?', [$adress, $goodsId]);

        if (!empty($paySqlArray)) {
            $paySql = $paySqlArray[0];
            //如果已经订单已经完成,不需要操作
            if ($paySql->complete == 1) {
                return ['result' => 300, 'data' => "You have owned this one"];
            }
            return ['result' => 201, 'data' => "The request is successful. Please log in to wallet later"];
        }

        //创建时间
        $dateTime = new \DateTime();
        $ct_time = $dateTime->getTimestamp();

        // //添加记录到orders记录
        // $insert = DB::insert(
        //     'insert into orders (dbid,account,cp_no,goodsId,goodsName,ct_time,app_trade_no,complete) values (?,?,?,?,?,?,?,?)',
        //     [$uuid, $adress, $nftName, $goodsId, $goodsName, $ct_time,  $app_trade_no ,$success]
        // );

        //添加记录到orders记录
        $insert = DB::insert(
            'insert into orders (account,cp_no,goodsId,goodsName,ct_time,app_trade_no,out_trade_no,complete) values (?,?,?,?,?,?,?,?)',
            [$adress, $nftName, $goodsId, $goodsName, $ct_time,  $app_trade_no ,$uuid ,$success]
        );

        return ['result' => 200, 'data' => "The request is successful. Please log in to wallet later"];       
    }

    //领取nft完毕
    function recieveNFT(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }
        $adress = $input['adress']; //玩家钱包地址
        $tokenId = $input['token']; //nft皮肤id

        $paySqlArray = DB::select('select * from orders where account = ? and app_trade_no = ?', [$adress, $tokenId]);

        if (empty($paySqlArray)) {
            // echo $rechargeNo;
            return 'fail';
        }
        $paySql = $paySqlArray[0];
        //如果已经订单已经完成,不需要操作
        if ($paySql->complete == 1) {
            return 'success';
        }

        //完成时间
        $dateTime = new \DateTime();
        $end_time = $dateTime->getTimestamp();

        $amount = 1;
        $pay_channel = 0; // 0 ：手动  1 ：自动
        //-------------

        // if(1>0){
        //     print("rechargeNo: ".$rechargeNo." pay_channel: ".$pay_channel." amount: ".$amount." app_trade_no: ".$app_trade_no." out_trade_no: ".$out_trade_no);
        //     return '---fail---';
        // }

        //完成订单
        DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,complete=1 where account =? and app_trade_no = ?", [$adress, $tokenId]);
        return 'success';
    }

    //获取nftrank
    function queryWorldRank(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }
        $ttime = $input['time'];

        $Sql = DB::select('select * from nftrank where 1');

        //-------------

        // if(1>0){
        //     print("rechargeNo: ".$rechargeNo." pay_channel: ".$pay_channel." amount: ".$amount." app_trade_no: ".$app_trade_no." out_trade_no: ".$out_trade_no);
        //     return '---fail---';
        // }

        $dateTime = new \DateTime();
        $now_time = $dateTime->getTimestamp();
        
        //剩余时间设计两周时间，06/14截止
        $surplusTime = 1688832000- $now_time;


        if (empty($Sql)) return ['result' => 0, 'count' => 0 , 'surplusTime' => $surplusTime];

 
        $cnt = count($Sql);
        $orderPlayerId = 0;
        for ($j = 0; $j < $cnt; $j++) {
            $sql = $Sql[$j];
            $data[] = [
                'score' => $sql->score,
                'rank' => 0, //客户端排序
                'id' => $sql->uid,
                'adress' => $sql->adress,
            ];
        }
        return ['result' => 0, 'count' => $cnt,'surplusTime' => $surplusTime ,'round'=> 0 ,'rankInfo' => $data];
    }



    //上传RANK积分
    function uploadNFTRankScore(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }

        $dateTime = new \DateTime();
        $now_time = $dateTime->getTimestamp();
        
        //剩余时间设计两周时间，06/14截止
        $surplusTime = 1688832000- $now_time;

        if($surplusTime < 0){
            //本轮已经结束
            return ['result' => 2, 'surplusTime' => 0];
        }

        $score = $input['score'];

        $adress = $input['adress']; //玩家钱包地址
        
        $uuid = $input['uuid']; //uuid

        $heroid = $input['heroid']; //nft皮肤id

        $weaponid = $input['weaponid']; //nft皮肤id

        //-----------2023/6/23 新增加4个字段----------//
        $playCnt = 0; //playCnt
        if(in_array("playCnt",$input)){
            $playCnt = $input['playCnt'];
        }

        $buy = ""; //buy
        if(in_array("buys",$input)){
            $buy = $input['buys'];
        }

        $token = ""; //
        if(in_array("token",$input)){
            $token = $input['token'];
        }

        $version = ""; ////当前版本号带abcode
        if(in_array("version",$input)){
            $version = $input['version'];
        }

        //--------------------------------------------//

        $md5Sign = $input['sign'];
        //-------验签------//
        $md5key = '3ef80b3354494c8f29e78030b70ad9d4';
        ksort($input);
        foreach ($input as $k => $v) {
            if ($k == 'sign' || $k== 'ct_time') {
                continue;
            }
            $tmp[] = $k . '=' . urlencode($v);
        }
        $str = implode('&', $tmp) . $md5key;

        //创建时间
        $ct_time = $now_time;

        if($md5Sign!=md5($str)){ //记录作弊玩家 round = -1
            $insert = DB::insert( 
                'insert into nftrank (uid,adress,hero,weapon,ct_time,end_time,score,round,playCnt,buy,token,version) values (?,?,?,?,?,?,?,?,?,?,?,?)',
                [$uuid, $adress, $heroid, $weaponid, $ct_time,$now_time,$score,-1,0,"0","0",$version]
            );
            return ['result' => 1, 'surplusTime' => $surplusTime];
        }
        
        $paySqlArray = DB::select('select * from nftrank where adress = ?', [$adress]);

        if (!empty($paySqlArray)) {
            $sql = $paySqlArray[0]; 
            if($sql->round<0){
                return ['result' => 0, 'surplusTime' => $surplusTime];
            }
            DB::update("update nftrank set end_time= $now_time,score=$score,hero=$heroid,weapon=$weaponid,uid='$uuid' where adress = ?", [$adress]);
            return ['result' => 0, 'surplusTime' => $surplusTime];
        }

        $insert = DB::insert(
            'insert into nftrank (uid,adress,hero,weapon,ct_time,end_time,score,round,playCnt,buy,token,version) values (?,?,?,?,?,?,?,?,?,?,?,?)',
            [$uuid, $adress, $heroid, $weaponid, $ct_time,$now_time,$score,0,0,"0","0",$version]
        );
        return ['result' => 0, 'surplusTime' => $surplusTime];
    }


   //上传RANK积分的同时，同步数据
   function syncNFTRankPlayerInfo(Request $request)
   {
       $input = $request->all();
       if (empty($input)) {
           return 'empty data';
       }

       $dateTime = new \DateTime();
       $now_time = $dateTime->getTimestamp();
       
       //剩余时间设计两周时间，06/14截止
       $surplusTime = 1688832000- $now_time;

       if($surplusTime < 0){
           //本轮已经结束
           return ['result' => 2, 'surplusTime' => 0];
       }

       $adress = $input['a']; //玩家钱包地址

       //-----------2023/6/23 新增加4个字段----------//
        $playCnt = $input['p'];
        $buy = urldecode($input['b']);
        $token = urldecode($input['t']);   
        $version = urldecode($input['v']);//当前版本号带abcode
       //--------------------------------------------//

       $md5Sign = $input['s'];
       //-------验签------//
       $md5key = '3ef80b3354494c8f29e78030b70ad9d4';
       ksort($input);
       foreach ($input as $k => $v) {
           if ($k == 's') {
               continue;
           }
           $tmp[] = $k . '=' . urldecode($v);
       }
       $str = implode('&', $tmp) . $md5key;

       //创建时间
       $ct_time = $now_time;

       $paySqlArray = DB::select('select * from nftrank where adress = ?', [$adress]);

    //    if($md5Sign!=md5($str)){ //记录作弊玩家 round = -1
    //         if (!empty($paySqlArray)) {
    //             DB::update("update nftrank set end_time= $now_time,round= '-1',buy='$buy',playCnt=$playCnt,token='$token',version='$version' where adress = ?", [$adress]);
    //         }
    //         else{
    //             $insert = DB::insert(
    //                 'insert into nftrank (uid,adress,hero,weapon,ct_time,end_time,score,round,playCnt,buy,token,version) values (?,?,?,?,?,?,?,?,?,?,?,?)',
    //                 ["0", $adress, 0, 0, $ct_time,$now_time,0,-1,$playCnt,$buy,$token,$version]
    //             );
    //         }
    //        return ['result' => 1, 'surplusTime' => $surplusTime];
    //    }

       if (!empty($paySqlArray)) {
            $sql =  $paySqlArray[0];
            $round = $sql->round;
            if(!empty($sql->token)){
                //上次普通体力
                $daliyPhysical_old = (int)explode("_", $sql->token)[2];
                //本次上传的普通体力
                $daliyPhysical_new = (int)explode("_", $token)[2];

                //新上传了普通体力恢复
                if($daliyPhysical_new==20 && $daliyPhysical_old!=20){
                    if($round >=0){
                        //判断离上次更新的时间
                        $passTime = $now_time - $round;
                        if($passTime >= 80000)
                        {
                            $round = $now_time; //正常恢复
                            DB::update("update nftrank set end_time= $now_time,buy='$buy',playCnt=$playCnt,round=$round,token='$token',version='$version' where adress = ?", [$adress]);
                            return ['result' => 0, 'surplusTime' => $surplusTime];
                        }
                        else{
                            if($adress=="0x3100cd3745acbc022C62AF11c1d5Fd3678f1e463" || $playCnt > 2000){
                                //修改本地时间的20体力
                                $round = min(-1,$sql->round - 1);
                                DB::update("update nftrank set end_time= $now_time,buy='$buy',playCnt=$playCnt,round=$round,version='$version' where adress = ?", [$adress]);
                                return ['result' => 0, 'surplusTime' => $surplusTime];
                            }
                        }
                    }
                    else{
                        //修改本地时间的20体力
                        $round = min(-1,$sql->round - 1);
                        DB::update("update nftrank set end_time= $now_time,buy='$buy',playCnt=$playCnt,round=$round,version='$version' where adress = ?", [$adress]);          
                        return ['result' => 0, 'surplusTime' => $surplusTime];
                    }
                }

                //上次钻石体力
                $buyPhysical_old = explode("_", $sql->token)[1];
                //本次上传的钻石体力
                $buyPhysical_new = explode("_", $token)[1];

                //本次上传的钻石
                $diamond_new = explode("_", $token)[0];

                //钻石体力没变化
                if($buyPhysical_old==$buyPhysical_new){ 
                    //上次购买体力数据
                    $buy_Data_old = explode("#", $sql->buy)[1];
                    $buy_10_old = explode("_", $buy_Data_old)[0];
                    $buy_40_old = explode("_", $buy_Data_old)[1];

                    //本次上传的购买体力数据
                    $buy_Data_new = explode("#", $buy)[1];
                    $buy_10_new = explode("_", $buy_Data_new)[0];
                    $buy_40_new = explode("_", $buy_Data_new)[1];

                    $new_buy_physical = $buyPhysical_old;
                    if($buy_10_old=="0" && $buy_10_new=="1"){
                        //消耗10钻石购买了10体力
                        $new_buy_physical = $buyPhysical_old + 10;
                    }

                    if($buy_40_old=="0" && $buy_40_new=="1"){
                        //消耗40钻石购买了50体力
                        $new_buy_physical = $buyPhysical_old + 50;
                    }

                    $token = $diamond_new ."_".$new_buy_physical."_".$daliyPhysical_new;
                    DB::update("update nftrank set end_time= $now_time,buy='$buy',playCnt=$playCnt,token='$token',version='$version' where adress = ?", [$adress]);
                    return ['result' => 0, 'surplusTime' => $surplusTime];
                }
            }
            
            DB::update("update nftrank set end_time= $now_time,buy='$buy',playCnt=$playCnt,token='$token',version='$version' where adress = ?", [$adress]);
            return ['result' => 0, 'surplusTime' => $surplusTime];
        }

       $insert = DB::insert(
            'insert into nftrank (uid,adress,hero,weapon,ct_time,end_time,score,round,playCnt,buy,token,version) values (?,?,?,?,?,?,?,?,?,?,?,?)',
            ["0", $adress, 0, 0, $ct_time,$now_time,0,0,$playCnt,$buy,$token,$version]
        );

       return ['result' => 0, 'surplusTime' => $surplusTime];
   }


    //获取密钥
    function requestPkey(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }

        $uuid = $input['uuid']; //uuid

        $country = $input['country'] || ""; //country

        $adress = $input['adress']; //玩家钱包地址

        $md5key = '3ef80b3354494c8f29e78030b70ad9d4';

        $dateTime = new \DateTime();
        $now_time = $dateTime->getTimestamp();

        //剩余时间设计两周时间，07/9截止
        $startTime = 1687104000;
        $end_time = 1688832000;
        // $surplusTime = $end_time - $now_time;
        $surplusTime = -1;
        // $data["surplusTime"] = $surplusTime;
        //标题
        $tittle = "SEASON RANK";
        //描述
        $info = "";
        //额外奖励：社区，问卷
        $rewardst = [2,2];
        //奖金信息
        $rewardsU = [[10,500,250,150,100,80,60,50,40,30,20],[100,0],[200,0]];
        $rewardsM = [[10,"200k","100k","90k","80k","70k","60k","50k","40k","30k","20k"],[100,"1k"],[200,"500"]];

        //奖金标题
        $rewardsTittle = 500;

        $scores = [25,15,10,5,-4,-9,-13,-20];
        // $data = [$startTime,$surplusTime,$tittle,$info,$rewardst,$rewards,$scores];
        $data = [$startTime."|".$end_time,$surplusTime,$tittle,$info,$rewardst,$rewardsU,$rewardsM,$rewardsTittle,$scores];

        //附加玩家的一些数据
        $role = [];
        if (empty($adress)){
            //从UUID获取信息
            if (empty($uuid) || $uuid==0){
            }
            else{
               //找到自己的数据
                $sqlArray = DB::select('select * from nftrank where uid = ?', [$uuid]);
                if (!empty($sqlArray)) {
                    $sql = $sqlArray[0];
                    if($sql->round<0){
                        //玩家重新上线，恢复正常游玩
                        //DB::update("update nftrank set round=0 where adress = ?", [$uuid]);
                    }
                    $role = [
                        $sql->playCnt,
                        $sql->token,
                        $sql->buy,
                        $sql->adress,
                    ];
                }
            }
        }
        else{
            if($adress=="0x6a1Ac5899d470DAC1a43D2b17e9953fdF39D702a" &&$country=="IN"){
                return ['result' => 200, 'key' => $md5key,'data' => $data , 'role' => $role];  
            }

            //找到自己的数据
            $sqlArray = DB::select('select * from nftrank where adress = ?', [$adress]);
            if (!empty($sqlArray)) {
                $sql = $sqlArray[0];
                if($sql->round<0){
                    //玩家重新上线，恢复正常游玩
                    //DB::update("update nftrank set round=0 where adress = ?", [$adress]);
                }
                $role = [
                    $sql->playCnt,
                    $sql->token,
                    $sql->buy,
                    $sql->adress,
                ];
            }
        }
        return ['result' => 200, 'key' => $md5key,'data' => $data , 'role' => $role];  
    }

    //检查版本
    function checkVersion(Request $request)
    {
        $input = $request->all();

        if (empty($input)) {
            return 'empty data';
        }

        $uuid = $input['uuid']; //uuid

        $country = $input['country'] || ""; //country

        $adress = $input['adress']; //玩家钱包地址

        $path = "/version/{$input['platformid']}_0.txt";
        $data = [];
        if (Storage::disk('onekey')->exists($path)) {
            $text = Storage::disk('onekey')->get($path);
            $data[] = [
                'result' => 200,
                'data' => $text,
            ];
        } else {
            $data[] = [
                'result' => 0,
                'data' => "",
            ];
        }
        return $data[0];
    }

}




    
   


