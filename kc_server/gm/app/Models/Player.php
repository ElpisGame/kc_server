<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use Timestamp;
    public $timestamps = false;
    protected $primaryKey = 'dbid'; //主键
}
