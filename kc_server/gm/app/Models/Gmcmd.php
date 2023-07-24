<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gmcmd extends Model
{
    use Timestamp;

    protected $guarded = ['dbid'];//黑名单
    protected $primaryKey = 'dbid'; //主键
    public $timestamps = false;

    protected $table = 'gmcmd';
}
