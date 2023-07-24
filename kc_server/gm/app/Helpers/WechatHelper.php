<?php

namespace App\Helpers;

use App\Models\Account;
use App\Models\Bid;
use App\Models\Order;
use App\Models\User;
use App\Models\WechatPayment;
use App\Models\WechatRefund;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class WechatHelper
{
    public static function officialAccount()
    {
        $app = Factory::officialAccount(config("wechat.officialAccount"));
        return $app;
    }

    public static function paymentAccount()
    {
        $app = Factory::payment(config("wechat.payment"));
        return $app;
    }

    public static function miniProgram()
    {
        $factory = Factory::miniProgram(config("wechat.miniProgram"));
        //???为啥要更新我忘记了
        $wechat = Cache::get("wechat.program");
        if (empty($wechat) || now()->timestamp > $wechat->access_token_expired_at) {
            $token = $factory->access_token->getToken(true);
            $wechat->access_token = $token['access_token'];
            $wechat->access_token_expired_at = now()->addSeconds($token['expires_in'])->timestamp - 60 * 30;
            $wechat->save();
            Cache::put("wechat.program", $wechat);
        }
        return $factory;
    }

    /**
     * 生成唯一订单号
     * @return string
     */
    public static function generateTradeNo()
    {
        return date('YmdHis') . \Faker\Factory::create()->numerify($string = '##################');
    }

    public static function pushNewOrderMessage(Order $order)
    {
        $account = Account::query()->findOrFail($order->account_id);
        if (!empty($account) && !empty($account->openid)) {
            $factory = self::officialAccount();
            $redirect_uri = "https://merchant.chonglala.com/#/order/{$order->id}";
            $message = [
                'touser' => $account->openid,
                'template_id' => 'SIPwuuydKZGxufnsc1WkTt4q03FbyfgosRwbxL3voCA',
                'url' => $redirect_uri,
                'data' => [
                    'first' => "{$order->pet_quantity}只{$order->pet_weight}公斤{$order->pet_brand}({$order->pet_name})",
                    //我的需求
                    'keyword1' => "{$order->start_province}{$order->start_city} - {$order->dest_province}{$order->dest_city}",
                    //订单状态
                    'keyword2' => "新订单",
                    'remark' => "{$order->id} # {$order->created_at}"
                ]
            ];
            $factory->template_message->send($message);
        }
    }

    public static function pushNewBidMessage(Bid $bid)
    {
        $user = $bid->order->user;
        $redirect_uri = "https://user.chonglala.com/#/order/{$bid->order_id}";
        $message = [
            'touser' => $user->openid,
            'template_id' => 'kfREioC3CjTzg67YEt6gmtIwfpDcyiappuxYoBD1bEY',
            'url' => $redirect_uri,
            'data' => [
                'first' => '宠拉拉为您的爱宠保驾护航',
                //商家名称
                'keyword1' => $bid->user->nickname,
                //商家电话
                'keyword2' => "同意报价后可显示",
                //订单号
                'keyword3' => date("YmdH") . $bid->order_id,
                //状态
                'keyword4' => "收到新的报价",
                //总价
                'keyword5' => "￥ {$bid->price} 元",
                'remark' => $bid->description
            ],
        ];
        $factory = self::officialAccount();
        $factory->template_message->send($message);
    }

    public static function pushBidAcceptMessage(Bid $bid)
    {
        $user = $bid->user;
        $redirect_uri = "https://merchant.chonglala.com/#/order/{$bid->order_id}";
        $message = [
            'touser' => $user->openid,
            'template_id' => 'kfREioC3CjTzg67YEt6gmtIwfpDcyiappuxYoBD1bEY',
            'url' => $redirect_uri,
            'data' => [
                'first' => '客户已接受了你的报价',
                //商家名称
                'keyword1' => $user->nickname,
                //商家电话
                'keyword2' => "**",
                //订单号
                'keyword3' => date("YmdH") . $bid->order_id,
                //状态
                'keyword4' => "客户已接受了你的报价",
                //总价
                'keyword5' => "￥ {$bid->price} 元",
                'remark' => $bid->description
            ],
        ];
        $factory = self::officialAccount();
        $factory->template_message->send($message);
    }

    //推送给商户
    public static function pushBidRefuseMessage(Bid $bid)
    {
        $user = $bid->user;
        $redirect_uri = "https://merchant.chonglala.com/#/order/{$bid->order_id}";
        $message = [
            'touser' => $user->openid,
            'template_id' => 'kfREioC3CjTzg67YEt6gmtIwfpDcyiappuxYoBD1bEY',
            'url' => $redirect_uri,
            'data' => [
                'first' => '客户已拒绝了你的报价',
                //商家名称
                'keyword1' => $user->nickname,
                //商家电话
                'keyword2' => "**",
                //订单号
                'keyword3' => date("YmdH") . $bid->order_id,
                //状态
                'keyword4' => "客户已拒绝了你的报价",
                //总价
                'keyword5' => "￥ {$bid->price} 元",
                'remark' => $bid->description
            ],
        ];
        $factory = self::officialAccount();
        $factory->template_message->send($message);
    }

    public static function pushBidPaySuccess(Bid $bid)
    {
        $user = $bid->user;
        $redirect_uri = "https://merchant.chonglala.com/#/order/{$bid->order_id}";
        $message = [
            'touser' => $user->openid,
            'template_id' => 'kfREioC3CjTzg67YEt6gmtIwfpDcyiappuxYoBD1bEY',
            'url' => $redirect_uri,
            'data' => [
                'first' => '顾客已付款',
                //商家名称
                'keyword1' => "宠拉拉平台",
                //商家电话
                'keyword2' => "15298705200",
                //订单号
                'keyword3' => date("YmdH") . $bid->order_id,
                //状态
                'keyword4' => "已付款",
                //总价
                'keyword5' => "￥ {$bid->price} 元",
                'remark' => $bid->description
            ],
        ];
        $factory = self::officialAccount();
        $factory->template_message->send($message);
        //notify to account
        self::pushBidPaySuccessToAccount($bid);
    }

    public static function pushBidPaySuccessToAccount(Bid $bid)
    {
        $order = $bid->order();
        $account = $bid->account();
        if (!empty($account) && !empty($account->openid)) {
            $redirect_uri = "https://merchant.chonglala.com/#/order/{$bid->order_id}";
            $message = [
                'touser' => $account->openid,
                'template_id' => 'kfREioC3CjTzg67YEt6gmtIwfpDcyiappuxYoBD1bEY',
                'url' => $redirect_uri,
                'data' => [
                    'first' => '顾客已付款',
                    //商家名称
                    'keyword1' => "宠拉拉平台",
                    //商家电话
                    'keyword2' => "15298705200",
                    //订单号
                    'keyword3' => date("YmdH") . $bid->order_id,
                    //状态
                    'keyword4' => "已付款",
                    //总价
                    'keyword5' => "￥ {$bid->price} 元",
                    'remark' => $bid->description
                ],
            ];
            $factory = self::officialAccount();
            $factory->template_message->send($message);
        }
    }


    public static function unifyOrder($userOpenid, $out_trade_no, $totalFee = 100, $body, $tradeType = "JSAPI")
    {
        $app = self::paymentAccount();
        //创建一个统一订单
        $unifyOrder = $app->order->unify([
            'out_trade_no' => $out_trade_no,
            'body' => $body,
            'total_fee' => $totalFee,
            'trade_type' => $tradeType, // 请对应换成你的支付方式对应的值类型
            'openid' => $userOpenid,
            'notify_url' => route('bid.notify')
        ]);
        //Log::info(json_encode($unifyOrder, JSON_PRETTY_PRINT));
        if ($unifyOrder['result_code'] == 'FAIL') {
            throw new \Exception($unifyOrder['err_code_des']);
        }
        $fields = [
            "mch_id" => $unifyOrder['mch_id'],
            "app_id" => $unifyOrder['appid'],
            "prepay_id" => $unifyOrder['prepay_id'],
            "openid" => $userOpenid,
            "out_trade_no" => $out_trade_no,
            "body" => $body,
            "total_fee" => $totalFee,
            "trade_type" => $tradeType
        ];
        //检查是否存在支付记录
        $wechatPayment = WechatPayment::query()->where("out_trade_no", $out_trade_no)->first();
        if (empty($wechatPayment)) {
            $wechatPayment = new WechatPayment($fields);
            $wechatPayment->save();
        } else {
            $wechatPayment->update($fields);
        }
        /*这是给客户端的返回值*/
        return $app->jssdk->sdkConfig($wechatPayment->prepay_id); // 返回数组
    }

    public static function queryPayment($wechatPayment)
    {
        $app = self::paymentAccount();
        $result = $app->order->queryByOutTradeNumber($wechatPayment->out_trade_no);
        return $result;
    }

    public static function refundPayment($wechatPayment, $refundFee, $desc)
    {
        if (empty($wechatPayment))
            throw new \Exception("Payment record not found");
        //生成退款单号
        $refundNo = WechatHelper::generateTradeNo();
        //执行退款
        $wechatRefund = self::refund($wechatPayment, $refundNo, $refundFee, $desc);
        //如果退款成功，则需要修改这个订单的退款总金额
        if ($wechatRefund->result_code == "SUCCESS") {
            $wechatPayment->refund = $wechatPayment->refund + $wechatRefund->refund_fee;
            $wechatPayment->save();
        }
        return $wechatRefund;
    }

    private static function refund($wechatPayment, $refundNo, $refundFee, $desc = "退款说明")
    {
        $app = self::paymentAccount();
        $result = $app->refund->byOutTradeNumber(
            $wechatPayment->out_trade_no,
            $refundNo,
            $wechatPayment->total_fee,
            $refundFee,
            ['refund_desc' => $desc]);
        if ($result['return_code'] == "FAIL") {
            throw new \Exception($result['return_msg']);
        }
        //进入正常逻辑
        $data = collect($result)->only([
            'mch_id',
            'refund_id',
            'coupon_refund_fee',
            'coupon_refund_fee',
            'cash_fee',
            'coupon_refund_count',
            'cash_refund_fee',
            'result_code',
            'err_code',
            'err_code_des'
        ])->toArray();
        $data['gh_id'] = $wechatPayment->gh_id;
        $data['app_id'] = $wechatPayment->app_id;
        $data['out_trade_no'] = $wechatPayment->out_trade_no;
        $data['out_refund_no'] = $refundNo;
        $data['total_fee'] = $wechatPayment->total_fee;
        $data['refund_fee'] = $refundFee;
        $data['transaction_id'] = $wechatPayment->transaction_id;

        $wechatRefund = new WechatRefund($data);
        $wechatRefund->save();
        return $wechatRefund;
    }

    /*创建临时二维码*/
    public static function qrcodeTemporary($event_key, $expire_seconds = 30 * 24 * 3600)
    {
        $factory = self::officialAccount();
        $result = $factory->qrcode->temporary($event_key, $expire_seconds);
        $url = $factory->qrcode->url($result['ticket']);
        #返回
        return [
            "uuid" => $event_key,
            "expire" => $expire_seconds,
            "url" => $url
        ];
    }

    /*创建永久二维码*/
    public static function qrcodeForever($event_key)
    {
        $factory = self::officialAccount();
        $result = $factory->qrcode->forever($event_key);
        $url = $factory->qrcode->url($result['ticket']);
        return [
            "event_key" => $event_key,
            "url" => $url
        ];
    }

    public static function getAccesstoken()
    {
        $wechat = Cache::get("wechat");
        if (empty($wechat) || now()->timestamp > $wechat->access_token_expired_at) {
            $factory = self::officialAccount();
            $token = $factory->access_token->getToken(true);
            $wechat->access_token = $token['access_token'];
            $wechat->access_token_expired_at = now()->addSeconds($token['expires_in'])->timestamp - 60 * 30;
            Cache::put("wechat", $wechat);
        }
        return $wechat->access_token;
    }

    /*创建短网址*/
    public static function shortUrl($url)
    {
        $app = self::officialAccount();
        $result = $app->url->shorten($url);
        return isset($result['short_url']) ? $result['short_url'] : $url;
    }

    /*创建菜单*/
    public static function createMenu($menus)
    {
        $factory = self::officialAccount();
        return $factory->menu->create($menus);
    }


    /**
     * 生成带场景的二维码，一般用于统计用户关注来源
     * @param Request $request
     * @return JsonResource
     */
    public function sceneQrcode(Request $request)
    {
        $event_key = $request->get("scene");
        $data = self::qrcodeForever($event_key);
        return new JsonResource($data);
    }

    /**
     * 转账
     * @param $open_id
     * @param $real_name
     * @param $amount
     * @param $desc
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function transfer($open_id, $real_name, $amount, $desc)
    {
        $app = self::paymentAccount();
        $trade_no = self::generateTradeNo();
        $app->transfer->toBalance([
            'partner_trade_no' => $trade_no, // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
            'openid' => $open_id,
            'check_name' => 'FORCE_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
            're_user_name' => $real_name, // 如果 check_name 设置为FORCE_CHECK，则必填用户真实姓名
            'amount' => $amount, // 企业付款金额，单位为分
            'desc' => $desc, // 企业付款操作说明信息。必填
        ]);
    }
}
