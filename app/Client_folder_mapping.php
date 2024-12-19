<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client_folder_mapping extends Model
{
    protected $table = 'client_folder_mapping';
    protected $fillable = [ 'folder_id', 'folder_name'];
}
