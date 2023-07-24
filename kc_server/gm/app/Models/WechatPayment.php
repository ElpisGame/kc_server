<?php

namespace App\Models;

use App\Helpers\WechatHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WechatPayment extends Model
{
    use SoftDeletes, Timestamp;

    //软删除
    protected $guarded = ['id'];//黑名单
    protected $primaryKey = 'id'; //主键

    //当使用这个属性的时候，可以直接后面跟着carbon类时间操作的任何方法
    protected $dates = ['created_at', 'updated_at'];
    //类型自动转换 https://blog.csdn.net/z772532526/article/details/81302990?utm_source=blogxgwz8
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'openid', 'openid');
    }

    /**
     * 检查支付状态
     * @return $this
     */
    public function checkPayStatus()
    {
        $message = WechatHelper::queryPayment($this);
        if ($message['trade_state'] === 'SUCCESS') {
            //支付时间
            if (empty($this->paid_at)) {
                $this->paid_at = date("Y-m-d H:i:s");
            }
            //微信订单号
            $this->transaction_id = isset($message['transaction_id']) ? $message['transaction_id'] : '';
            //支付结果
            if ($message['result_code'] === 'SUCCESS') {
                $this->status = '支付成功';
            } elseif ($message['result_code'] === 'FAIL') {
                $this->status = '支付失败';
            }
            $this->save();
            $this->executePaymentRelations();
        }
        return $this;
    }

    /**
     * 处理关联的数据
     */
    private function executePaymentRelations()
    {
        $bid = Bid::query()->where("trade_no", $this->out_trade_no)->first();
        if ($this->status == '支付成功') {
            $bid->paid_at = $this->paid_at;
            $bid->paid_status = $this->status;
            $bid->save();
            //更新订单状态
            $bid->order->status = 3;
            $bid->order->save();
            WechatHelper::pushBidPaySuccess($bid);
        }
    }
}
