<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    //
    protected $table = 'loginLog';
       
       protected $fillable = array('users_id', 'ip','deviceId','source','status');

}
