<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class password_resets extends Model
{
    //
    protected $table = 'password_resets';
    protected $fillable = [
        'email','user_id', 'token',
    ];

}
