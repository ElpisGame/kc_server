<?php

namespace App\Models;

use App\Helpers\WechatHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Account extends Authenticatable implements JWTSubject
{
    use SoftDeletes, Timestamp, HasRoles, Notifiable;

    //软删除
    protected $guarded = ['id'];//黑名单
    protected $primaryKey = 'id'; //主键

    //当使用这个属性的时候，可以直接后面跟着carbon类时间操作的任何方法
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    //类型自动转换 https://blog.csdn.net/z772532526/article/details/81302990?utm_source=blogxgwz8
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
        'active' => 'boolean'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function login()
    {
        return auth('admin')->login($this);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', "openid", "openid");
    }

    public function children()
    {
        return $this->hasMany(Account::class, "pid", "id")->with('children');
    }

    public function wechatQrcode()
    {
        $qrcode = WechatHelper::qrcodeForever("account_{$this->id}");
        $content = file_get_contents($qrcode['url']);
        $fileName = "{$this->id}.jpg";
        Storage::disk("qrcode")->put($fileName, $content);
        $this->qrcode = asset("qrcode/{$fileName}");
        $this->save();
    }
}
