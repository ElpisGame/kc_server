<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WechatRefund extends Model
{
    use SoftDeletes,Timestamp; //软删除
    protected $guarded = ['id'];//黑名单
    protected $primaryKey = 'id'; //主键


    //当使用这个属性的时候，可以直接后面跟着carbon类时间操作的任何方法
    protected $dates = ['created_at','updated_at','deleted_at'];
    //类型自动转换 https://blog.csdn.net/z772532526/article/details/81302990?utm_source=blogxgwz8
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];
}
