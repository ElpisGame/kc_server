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

class GameHallController extends Controller
{

    //公告文本
    function announce(Request $request)
    {
        $input = $request->all();

        $version_path = "/announcement/{$input['platformid']}_0.txt";
        $text = Storage::disk('onekey')->get($version_path);
        $ver = explode(";", $text)[0];

        $path = "/announcement/{$input['platformid']}_$ver.txt";
        $data = [];
        if (Storage::disk('onekey')->exists($path)) {
            $text = Storage::disk('onekey')->get($path);
            $data[] = [
                'result' => 1,
                'data' => $text,
            ];
        } else {
            $data[] = [
                'result' => 0,
                'data' => "",
            ];
        }
        return $data;
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


    //登录获取服务器公告信息
    function getPlayerServerInfo(Request $request)
    {
        $input = $request->all();
        $path = "/announcement/{$input['platformid']}_0.txt";
        $data = [];
        if (Storage::disk('onekey')->exists($path)) {
            $text = Storage::disk('onekey')->get($path);
            $ns = explode(';', $text);
            return ['result' => 1, 'ns' => $ns[0]];
        } else {
            return ['result' => 1, 'ns' => 0];
        }
    }


    /**
     * 安卓平台登录验证
     */
    function verifyLoginAndroid(Request $request)
    {

        $input = $request->all();
        $userId = $input['userid'];

        //登录验证
        $arr_post = array(
            'user_id' => $userId,
        );

        $url = "http://cms.mycente.com/userRoute/verify_user_id";

        // print("##################");
        // echo($url);

        $post_data = http_build_query($arr_post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($ch);
        curl_close($ch);
        // echo ($data);

        $dataArray = json_decode($data, true);

        $resultCode = isset($dataArray['resultCode']) ? $dataArray['resultCode'] : 0;
        $resultDesc = isset($dataArray['resultDesc']) ? $dataArray['resultDesc'] : "";

        if ($resultCode == 100) { //这里应该判断一下返回结果

            // $role_name = $input['name']; //用户名
            // $channel = $input['channel']; //渠道
            $channel = 1;
            // $platform = $input['platform']; //平台
            $platform = 'android';

            $dateTime = new \DateTime();
            $now_time = $dateTime->getTimestamp();

            //存在记录就更新,不存在则插入
            $login = DB::select('select * from login where user_id = ?', [$userId]);

            if (empty($login)) {
                DB::insert('insert into login (user_id,role_c_time,login_time,channel,platform) values (?,?,?,?,?)', [$userId, $now_time, $now_time, $channel, $platform]);
            } else {
                //查找玩家信息
                // $player = Player::query()->where("account", $userId)->first();
                // if (!empty($player)) {
                //     DB::update("update login set role_id=$player->dbid,role_level=$player->level,login_time=$now_time where user_id =?", [$userId]);
                // } else {
                //     // echo("找不到account的玩家---".$userId);
                // }
                DB::update("update login set login_time=$now_time where user_id =?", [$userId]);
            }
            return ['result' => 1, 'data' => $resultDesc];
        } else {
            return ['result' => $resultCode, 'data' => $resultDesc];
        }
    }


    /**
     * 平台登录回调+验证登录
     */
    function verifyLogin(Request $request)
    {
        //先验签 OPENSSL_ALGO_SHA1
        //签名
        $input = $request->all();
        $sign = $input['sign'];
        // echo($sign);
        //剩余订单信息数据进行字典排序,默认升序
        foreach ($input as $key => $value) {
            if ($key == 'sign') {
                continue;
            }
            $arr[$key] = $key;
        }
        sort($arr);
        //将Key和Value拼接,得到待签名字符串
        $str = "";
        foreach ($arr as $k => $v) {
            $str = $str . "&" . $arr[$k] . "=" . $input[$v];
        }
        $str = substr($str, 1);

        // print("################");
        // echo($str);

        // print("################");
        // echo sha1($str);

        if (sha1($str) == $sign) {
            //登录验证
            //公钥(和验签公钥一致)
            $filePath = "/key_kuchang/secret_key.txt";
            if (!Storage::disk('onekey')->exists($filePath)) {
                return 100;
            }
            $publicKey = Storage::disk('onekey')->get($filePath);
            $str = 'game_id=' . $input['game_id'] . '&' . 'user_id=' . $input['user_id'] . '&' . $publicKey;

            //----测试------//
            // $input['game_id'] = 'g32122186071161910';
            // $str = 'game_id=g32122186071161910&user_id=5ed271fefddcaa6f79a5e5ee&'.$publicKey;
            //----------//
            // print("##################");
            // echo($str);

            //md5.32位小写加密
            $sign = md5($str);

            // print("##################");
            // echo($sign);

            //登录验证
            $arr_post = array(
                'user_id' => $input['user_id'],
                'sign' => $sign
            );

            $url = "http://cms.mycente.com/h5game/{$input['game_id']}/user/verify_user_id2";

            // print("##################");
            // echo($url);

            $post_data = http_build_query($arr_post);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            $data = curl_exec($ch);
            curl_close($ch);
            // echo ($data);

            $dataArray = json_decode($data, true);

            $resultCode = isset($dataArray['resultCode']) ? $dataArray['resultCode'] : 0;

            if ($resultCode == 100) { //这里应该判断一下返回结果
                $userId = $input['user_id'];
                $role_name = $input['name'];

                $dateTime = new \DateTime();
                $now_time = $dateTime->getTimestamp();

                //存在记录就更新,不存在则插入
                $login = DB::select('select * from login where user_id = ?', [$userId]);

                if (empty($login)) {
                    DB::insert('insert into login (user_id,role_c_time,login_time,role_name) values (?,?,?,?)', [$userId, $now_time, $now_time, $role_name]);
                } else {
                    DB::update("update login set login_time=$now_time where user_id =?", [$userId]);
                }
                // return redirect("http://game1.coljoy.com/client/release/index.html?userId={$userId}");
                return redirect("http://game1.coljoy.com/client/release/index.html?userId={$userId}&v=$now_time");
                // $url2 = "http://game1.coljoy.com/client/release/index.html?userId={$userId}";
                // header("Location: $url2");
                // exit();
            } else {
                // print_r($dataArray);
            }
        }
    }

    /**
     * 验证返回的签名是否正确
     */
    function verifyRespondSign(Request $request, $signature_flag = OPENSSL_ALGO_SHA256) //'SHA256'
    {
        $input = $request->all();
        //验证价格
        $amount = $input['sale_price'];
        $goodsName = $input['goods_name'];

        //商品价格
        $price = 0;
        $text = Storage::disk('onekey')->get('payItems.txt');
        $goods = explode("\n", $text);
        foreach ($goods as $key => $value) {
            $data = explode(";", $value);
            if ($data[3] == $goodsName) {
                $price = $data[2];
                break;
            }
        }
        // $price = 0.01;
        if ($price != $amount) {
            // echo ("error price");
            return 'fail';
        }
        // echo("true price");
        //签名
        $sign = $input['sign'];

        //游戏内订单号
        $rechargeNo = $input['app_trade_no'];
        // echo($sign);
        //剩余订单信息数据进行字典排序,默认升序
        foreach ($input as $key => $value) {
            if ($key == 'sign') {
                continue;
            }
            $arr[$key] = $key;
        }
        sort($arr);
        //将Key和Value拼接,得到待签名字符串
        $str = "";
        foreach ($arr as $k => $v) {
            $str = $str . "&" . $arr[$k] . "=" . $input[$v];
        }
        $str = substr($str, 1);
        
        // $str = strtr(substr($str,1)," ","");
        // preg_replace('/\s/', '', $str);

        // print("################");
        // echo($str);

        $filePath = "/key_kuchang/pbk.pem";
        if (!Storage::disk('onekey')->exists($filePath)) {
            return 'fail';
        }

        $publicKey = Storage::disk('onekey')->get($filePath);

        $publicKey = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($publicKey, 64, "\n", true) . "\n-----END PUBLIC KEY-----";

        $pubkeyid = openssl_get_publickey($publicKey);
        if (!is_resource($pubkeyid)) {
            return 'fail';
        }

        // $result = openssl_public_decrypt($sign,$newsource,$pubkeyid);
        // var_dump($result);
        // echo "String decrypt : $newsource";
        // OPENSSL_ALGO_SHA1

        $signature = base64_decode($sign);

        // print("signature_flag-->".$signature_flag."  ");

        //SHA256withRAS
        $ok = openssl_verify($str, $signature, $pubkeyid, $signature_flag);

        openssl_free_key($pubkeyid);
        if ($ok == 1) {
            //验签成功,修改order记录
            $paySqlarr = DB::select('select * from orders where cp_no = ?', [$rechargeNo]);

            if (empty($paySqlarr)) {
                // echo $rechargeNo;
                return 'fail';
            }
            $paySql = $paySqlarr[0];
            //如果已经订单已经完成,不需要操作
            if ($paySql->complete == 1) {
                return 'success';
            }

            $dbid = $paySql->dbid;
            $serverid = $paySql->serverid;
            $goodsid = $paySql->goodsid;

            // echo  $dbid."---".$serverid."--". $goodsid;
            //完成时间
            $dateTime = new \DateTime();
            $end_time = $dateTime->getTimestamp();

            $pay_channel = $input['pay_channel'];
            $app_trade_no = $input['app_trade_no'];
            $out_trade_no = $input['out_trade_no'];

            //完成订单
            // DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,app_trade_no=$app_trade_no,out_trade_no=$out_trade_no,sign= $sign,complete=1 where cp_no =?",[$rechargeNo]);

            DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,app_trade_no=$app_trade_no,out_trade_no='$out_trade_no',complete=1 where cp_no =?", [$rechargeNo]);

            //这里需要区分游戏服

            //发货
            // DB::insert('insert into pay (dbid,serverid,playerid,goodsid) values (?,?,?,?)', [$dbid, $serverid, $dbid, $goodsid]);
            $this->delivergoodsByServer($dbid, $serverid, $goodsid);
            // echo ($data);

            // $dataArray = json_decode($data, true);
            // $resultCode = isset($dataArray['resultCode']) ? $dataArray['resultCode'] : 0;

            return 'success';
        }
        return 'fail';
    }

    //发起支付
    function startPay(Request $request)
    {
        $input = $request->all();

        $dbid = $input['dbid'];
        $account = $input['account'];
        $serverid = $input['serverid'];

        //orders表是跨服表，account为平台分发的唯一标识

        //查询上次未完成订单.（已收到发货通知，验签成功未发货）
        $paySqlArray = DB::select('select * from orders where account = ? and is_verify_sign = ? and complete = ?', [$account, 1, 0]);
        if (!empty($paySqlArray)) {
            //有未发货的订单 array
            // print_r($paySql);
            $cnt = $paySqlArray . count();
            $old_order = false;
            for ($i = 0; $i < $cnt; $i++) {
                $paySql = $$paySqlArray[i];
                //验签成功
                if ($paySql['verify_result'] == 1) {
                    $old_order = true;
                    //发货,插入一条记录，游戏服延迟1s轮询处理并删除
                    // DB::insert('insert into pay (dbid,serverid,playerid,goodsid) values (?,?,?,?)', [$player->dbid, $player->serverid, $player->dbid, $paySql['goodsid']]);
                    $this->delivergoodsByServer($dbid, $serverid, $goodsid);
                    if ($i < $cnt - 1) {
                        sleep(1);
                    }
                } else //验签失败(联系客服)
                {
                    return ['result' => 1000, 'data' => "联系客服"];
                }
            }
            if ($old_order) {
                return ['result' => 200, 'data' => "您有一条未处理的订单,请稍后.."];
            }
        } else {
            //只有订单号,没有sign认为是未支付

            //----插入新的一条数据,无默认值的字段都需要初始化----//
            //订单号
            $rechargeNo = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            //商品id
            $goodsId = $input['goodsId'];
            //商品名称
            $goodsName = $input['goodsName'];
            //商品价格
            $price = 0;
            $text = Storage::disk('onekey')->get('payItems.txt');
            $goods = explode("\n", $text);
            foreach ($goods as $key => $value) {
                $data = explode(";", $value);
                if ($data[0] == $goodsId) {
                    $price = $data[2];
                    break;
                }
            }
            //$price = 0.01;
            //创建时间
            $dateTime = new \DateTime();
            $ct_time = $dateTime->getTimestamp();

            $insert = DB::insert(
                'insert into orders (dbid,serverid,account,cp_no,goodsId,goodsName,ct_time) values (?,?,?,?,?,?,?)',
                [$dbid, $serverid, $account, $rechargeNo, $goodsId, $goodsName, $ct_time]
            );

            $filePath = "/key_kuchang/secret_key.txt";
            if (!Storage::disk('onekey')->exists($filePath)) {
                return ['result' => 300, 'data' => "找不到secret_key文件"];
            }
            $secret_key = Storage::disk('onekey')->get($filePath);

            return ['result' => 1, 'data' => $rechargeNo, 'price' => $price, 'key' => $secret_key];
            //-----------------------------------------------//
        }
    }

    //酷畅实名信息查询
    function getUserauthenInfo(Request $request)
    {
        $input = $request->all();


        $arr_post = array(
            'user_id' => $input['user_id'],
        );

        $url = "https://cms.mycente.com/userRoute/verify_NPPA_ID_info";

        // print("##################");
        // echo($url);

        $post_data = http_build_query($arr_post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($ch);
        curl_close($ch);
        //echo ($data);

        $dataArray = json_decode($data, true);

        $resultCode = isset($dataArray['resultCode']) ? $dataArray['resultCode'] : 0;
        $resultDesc = isset($dataArray['resultDesc']) ? $dataArray['resultDesc'] : "";
        $info = isset($dataArray['data']) ? $dataArray['data'] : [];


        return ['result' => $resultCode, 'data' => $info, 'resultDesc' => $resultDesc];
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

    //获取游戏服列表
    function getServerInfo(Request $request)
    {
        $input = $request->all();
        $platformid = $input['platformid'];

        $data = $this->getServerlist();

        if (empty($data)) {
            return ['result' => 300, 'data' => "找不到serverlist文件"];
        }

        $arr = [];
        foreach ($data as $key => $value) {
            if ($value['platformid'] == $platformid) {
                $arr[] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'ip' => $value['ip'],
                    'port_1' => $value['port_1'],
                    'port_2' => $value['port_2'],
                    'platformid' => $value['platformid'],
                ];
            }
        }

        return ['result' => 1, 'data' => $arr];
    }

    function delivergoodsByServer($dbid, $serverid, $goodsid)
    {
        $arr_post = array(
            'dbid' => $dbid,
            'serverid' => $serverid,
            'goodsid' => $goodsid
        );

        $serverlist = $this->getServerlist();

        if (empty($serverlist)) {
            return 'fail';
        }

        $url = "";
        foreach ($serverlist as $server) {
            $str = substr($server["id"], 3);
            $id = (int)$str;
            if ($id == $serverid) {
                $url = "http://{$server['ip']}:{$server['port_2']}/api/game/delivergoods";
            }
        }
        // print("##################");
        // echo($url);
        // print("##################");

        $post_data = http_build_query($arr_post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($ch);
        curl_close($ch);
    }


    //----------------------------------QUICK SDK-BEGIN-------------------------------//
    //验证用户信息接口--绘梦
    function checkUserInfoForHM(Request $request)
    {

        $input = $request->all();

        $token = $input['token'];
        $product_code = $input['product_code'];
        $uid = $input['uid'];

        $channel_code = $input['platformid'];
        //登录验证
        $arr_post = array(
            'token' => $token,
            'product_code' => $product_code,
            'uid' => $uid,
        );

        $url = "http://checkuser.quickapi.net/v2/checkUserInfo";

        // print("##################");
        // echo($url);

        $post_data = http_build_query($arr_post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($ch);
        curl_close($ch);
        // echo ($data);

        if ($data == '1') { //这里应该判断一下返回结果
            $userId = $uid;
            // $role_name = $input['name']; //用户名
            $channel = $channel_code; //渠道
            // $channel = 1; 
            // $platform = $input['platform']; //平台
            $platform = 'H5-HUIMENG';

            $dateTime = new \DateTime();
            $now_time = $dateTime->getTimestamp();

            //存在记录就更新,不存在则插入
            $login = DB::select('select * from login where user_id = ?', [$userId]);

            if (empty($login)) {
                DB::insert('insert into login (user_id,role_c_time,login_time,channel,platform) values (?,?,?,?,?)', [$userId, $now_time, $now_time, $channel, $platform]);
            } else {
                //查找玩家信息
                // $player = Player::query()->where("account", $userId)->first();
                // if (!empty($player)) {
                //     DB::update("update login set role_id=$player->dbid,role_level=$player->level,login_time=$now_time where user_id =?", [$userId]);
                // } else {
                //     // echo("找不到account的玩家---".$userId);
                // }
                DB::update("update login set login_time=$now_time where user_id =?", [$userId]);
            }
            return ['result' => 1, 'data' => "success"];
        } else {
            return ['result' => $resultCode, 'data' => "failure"];
        }
    }

    /**
     * 验证返回的签名是否正确--绘梦
     */
    function verifyRespondSignForHM(Request $request)
    {
        $input = $request->all();

        $nt_data = $input['nt_data'];

        // echo("true price");
        //签名
        $sign = $input['sign'];
        $md5Sign = $input['md5Sign'];

        $md5key = 'f66ph6ven0dthfnctbrhcvtoyryigktl';
        if (md5($nt_data . $sign . $md5key) == $md5Sign) {
            //验签成功,修改order记录

            //解密nt_data获取订单信息
            $tradeInfo = $this->decode($nt_data, '04731100360962639632340674557237');
            $tradeInfo = str_replace("<", "", $tradeInfo);
            $tradeInfo = str_replace(">", "", $tradeInfo);
            $tradeInfo = str_replace("_", "", $tradeInfo);

            // <_i_s___t_e_s_t_>_0_<_/_i_s___t_e_s_t_>
            // "gameorder2021112533781/gameorder"
            //XML标签配置
            $xmlTag = array(
                'status',
                'gameorder',    //游戏内订单号，原样返回
                'channelorder', //渠道订单号
                'orderno',      //QK 订单号
                'amount',       //充值金额
                'channeluid',   //渠道用户id
                'channelname',  //渠道名
                'channel',      //渠道id
                // 'istest',        //0:正式，1:测试
                // 'paytime',          //支付时间
                // 'message',           
                // 'extrasparams',
            );

            $arr = array();
            foreach ($xmlTag as $x) {
                preg_match_all("/" . $x . ".*\/" . $x . "/", $tradeInfo, $temp);
                $tradeInfo = str_replace($temp[0][0], "", $tradeInfo);
                $temp[0][0] = str_replace($x, "", $temp[0][0]);
                $temp[0][0] = str_replace("/", "", $temp[0][0]);
                $arr[] = $temp[0];
            }

            //游戏内订单号
            $rechargeNo = $arr[1][0];

            $paySqlarr = DB::select('select * from orders where cp_no = ?', [$rechargeNo]);

            if (empty($paySqlarr)) {
                // echo $rechargeNo;
                return 'fail';
            }
            $paySql = $paySqlarr[0];
            //如果已经订单已经完成,不需要操作
            if ($paySql->complete == 1) {
                return 'success';
            }

            $dbid = $paySql->dbid;
            $serverid = $paySql->serverid;
            $goodsid = $paySql->goodsid;

            // echo  $dbid."---".$serverid."--". $goodsid;
            //完成时间
            $dateTime = new \DateTime();
            $end_time = $dateTime->getTimestamp();

            $pay_channel = $arr[7][0];
            $app_trade_no = $arr[2][0];
            $out_trade_no = $arr[3][0];

            $amount = $arr[4][0];

            //---验证价格---
            // echo ("amount ".$amount);
            //商品价格
            $price = 0;
            $text = Storage::disk('onekey')->get('payItems.txt');
            $goods = explode("\n", $text);
            foreach ($goods as $key => $value) {
                $data = explode(";", $value);
                if ($data[0] == $goodsid) {
                    $price = $data[2];
                    break;
                }
            }
            // $price = 0.01;
            if ($price != $amount) {
                // echo ("error price");
                return 'fail';
            }
            //-------------

            // if(1>0){
            //     print("rechargeNo: ".$rechargeNo." pay_channel: ".$pay_channel." amount: ".$amount." app_trade_no: ".$app_trade_no." out_trade_no: ".$out_trade_no);
            //     return '---fail---';
            // }

            //完成订单
            // DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,app_trade_no=$app_trade_no,out_trade_no=$out_trade_no,sign= $sign,complete=1 where cp_no =?",[$rechargeNo]);

            DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,app_trade_no='$app_trade_no',out_trade_no='$out_trade_no',complete=1 where cp_no =?", [$rechargeNo]);

            //这里需要区分游戏服

            //发货
            // DB::insert('insert into pay (dbid,serverid,playerid,goodsid) values (?,?,?,?)', [$dbid, $serverid, $dbid, $goodsid]);
            $this->delivergoodsByServer($dbid, $serverid, $goodsid);
            // echo ($data);

            // $dataArray = json_decode($data, true);
            // $resultCode = isset($dataArray['resultCode']) ? $dataArray['resultCode'] : 0;

            return 'success';
        }
        return 'fail';
    }


    //验证用户信息接口--胜良
    function checkUserInfoForSL(Request $request)
    {

        $input = $request->all();

        $token = $input['token'];
        $product_code = $input['product_code'];
        $uid = $input['uid'];

        $channel_code = $input['platformid'];
        //登录验证
        $arr_post = array(
            'token' => $token,
            'product_code' => $product_code,
            'uid' => $uid,
        );

        $url = "http://checkuser.quickapi.net/v2/checkUserInfo";

        // print("##################");
        // echo($url);

        $post_data = http_build_query($arr_post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($ch);
        curl_close($ch);
        // echo ($data);

        if ($data == '1') { //这里应该判断一下返回结果
            $userId = $uid;
            // $role_name = $input['name']; //用户名
            $channel = $channel_code; //渠道
            // $channel = 1; 
            // $platform = $input['platform']; //平台
            $platform = 'H5-SHENGLIANG';

            $dateTime = new \DateTime();
            $now_time = $dateTime->getTimestamp();

            //存在记录就更新,不存在则插入
            $login = DB::select('select * from login where user_id = ?', [$userId]);

            if (empty($login)) {
                DB::insert('insert into login (user_id,role_c_time,login_time,channel,platform) values (?,?,?,?,?)', [$userId, $now_time, $now_time, $channel, $platform]);
            } else {
                //查找玩家信息
                // $player = Player::query()->where("account", $userId)->first();
                // if (!empty($player)) {
                //     DB::update("update login set role_id=$player->dbid,role_level=$player->level,login_time=$now_time where user_id =?", [$userId]);
                // } else {
                //     // echo("找不到account的玩家---".$userId);
                // }
                DB::update("update login set login_time=$now_time where user_id =?", [$userId]);
            }
            return ['result' => 1, 'data' => "success"];
        } else {
            return ['result' => $resultCode, 'data' => "failure"];
        }
    }

    /**
     * 验证返回的签名是否正确--胜良
     */
    function verifyRespondSignForSL(Request $request)
    {
        $input = $request->all();

        $nt_data = $input['nt_data'];

        // echo("true price");
        //签名
        $sign = $input['sign'];
        $md5Sign = $input['md5Sign'];

        $md5key = 'j1bii8zi2fmqwvzrihergr7bp9ujsdec';
        if (md5($nt_data . $sign . $md5key) == $md5Sign) {
            //验签成功,修改order记录

            //解密nt_data获取订单信息
            $tradeInfo = $this->decode($nt_data, '50219258490682574717792653029774');
            $tradeInfo = str_replace("<", "", $tradeInfo);
            $tradeInfo = str_replace(">", "", $tradeInfo);
            $tradeInfo = str_replace("_", "", $tradeInfo);

            // <_i_s___t_e_s_t_>_0_<_/_i_s___t_e_s_t_>
            // "gameorder2021112533781/gameorder"
            //XML标签配置
            $xmlTag = array(
                'status',
                'gameorder',    //游戏内订单号，原样返回
                'channelorder', //渠道订单号
                'orderno',      //QK 订单号
                'amount',       //充值金额
                'channeluid',   //渠道用户id
                'channelname',  //渠道名
                'channel',      //渠道id
                // 'istest',        //0:正式，1:测试
                // 'paytime',          //支付时间
                // 'message',           
                // 'extrasparams',
            );

            $arr = array();
            foreach ($xmlTag as $x) {
                preg_match_all("/" . $x . ".*\/" . $x . "/", $tradeInfo, $temp);
                $tradeInfo = str_replace($temp[0][0], "", $tradeInfo);
                $temp[0][0] = str_replace($x, "", $temp[0][0]);
                $temp[0][0] = str_replace("/", "", $temp[0][0]);
                $arr[] = $temp[0];
            }

            //游戏内订单号
            $rechargeNo = $arr[1][0];

            $paySqlarr = DB::select('select * from orders where cp_no = ?', [$rechargeNo]);

            // var_dump($rechargeNo);
            if (empty($paySqlarr)) {
                // echo $rechargeNo;
                return 'fail';
            }
            $paySql = $paySqlarr[0];
            //如果已经订单已经完成,不需要操作
            if ($paySql->complete == 1) {
                return 'success';
            }

            $dbid = $paySql->dbid;
            $serverid = $paySql->serverid;
            $goodsid = $paySql->goodsid;

            // echo  $dbid."---".$serverid."--". $goodsid;
            //完成时间
            $dateTime = new \DateTime();
            $end_time = $dateTime->getTimestamp();

            $pay_channel = $arr[7][0];
            $app_trade_no = $arr[2][0];
            $out_trade_no = $arr[3][0];

            $amount = $arr[4][0];

            //---验证价格---
            // echo ("amount ".$amount);
            //商品价格
            $price = 0;
            $text = Storage::disk('onekey')->get('payItems.txt');
            $goods = explode("\n", $text);
            foreach ($goods as $key => $value) {
                $data = explode(";", $value);
                if ($data[0] == $goodsid) {
                    $price = $data[2];
                    break;
                }
            }
            // $price = 0.01;
            if ($price != $amount) {
                // echo ("error price");
                return 'fail';
            }
            //-------------


            // if(1>0){
            //     print("rechargeNo: ".$rechargeNo." pay_channel: ".$pay_channel." amount: ".$amount." app_trade_no: ".$app_trade_no." out_trade_no: ".$out_trade_no);
            //     return '---fail---';
            // }

            //完成订单
            // DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,app_trade_no=$app_trade_no,out_trade_no=$out_trade_no,sign= $sign,complete=1 where cp_no =?",[$rechargeNo]);

            DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,app_trade_no='$app_trade_no',out_trade_no='$out_trade_no',complete=1 where cp_no =?", [$rechargeNo]);

            //这里需要区分游戏服

            //发货
            // DB::insert('insert into pay (dbid,serverid,playerid,goodsid) values (?,?,?,?)', [$dbid, $serverid, $dbid, $goodsid]);
            $this->delivergoodsByServer($dbid, $serverid, $goodsid);
            // echo ($data);

            // $dataArray = json_decode($data, true);
            // $resultCode = isset($dataArray['resultCode']) ? $dataArray['resultCode'] : 0;

            return 'success';
        }
        // echo '验签失败';
        return 'fail';
    }


    //验证用户信息接口--水娱
    function checkUserInfoForSY(Request $request)
    {

        $input = $request->all();

        $token = $input['token'];
        $product_code = $input['product_code'];
        $uid = $input['uid'];

        $channel_code = $input['platformid'];
        //登录验证
        $arr_post = array(
            'token' => $token,
            'product_code' => $product_code,
            'uid' => $uid,
        );

        $url = "http://checkuser.quickapi.net/v2/checkUserInfo";

        // print("##################");
        // echo($url);

        $post_data = http_build_query($arr_post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($ch);
        curl_close($ch);
        // echo ($data);

        if ($data == '1') { //这里应该判断一下返回结果
            $userId = $uid;
            // $role_name = $input['name']; //用户名
            $channel = $channel_code; //渠道
            // $channel = 1; 
            // $platform = $input['platform']; //平台
            $platform = 'H5-SHENGLIANG';

            $dateTime = new \DateTime();
            $now_time = $dateTime->getTimestamp();

            //存在记录就更新,不存在则插入
            $login = DB::select('select * from login where user_id = ?', [$userId]);

            if (empty($login)) {
                DB::insert('insert into login (user_id,role_c_time,login_time,channel,platform) values (?,?,?,?,?)', [$userId, $now_time, $now_time, $channel, $platform]);
            } else {
                //查找玩家信息
                // $player = Player::query()->where("account", $userId)->first();
                // if (!empty($player)) {
                //     DB::update("update login set role_id=$player->dbid,role_level=$player->level,login_time=$now_time where user_id =?", [$userId]);
                // } else {
                //     // echo("找不到account的玩家---".$userId);
                // }
                DB::update("update login set login_time=$now_time where user_id =?", [$userId]);
            }
            return ['result' => 1, 'data' => "success"];
        } else {
            return ['result' => $resultCode, 'data' => "failure"];
        }
    }

    /**
     * 验证返回的签名是否正确--水娱
     */
    function verifyRespondSignForSY(Request $request)
    {
        $input = $request->all();

        $nt_data = $input['nt_data'];

        // echo("true price");
        //签名
        $sign = $input['sign'];
        $md5Sign = $input['md5Sign'];

        $md5key = 'e8z4aosbxtsvq2fsmkmt8cb1p2ifpbt4';
        if (md5($nt_data . $sign . $md5key) == $md5Sign) {
            //验签成功,修改order记录

            //解密nt_data获取订单信息
            $tradeInfo = $this->decode($nt_data, '34278033050293570768684786868782');
            $tradeInfo = str_replace("<", "", $tradeInfo);
            $tradeInfo = str_replace(">", "", $tradeInfo);
            $tradeInfo = str_replace("_", "", $tradeInfo);

            // <_i_s___t_e_s_t_>_0_<_/_i_s___t_e_s_t_>
            // "gameorder2021112533781/gameorder"
            //XML标签配置
            $xmlTag = array(
                'status',
                'gameorder',    //游戏内订单号，原样返回
                'channelorder', //渠道订单号
                'orderno',      //QK 订单号
                'amount',       //充值金额
                'channeluid',   //渠道用户id
                'channelname',  //渠道名
                'channel',      //渠道id
                // 'istest',        //0:正式，1:测试
                // 'paytime',          //支付时间
                // 'message',           
                // 'extrasparams',
            );

            $arr = array();
            foreach ($xmlTag as $x) {
                preg_match_all("/" . $x . ".*\/" . $x . "/", $tradeInfo, $temp);
                $tradeInfo = str_replace($temp[0][0], "", $tradeInfo);
                $temp[0][0] = str_replace($x, "", $temp[0][0]);
                $temp[0][0] = str_replace("/", "", $temp[0][0]);
                $arr[] = $temp[0];
            }

            //游戏内订单号
            $rechargeNo = $arr[1][0];

            $paySqlarr = DB::select('select * from orders where cp_no = ?', [$rechargeNo]);

            // var_dump($rechargeNo);
            if (empty($paySqlarr)) {
                // echo $rechargeNo;
                return 'fail';
            }
            $paySql = $paySqlarr[0];
            //如果已经订单已经完成,不需要操作
            if ($paySql->complete == 1) {
                return 'success';
            }

            $dbid = $paySql->dbid;
            $serverid = $paySql->serverid;
            $goodsid = $paySql->goodsid;

            // echo  $dbid."---".$serverid."--". $goodsid;
            //完成时间
            $dateTime = new \DateTime();
            $end_time = $dateTime->getTimestamp();

            $pay_channel = $arr[7][0];
            $app_trade_no = $arr[2][0];
            $out_trade_no = $arr[3][0];

            $amount = $arr[4][0];

            //---验证价格---
            // echo ("amount ".$amount);
            //商品价格
            $price = 0;
            $text = Storage::disk('onekey')->get('payItems.txt');
            $goods = explode("\n", $text);
            foreach ($goods as $key => $value) {
                $data = explode(";", $value);
                if ($data[0] == $goodsid) {
                    $price = $data[2];
                    break;
                }
            }
            // $price = 0.01;
            if ($price != $amount) {
                // echo ("error price");
                return 'fail';
            }
            //-------------


            // if(1>0){
            //     print("rechargeNo: ".$rechargeNo." pay_channel: ".$pay_channel." amount: ".$amount." app_trade_no: ".$app_trade_no." out_trade_no: ".$out_trade_no);
            //     return '---fail---';
            // }

            //完成订单
            // DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,app_trade_no=$app_trade_no,out_trade_no=$out_trade_no,sign= $sign,complete=1 where cp_no =?",[$rechargeNo]);

            DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,app_trade_no='$app_trade_no',out_trade_no='$out_trade_no',complete=1 where cp_no =?", [$rechargeNo]);

            //这里需要区分游戏服

            //发货
            // DB::insert('insert into pay (dbid,serverid,playerid,goodsid) values (?,?,?,?)', [$dbid, $serverid, $dbid, $goodsid]);
            $this->delivergoodsByServer($dbid, $serverid, $goodsid);
            // echo ($data);

            // $dataArray = json_decode($data, true);
            // $resultCode = isset($dataArray['resultCode']) ? $dataArray['resultCode'] : 0;

            return 'success';
        }
        // echo '验签失败';
        return 'fail';
    }


    /**
     * 解密方法
     * $strEncode 密文
     * $keys 解密密钥 为游戏接入时分配的 callback_key
     */
    public function decode($strEncode, $keys)
    {
        if (empty($strEncode)) {
            return $strEncode;
        }
        preg_match_all('(\d+)', $strEncode, $list);
        $list = $list[0];
        if (count($list) > 0) {
            $keys = self::getBytes($keys);
            for ($i = 0; $i < count($list); $i++) {
                $keyVar = $keys[$i % count($keys)];
                $data[$i] = $list[$i] - (0xff & $keyVar);
            }
            return self::toStrArray($data);
        } else {
            return $strEncode;
        }
    }


    /**
     * 转成字符数据
     */
    private static function getBytes($string)
    {
        $bytes = array();
        for ($i = 0; $i < strlen($string); $i++) {
            $bytes[] = ord($string[$i]);
        }
        return $bytes;
    }

    /**
     * 转化字符串
     */
    private static function toStrArray($bytes)
    {
        $str = '';
        foreach ($bytes as $ch) {
            // $str .= chr($ch);
            $str = $str . "_" . chr($ch);
        }
        return $str;
    }
    //----------------------------------QUICK SDK-END-------------------------------//

    function verifyMD5(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }
        // echo("true price");
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
    }

    //---------------------------------DUO YOU-------------------------------------//

    /**
     * 验证返回的签名是否正确--多游
     */
    function verifyRespondSignForDY(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }
        // echo("true price");
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
        if (md5($str) == $md5Sign) {
            //验签成功,修改order记录

            //游戏内订单号
            $rechargeNo = $input['game_order_id'];

            $paySqlarr = DB::select('select * from orders where cp_no = ?', [$rechargeNo]);

            if (empty($paySqlarr)) {
                // echo $rechargeNo;
                return 'fail';
            }
            $paySql = $paySqlarr[0];
            //如果已经订单已经完成,不需要操作
            if ($paySql->complete == 1) {
                return 'success';
            }

            $dbid = $paySql->dbid;
            $serverid = $paySql->serverid;
            $goodsid = $paySql->goodsid;

            // echo  $dbid."---".$serverid."--". $goodsid;
            //完成时间
            $dateTime = new \DateTime();
            $end_time = $dateTime->getTimestamp();

            $pay_channel = 6;
            $app_trade_no = $input['gather_order_id'];
            $out_trade_no = $input['gather_order_id'];

            $amount = $input['order_amount'];

            //---验证价格---
            // echo ("amount ".$amount);
            //商品价格
            $price = 0;
            $text = Storage::disk('onekey')->get('payItems.txt');
            $goods = explode("\n", $text);
            foreach ($goods as $key => $value) {
                $data = explode(";", $value);
                if ($data[0] == $goodsid) {
                    $price = $data[2] * 100; //多游价格单位为分
                    break;
                }
            }
            // $price = 0.01;
            if ($price != $amount) {
                // echo ("error price");
                return 'fail';
            }
            //-------------

            // if(1>0){
            //     print("rechargeNo: ".$rechargeNo." pay_channel: ".$pay_channel." amount: ".$amount." app_trade_no: ".$app_trade_no." out_trade_no: ".$out_trade_no);
            //     return '---fail---';
            // }

            //完成订单
            // DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,app_trade_no=$app_trade_no,out_trade_no=$out_trade_no,sign= $sign,complete=1 where cp_no =?",[$rechargeNo]);

            DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,app_trade_no='$app_trade_no',out_trade_no='$out_trade_no',complete=1 where cp_no =?", [$rechargeNo]);

            //这里需要区分游戏服

            //发货
            // DB::insert('insert into pay (dbid,serverid,playerid,goodsid) values (?,?,?,?)', [$dbid, $serverid, $dbid, $goodsid]);
            $this->delivergoodsByServer($dbid, $serverid, $goodsid);
            // echo ($data);

            // $dataArray = json_decode($data, true);
            // $resultCode = isset($dataArray['resultCode']) ? $dataArray['resultCode'] : 0;

            return 'success';
        }
        return 'fail';
    }
    //---------------------------------DUO YOU-------------------------------------//

    //---------------------------------JIA YOU 4399-------------------------------------//

    //验证用户信息接口--佳优
    function checkUserInfoForJY(Request $request)
    {
        $input = $request->all();

        $name = $input['name'];
        $uid = $input['uid'];
        $ext = $input['ext'];

        // foreach ($ext as $key => $value) {
        //    print($key);
        // }

        $channel_code = $input['platformid'];
        //登录验证
        $arr_post = array(
            'name' => urlencode($name),
            'uid' => urlencode($uid),
            'ext' => urlencode($ext),
        );

        $url = "http://fnapi.4399sy.com/sdk/api/login.php";

        // print("##################");
        // echo($url);

        $post_data = http_build_query($arr_post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($ch);
        curl_close($ch);
        // echo ($data);
        // print("##################");
        $dataArray = json_decode($data, true);
        $resultCode = isset($dataArray['code']) ? $dataArray['code'] : 0;
        $resultDesc = isset($dataArray['msg']) ? $dataArray['msg'] : "";
        $resultContent = isset($dataArray['content']) ? $dataArray['content'] : "";

 		if ($resultCode == '1') { //这里应该判断一下返回结果
            $userId = $uid;
            $userName = $uid;
            if (empty($resultContent)) {
                //返回内容为空
                // print("返回内容为空");

            } else {
                $userId = $resultContent["uid"];
                $userName = $resultContent["name"];
            }

            $channel = $channel_code; //渠道
            $platform = 'APK-JIAYOU';
            $dateTime = new \DateTime();
            $now_time = $dateTime->getTimestamp();

            //存在记录就更新,不存在则插入
            $login = DB::select('select * from login where user_id = ?', [$userId]);

            if (empty($login)) {
                DB::insert('insert into login (user_id,role_c_time,login_time,channel,platform) values (?,?,?,?,?)', [$userId, $now_time, $now_time, $channel, $platform]);
            } else {
                //查找玩家信息
                // $player = Player::query()->where("account", $userId)->first();
                // if (!empty($player)) {
                //     DB::update("update login set role_id=$player->dbid,role_level=$player->level,login_time=$now_time where user_id =?", [$userId]);
                // } else {
                //     // echo("找不到account的玩家---".$userId);
                // }
                DB::update("update login set login_time=$now_time where user_id =?", [$userId]);
            }
            return ['result' => 1, 'data' => "success", 'uid' => $userId, 'uName' => $userName, 'content' => $data];
        } else {
            return ['result' => $resultCode, 'data' => "failure"];
        }
    }

    /**
     * 验证充值返回的签名是否正确--佳优
     */
    function verifyRespondSignForJY(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }

        //订单支付状态
        $order_status = $input['order_status'];
        if ($order_status == 'F') {
            return -5;
        }

        // echo("true price");
        //签名
        $md5Sign = $input['sign'];
        $callback_info = $input['callback_info'];

        //-------验签------//
        $md5key = '5ef90b3354494c6f29e78030b70ad9c8';
        // client_id:1642493222219300
        // client_key:7882b4948eeadbe1bdc31c45f7ba7687
        // server_key:423c7c96dce1bf2b5af2a467f177bf38
        // pay key:226737fbe7939855493315e4d2118383
        $order_id = $input['order_id'];
        $game_id = $input['game_id'];
        $server_id = $input['server_id'];
        $fnpid = $input['fnpid'];
        $uid = $input['uid'];
        $pay_way = $input['pay_way'];
        $amount = $input['amount'];
        $callback_info = $input['callback_info'];
        $failed_desc = $input['failed_desc'];

        $str = $order_id . $game_id . $server_id . $fnpid . $uid . $pay_way . $amount . $callback_info . $order_status . $failed_desc . $md5key;

        if (md5($str) == $md5Sign) {
            //验签成功,修改order记录

            //游戏内订单号
            $rechargeNo = $callback_info;

            $paySqlarr = DB::select('select * from orders where cp_no = ?', [$rechargeNo]);

            if (empty($paySqlarr)) {
                return -5;
            }
            $paySql = $paySqlarr[0];
            //如果已经订单已经完成,不需要操作
            if ($paySql->complete == 1) {
                return 2; //订单重复
            }

            $dbid = $paySql->dbid;
            $serverid = $paySql->serverid;
            $goodsid = $paySql->goodsid;

            // echo  $dbid."---".$serverid."--". $goodsid;
            //完成时间
            $dateTime = new \DateTime();
            $end_time = $dateTime->getTimestamp();

            $pay_channel = $pay_way;
            $app_trade_no = $order_id;
            $out_trade_no = $order_id;

            //---验证价格---
            // echo ("amount ".$amount);
            //商品价格
            $price = 0;
            $text = Storage::disk('onekey')->get('payItems.txt');
            $goods = explode("\n", $text);
            foreach ($goods as $key => $value) {
                $data = explode(";", $value);
                if ($data[0] == $goodsid) {
                    $price = $data[2]; //单位：元
                    break;
                }
            }
            // $price = 0.01;
            if ($price != $amount) {
                // echo ("error price");
                return 'fail';
            }
            //-------------

            // if(1>0){
            //     print("rechargeNo: ".$rechargeNo." pay_channel: ".$pay_channel." amount: ".$amount." app_trade_no: ".$app_trade_no." out_trade_no: ".$out_trade_no);
            //     return '---fail---';
            // }

            //完成订单
            // DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,app_trade_no=$app_trade_no,out_trade_no=$out_trade_no,sign= $sign,complete=1 where cp_no =?",[$rechargeNo]);

            DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,app_trade_no='$app_trade_no',out_trade_no='$out_trade_no',complete=1 where cp_no =?", [$rechargeNo]);

            //这里需要区分游戏服

            //发货
            // DB::insert('insert into pay (dbid,serverid,playerid,goodsid) values (?,?,?,?)', [$dbid, $serverid, $dbid, $goodsid]);
            $this->delivergoodsByServer($dbid, $serverid, $goodsid);
            // echo ($data);

            // $dataArray = json_decode($data, true);
            // $resultCode = isset($dataArray['resultCode']) ? $dataArray['resultCode'] : 0;

            return 1; //成功
        }
        return -2; //签名验证失败
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
    function SendMsForJY(Request $request)
    {
        $input = $request->all();
        $business_id = $input['business_id'];
        $phone_number = $input['phone_number'];
        $code = $input['code'];
        //def send_sms(self, business_id, phone_number, sign_name, template_code, template_param=None):
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


    //---------------------------------微信小游戏-------------------------------------//
    //验证用户信息接口--微信
    function checkUserInfoForWX(Request $request)
    {
        $input = $request->all();

        $code = $input['code'];
        $appid = "wxee83c7ba94bee0ce";
        $appsecret = "570ce885bd7268edb185e1c0e8100b79";


        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$appsecret&js_code=$code&grant_type=authorization_code";

        // print("##################");
        // echo($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);

        $dataArray = json_decode($data, true);

        // var_dump($dataArray);

        $resultCode = isset($dataArray['errcode']) ? $dataArray['errcode'] : 0;
        $errmsg = isset($dataArray['errmsg']) ? $dataArray['errmsg'] : "";

        //---------------------------------------------
        if ($resultCode == 0) { //这里应该判断一下返回结果
            $userId = isset($dataArray['openid']) ? $dataArray['openid'] : "";
            $session_key = isset($dataArray['session_key']) ? $dataArray['session_key'] : "";
            
            if ($userId == "" || $session_key =="") {
                return ['result' => $resultCode, 'data' => "未获取到openid或者session_key"];
            }

            $channel = 9; //渠道
            $platform = 'WXGame';
            $dateTime = new \DateTime();
            $now_time = $dateTime->getTimestamp();

            //存在记录就更新,不存在则插入
            $login = DB::select('select * from login where user_id = ?', [$userId]);

            if (empty($login)) {
                DB::insert('insert into login (user_id,role_c_time,login_time,zone_name,channel,platform) values (?,?,?,?,?,?)', [$userId, $now_time, $now_time, $session_key ,$channel, $platform]);
            } else {
                DB::update("update login set login_time=$now_time where user_id =?", [$userId]);
            }
            return ['result' => 1, 'data' => "success", 'uid' => $userId];
        } else {
            return ['result' => $resultCode, 'data' => $errmsg];
        }
    }

    /**
     * 验证充值返回的签名是否正确--微信
     */
    function verifyRespondSignForWX(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }

        //订单支付状态
        $order_status = $input['order_status'];
        if ($order_status == 'F') {
            return -5;
        }

        // echo("true price");
        //签名
        $md5Sign = $input['sign'];
        $callback_info = $input['callback_info'];

        //-------验签------//
        $md5key = '5ef90b3354494c6f29e78030b70ad9c8';
        // client_id:1642493222219300
        // client_key:7882b4948eeadbe1bdc31c45f7ba7687
        // server_key:423c7c96dce1bf2b5af2a467f177bf38
        // pay key:226737fbe7939855493315e4d2118383
        $order_id = $input['order_id'];
        $game_id = $input['game_id'];
        $server_id = $input['server_id'];
        $fnpid = $input['fnpid'];
        $uid = $input['uid'];
        $pay_way = $input['pay_way'];
        $amount = $input['amount'];
        $callback_info = $input['callback_info'];
        $failed_desc = $input['failed_desc'];

        $str = $order_id . $game_id . $server_id . $fnpid . $uid . $pay_way . $amount . $callback_info . $order_status . $failed_desc . $md5key;

        if (md5($str) == $md5Sign) {
            //验签成功,修改order记录

            //游戏内订单号
            $rechargeNo = $callback_info;

            $paySqlarr = DB::select('select * from orders where cp_no = ?', [$rechargeNo]);

            if (empty($paySqlarr)) {
                return -5;
            }
            $paySql = $paySqlarr[0];
            //如果已经订单已经完成,不需要操作
            if ($paySql->complete == 1) {
                return 2; //订单重复
            }

            $dbid = $paySql->dbid;
            $serverid = $paySql->serverid;
            $goodsid = $paySql->goodsid;

            // echo  $dbid."---".$serverid."--". $goodsid;
            //完成时间
            $dateTime = new \DateTime();
            $end_time = $dateTime->getTimestamp();

            $pay_channel = $pay_way;
            $app_trade_no = $order_id;
            $out_trade_no = $order_id;

            //---验证价格---
            // echo ("amount ".$amount);
            //商品价格
            $price = 0;
            $text = Storage::disk('onekey')->get('payItems.txt');
            $goods = explode("\n", $text);
            foreach ($goods as $key => $value) {
                $data = explode(";", $value);
                if ($data[0] == $goodsid) {
                    $price = $data[2]; //单位：元
                    break;
                }
            }
            // $price = 0.01;
            if ($price != $amount) {
                // echo ("error price");
                return 'fail';
            }
            //-------------

            // if(1>0){
            //     print("rechargeNo: ".$rechargeNo." pay_channel: ".$pay_channel." amount: ".$amount." app_trade_no: ".$app_trade_no." out_trade_no: ".$out_trade_no);
            //     return '---fail---';
            // }

            //完成订单
            // DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,app_trade_no=$app_trade_no,out_trade_no=$out_trade_no,sign= $sign,complete=1 where cp_no =?",[$rechargeNo]);

            DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,app_trade_no='$app_trade_no',out_trade_no='$out_trade_no',complete=1 where cp_no =?", [$rechargeNo]);

            //这里需要区分游戏服

            //发货
            // DB::insert('insert into pay (dbid,serverid,playerid,goodsid) values (?,?,?,?)', [$dbid, $serverid, $dbid, $goodsid]);
            $this->delivergoodsByServer($dbid, $serverid, $goodsid);
            // echo ($data);

            // $dataArray = json_decode($data, true);
            // $resultCode = isset($dataArray['resultCode']) ? $dataArray['resultCode'] : 0;

            return 1; //成功
        }
        return -2; //签名验证失败
    }

    //---------------------------------微信小游戏-------------------------------------//


    //---------------------------------QQ小游戏-------------------------------------//
    //验证用户信息接口--QQ
    function checkUserInfoForQQ(Request $request)
    {
        $input = $request->all();

        $code = $input['code'];
        $appid = "1112181876";
        $appsecret = "zD7voCR6HxCvArJF";


        $url = "https://api.q.qq.com/sns/jscode2session?appid=$appid&secret=$appsecret&js_code=$code&grant_type=authorization_code";
        // print("##################");
        // echo($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);

        $dataArray = json_decode($data, true);

        // var_dump($dataArray);

        $resultCode = isset($dataArray['errcode']) ? $dataArray['errcode'] : 0;
        $errmsg = isset($dataArray['errmsg']) ? $dataArray['errmsg'] : "";

        //---------------------------------------------
        if ($resultCode == 0) { //这里应该判断一下返回结果
            $userId = isset($dataArray['openid']) ? $dataArray['openid'] : "";
            $session_key = isset($dataArray['session_key']) ? $dataArray['session_key'] : "";
            
            if ($userId == "" || $session_key =="") {
                return ['result' => $resultCode, 'data' => "未获取到openid或者session_key"];
            }

            $channel = 8; //渠道
            $platform = 'QQGame';
            $dateTime = new \DateTime();
            $now_time = $dateTime->getTimestamp();

            //存在记录就更新,不存在则插入
            $login = DB::select('select * from login where user_id = ?', [$userId]);

            if (empty($login)) {
                DB::insert('insert into login (user_id,role_c_time,login_time,channel,platform) values (?,?,?,?,?)', [$userId, $now_time, $now_time, $channel, $platform]);
            } else {
                DB::update("update login set login_time=$now_time where user_id =?", [$userId]);
            }
            return ['result' => 1, 'data' => "success", 'uid' => $userId, 'key' => $session_key];
        } else {
            return ['result' => $resultCode, 'data' => $errmsg];
        }
    }

    //请求新的access_token
    function getAccessTokenForQQ($zone_id)
    {
        $appid = "1112181876";
        $appsecret = "zD7voCR6HxCvArJF";

        //存在记录就更新,不存在则插入
        $logSqlArray = DB::select('select * from log where serverid = ? and type = ?', [$zone_id, "accessToken"]);

        $access_token = "";

        $dateTime = new \DateTime();
        $now_time = $dateTime->getTimestamp();

        $needRefresh = false;
        if (empty($logSqlArray)) {
            $needRefresh = true;
        }
        else{
            $logSql = $logSqlArray[0];
            //判断有效期
            $ts = (int)$logSql->log_time;
            $expires_in = (int)$logSql->value1;

            if($now_time - $ts > $expires_in){
                $needRefresh = true;
            }
            else{
                $access_token = $logSql->value2;
            }
        }

        if(!$needRefresh){
            return $access_token;
        }

        $url = "https://api.q.qq.com/api/getToken?grant_type=client_credential&appid=$appid&secret=$appsecret";
        // print("##################");
        // echo($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);

        $dataArray = json_decode($data, true);

        // var_dump($dataArray);

        $resultCode = isset($dataArray['errcode']) ? $dataArray['errcode'] : 0;
        $errmsg = isset($dataArray['errmsg']) ? $dataArray['errmsg'] : "";

        //---------------------------------------------
        if ($resultCode == 0) { //这里应该判断一下返回结果
            //有效期
            $expires_in = isset($dataArray['expires_in']) ? $dataArray['expires_in'] : "";   
            //
            $access_token = isset($dataArray['access_token']) ? $dataArray['access_token'] : "";
            
            if ($expires_in == "" || $access_token =="") {
                // return ['result' => $resultCode, 'data' => "未获取到expires_in或者access_token"];
                return $access_token;
            }

            if (empty($logSqlArray)) {
                DB::insert('insert into log (serverid,type,log_time,value1,value2) values (?,?,?,?,?)', [$zone_id, "accessToken", $now_time, $expires_in,$access_token]);
            } else {
                DB::update("update log set value1='$expires_in',value2='$access_token' where serverid = ? and type = ?", [$zone_id, "accessToken"]);
            }
            // return ['result' => 1, 'data' => "success", 'token' => $access_token];
            return $access_token;

        } else {
            // return ['result' => $resultCode, 'data' => $errmsg];
            return $access_token;
        }
        
    }

    //预支付--QQ
    function gamePrePayForQQ(Request $request)
    {
        $input = $request->all();
        $appid = "1112181876";

        $openid = $input['openid'];
        $session_key = $input['session_key'];
        $serverid = $input['serverid'];
        // $zone_id = $input['serverid'];
        $amt = (int)$input['amt'];           
        $bill_no = $input['bill_no']; //订单号
        $goodid = $input['goodid']; //qq后台返回
        $dateTime = new \DateTime();
        $ts = $dateTime->getTimestamp();
        $app_remark = str_replace("=","",$session_key); //原样放回

        //取payItem的id 
        $offerId = "";
        $text = Storage::disk('onekey')->get('payItems.txt');
        $goods = explode("\n", $text);
        foreach ($goods as $key => $value) {
            $data = explode(";", $value);
            if ($data[0] == $goodid) {
                $offerId = $data[4]; //小程序后台商品id
                break;
            }
        }
    
        if(empty($offerId)){
            return;
        }

        //在log表里存储access_token
        $access_token = $this->getAccessTokenForQQ($serverid);
        if(empty($access_token)){
            return;
        }

        $url = "https://api.q.qq.com/api/json/openApiPay/GamePrePay?access_token=$access_token";
        
        $arr_post = [
            'openid' => $openid,
            'appid' => $appid,
            'ts' => $ts,
            'zone_id' => "1",
            "pf"=>"qq_m_qq-2001-android-2011",
            'amt' => $amt,
            'bill_no' => $bill_no,
            'goodid' => $goodid,
            'good_num' => 1,
            'app_remark' => $app_remark,
            'sig' => "",
        ];

        ksort($arr_post);
        foreach ($arr_post as $k => $v) {
            if ($k == 'sig' || $k =='access_token' || $v =="") {
                continue;
            }
            $tmp[] = $k . '=' . urlencode($v);
        }

        $str = 'POST&%2Fapi%2Fjson%2FopenApiPay%2FGamePrePay&'.implode('&', $tmp) . "&session_key=".$session_key;
        $h = hash_hmac('sha256', $str,$session_key, true);
        $sign = bin2hex($h);
        
        $arr_post['sig'] = $sign;
        // print( $str);
        // var_dump($ts);
        // print($arr_post['sig']);

        // $post_data = http_build_query($arr_post);
        // var_dump($arr_post);

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        // curl_setopt($ch, CURLOPT_HTTPHEADER,array(
        //     'Content-Type: application/json; charset=utf-8',
        //     'Content-Length:' . strlen($post_data),
        //     'Cache-Control: no-cache',
        //     'Pragma: no-cache'
        // ));
        // $data = curl_exec($ch);
        // curl_close($ch);

        $post_data = $arr_post;

        /////////////////////
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if(!$post_data){
            return 'post_data is null';
        }

        if(is_array($post_data))
        {
            $post_data = json_encode($post_data);
        }

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER,array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length:' . strlen($post_data),
                'Cache-Control: no-cache',
                'Pragma: no-cache'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        $errorno = curl_errno($curl);
        if ($errorno) {
            return $errorno;
        }
        curl_close($curl);
        ////////
        
        $dataArray = json_decode($res, true);

        $resultCode = isset($dataArray['errcode']) ? $dataArray['errcode'] : 0;
        $errmsg = isset($dataArray['errmsg']) ? $dataArray['errmsg'] : "";

        //---------------------------------------------
        if ($resultCode == 0) { //这里应该判断一下返回结果
            //订单号，有效期是 48 小时
            $prepayId = isset($dataArray['prepayId']) ? $dataArray['prepayId'] : "";   
            
            if ($prepayId == "") {
                return ['result' => $resultCode, 'data' => "未获取到prepayId"];
            }
            
            //记录订单号
            $orderSqlarr = DB::select('select * from orders where cp_no = ?', [$bill_no]);

            if (empty($orderSqlarr)) {
                return ['result' => 10000, 'data' => "订单数据未落库"];
            } else {
                DB::update("update orders set app_trade_no='$prepayId' where cp_no =?", [$bill_no]);
            }
            return ['result' => 1, 'data' => "success", 'pay_no' => $prepayId];
        } else {
            return ['result' => $resultCode, 'data' => $errmsg];
        }
    }
    
    //检查支付状态--QQ
    function checkGamePayForQQ(Request $request)
    {
        $input = $request->all();
        $appid = "1112181876";

        $openid = $input['openid'];
        $session_key = $input['session_key'];
        $prepay_id = $input['prepay_id']; //qq订单号
        $bill_no = $input['bill_no']; //订单号
        $server_id = $input['serverid'];

        if(empty($prepay_id) || empty($bill_no) ){
            return ['result' => 1000, 'data' => "缺少参数"];
        }

        //查询订单是否已经完成
        $orderSqlarr = DB::select('select * from orders where cp_no = ?', [$bill_no]);
        if (empty($orderSqlarr)) {
            return ['result' => 10000, 'data' => "订单数据未落库"];
        }
        $paySql = $orderSqlarr[0];
        //如果已经订单已经完成,不需要操作
        if ($paySql->complete == 1) {
            return ['result' => 1, 'data' => "complete"];
        }

        //验证qq订单号
        $app_trade_no = $paySql->app_trade_no;

        if($app_trade_no!=$prepay_id){
            return ['result' => 20000, 'data' => "prepay_id和记录的不同!"];
        }

        //在log表里存储access_token
        $access_token = $this->getAccessTokenForQQ($server_id);
        if(empty($access_token)){
            return;
        }
    
        $url = "https://api.q.qq.com/api/json/openApiPay/CheckGamePay?access_token=$access_token";
        
        $arr_post = [
            'openid' => $openid,
            'appid' => $appid,
            'bill_no' => $bill_no,
            'prepay_id' => $prepay_id,
            'sig' => "",
        ];

        // $arr_post = 
        // [
        //     // 'access_token' => $access_token,
        //     "openid"=>"55107C3B8501CD7CBD90AEE4626E6D17",
        //     "appid"=>"1107981003",
        //     "bill_no"=>"69ae13a3a87f2551109a2ed26bc704201f56d664",
        //     "prepay_id"=>"beaf257883b098007ca821e1c59f7f7a",
        //     "sig"=>"",
        // ];
        // $session_key  = 'VUNQZ0hRYURxNlZZbmNOZw==';

        ksort($arr_post);
        foreach ($arr_post as $k => $v) {
            if ($k == 'sig' || $v =="") {
                continue;
            }
            $tmp[] = $k . '=' . urlencode($v);
        }

        $str = 'POST&%2Fapi%2Fjson%2FopenApiPay%2FCheckGamePay&'.implode('&', $tmp) . "&session_key=".$session_key;
        
        $h = hash_hmac('sha256', $str,$session_key, true);
        $sign = bin2hex($h);

        $arr_post['sig'] = $sign;

        $post_data = $arr_post;
 
        //////////////////////////////////
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if(!$post_data){
            return 'post_data is null';
        }

        if(is_array($post_data))
        {
            $post_data = json_encode($post_data);
        }

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER,array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length:' . strlen($post_data),
                'Cache-Control: no-cache',
                'Pragma: no-cache'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        $errorno = curl_errno($curl);
        if ($errorno) {
            return $errorno;
        }
        curl_close($curl);
        /////////////////////////////////

        $dataArray = json_decode($res, true);

        $resultCode = isset($dataArray['errcode']) ? $dataArray['errcode'] : 0;
        $errmsg = isset($dataArray['errmsg']) ? $dataArray['errmsg'] : "";

        //---------------------------------------------
        if ($resultCode == 0) { //这里应该判断一下返回结果
            //订单号，有效期是 48 小时
            $pay_state = isset($dataArray['pay_state']) ? $dataArray['pay_state'] : "";   
            $pay_time = isset($dataArray['pay_time']) ? $dataArray['pay_time'] : "";   
            
            if ($pay_state == "") {
                return ['result' => $resultCode, 'data' => "未获取到pay_state"];
            }

            if($pay_state==1){
                $dbid = $paySql->dbid;
                $serverid = $paySql->serverid;
                $goodsid = $paySql->goodsid;
            
                //---完成订单强制发货---//
                $pay_channel = 0;
                $amount = 0;
                $text = Storage::disk('onekey')->get('payItems.txt');
                $goods = explode("\n", $text);
                foreach ($goods as $key => $value) {
                    $data = explode(";", $value);
                    if ($data[0] == $goodsid) {
                        $amount = $data[2]; //单位：元
                        break;
                    }
                }

                $out_trade_no = $app_trade_no;

                DB::update("update orders set end_time= $pay_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,out_trade_no='$out_trade_no',complete=1 where cp_no =?", [$bill_no]);

                //这里需要区分游戏服
                //发货
                // DB::insert('insert into pay (dbid,serverid,playerid,goodsid) values (?,?,?,?)', [$dbid, $serverid, $dbid, $goodsid]);
                $this->delivergoodsByServer($dbid, $serverid, $goodsid);
            }
            else{

            }
            return ['result' => 1, 'data' => "success", 'pay_time' => $pay_time,'pay_state' => $pay_state];
        } else {
            return ['result' => $resultCode, 'data' => $errmsg];
        }
    }

    // /**
    //  * 验证充值返回的签名是否正确--QQ
    //  */
    function verifyRespondSignForQQ(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            return 'empty data';
        }

        //签名
        $Sign = $input['sig'];

        //-------验签------//
        $openid = $input['openid'];
        $bill_no = $input['bill_no'];
        $ts = $input['ts'];
        $app_remark = $input['app_remark']; //用户session_key
        $amount = $input['amt'];
        
        $appsecret = "zD7voCR6HxCvArJF";
        $session_key = $appsecret;


        ///////////////////////
        $arr_post = [
            'openid' => $openid,
            'ts' => $ts,
            'amt' => $amount,
            'bill_no' => $bill_no,
            'app_remark' => $app_remark,
        ];

        ksort($arr_post);
        foreach ($arr_post as $k => $v) {
            if ($v =="") {
                continue;
            }
            $tmp[] = $k . '=' . urlencode($v);
        }

        $str = 'POST&%2Fgmapi%2Fgame%2FverifyRespondSignForQQ&'.implode('&', $tmp) . "&AppSecret=".$session_key;
        /////////////////////////////////////////////
       
        $h = hash_hmac('sha256', $str , $session_key, true);
        $sig = bin2hex($h);
   
        if ($sig == $Sign) {
            // if(1>0){
            //     return ['code' => 1,'msg' => "签名验证成功"];
            // }
            //验签成功,修改order记录

            //游戏内订单号
            $paySqlarr = DB::select('select * from orders where cp_no = ?', [$bill_no]);

            if (empty($paySqlarr)) {
                return -5;
            }
            $paySql = $paySqlarr[0];
            //如果已经订单已经完成,不需要操作
            if ($paySql->complete == 1) {
                //订单重复
                return ['code' => 2, 'msg' => "订单已经完成"];
            }

            $dbid = $paySql->dbid;
            $serverid = $paySql->serverid;
            $goodsid = $paySql->goodsid;

            // echo  $dbid."---".$serverid."--". $goodsid;
            //完成时间
            $dateTime = new \DateTime();
            $end_time = $dateTime->getTimestamp();

            $pay_channel = 0;

            //---验证价格---
            // echo ("amount ".$amount);
            //商品价格
            $price = 0;
            $text = Storage::disk('onekey')->get('payItems.txt');
            $goods = explode("\n", $text);
            foreach ($goods as $key => $value) {
                $data = explode(";", $value);
                if ($data[0] == $goodsid) {
                    $price = $data[2] * 10; //单位：元 //1 元 = 10星币
                    break;
                }
            }
            // $price = 0.01;
            if ($price != $amount) {
                // echo ("error price");
                return ['code' => 3, 'msg' => "商品价格不一致"];
            }
            //-------------
            DB::update("update orders set end_time= $end_time,is_verify_sign=1,verify_result=1,pay_channel=$pay_channel,amount=$amount,complete=1 where cp_no =?", [$bill_no]);

            //这里需要区分游戏服

            //发货
            // DB::insert('insert into pay (dbid,serverid,playerid,goodsid) values (?,?,?,?)', [$dbid, $serverid, $dbid, $goodsid]);
            $this->delivergoodsByServer($dbid, $serverid, $goodsid);
            // echo ($data);

            // $dataArray = json_decode($data, true);
            // $resultCode = isset($dataArray['resultCode']) ? $dataArray['resultCode'] : 0;
            return ['code' => 0, 'msg' => "成功"];//成功
        }
        return ['code' => 10,'msg' => "签名验证失败"]; //签名验证失败
    }

    //---------------------------------QQ小游戏-------------------------------------//

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
            if($sql->round!=0){
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
             //上次普通体力
            $daliyPhysical_old = (int)explode("_", $sql->token)[2];
            //本次上传的普通体力
            $daliyPhysical_new = (int)explode("_", $token)[2];

            //新上传了普通体力恢复
            if($daliyPhysical_new==20 && $daliyPhysical_old!=20){
                if($round >=0){
                    //判断离上次更新的时间
                    $passTime = $now_time - $round;
                    if($passTime >= (86400 - 600))
                    {
                        $round = $now_time; //正常恢复
                        DB::update("update nftrank set end_time= $now_time,buy='$buy',playCnt=$playCnt,round=$round,token='$token',version='$version' where adress = ?", [$adress]);
                        return ['result' => 0, 'surplusTime' => $surplusTime];
                    }
                    else{
                        //修改本地时间的20体力
                        $round = min(-1,$sql->round - 1);
                        DB::update("update nftrank set end_time= $now_time,buy='$buy',playCnt=$playCnt,round=$round,version='$version' where adress = ?", [$adress]);
                        return ['result' => 0, 'surplusTime' => $surplusTime];
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

                $new_buy_physical = 0;
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
        $surplusTime = $end_time - $now_time;
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
            if (empty($uuid)){
            }
            else{
                //找到自己的数据
                $sqlArray = DB::select('select * from nftrank where uid = ?', [$uuid]);
                if (!empty($sqlArray)) {
                    $sql = $sqlArray[0];
                    if($sql->round!=0){
                        //玩家重新上线，恢复正常游玩
                        DB::update("update nftrank set round=0 where adress = ?", [$adress]);
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
            //找到自己的数据
            $sqlArray = DB::select('select * from nftrank where adress = ?', [$adress]);
            if (!empty($sqlArray)) {
                $sql = $sqlArray[0];
                if($sql->round!=0){
                    //玩家重新上线，恢复正常游玩
                    DB::update("update nftrank set round=0 where adress = ?", [$adress]);
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
}
