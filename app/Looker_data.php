<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Looker_data extends Model
{
    protected $table = 'looker_data';
     protected $primaryKey = 'tbl_id';
    protected $fillable = [ 'client_primary_id','client_id','client_name','folder_id','folder_name','dash_id','title','created_at','updated_at'];
}
