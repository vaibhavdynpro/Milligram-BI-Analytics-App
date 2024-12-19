<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{  
    protected $table = 'signup_otp';
    protected $primaryKey = 'id';
    protected $fillable = [ 'first_name', 'last_name', 'email', 'group_code', 'otp','otp_count','flag'];
}