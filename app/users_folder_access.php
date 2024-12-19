<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class users_folder_access extends Model
{
    protected $table = 'users_folder_access';
    protected $fillable = [ 'user_id', 'folder_id','folder_primary_id'];
}
