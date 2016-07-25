<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    //
       protected $table = 'device_token';
       
       protected $fillable = array('users_id','deviceid', 'apikey','source','pushid','status');
}
