<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mail extends Model
{
    use Timestamp; //软删除
    protected $guarded = ['dbid'];//黑名单
    protected $primaryKey = 'dbid'; //主键
    public $timestamps = false;
}
