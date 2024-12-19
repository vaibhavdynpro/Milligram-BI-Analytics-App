<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users_dasboards_mapping extends Model
{
    protected $table = 'users_dasboards_mapping';
    protected $primaryKey = 'usr_dash_id';
    protected $fillable = [ 'grp_usr_mapping_id','client_primary_id','client_id','dashboard_id','sub_dashboard_id','is_active','created_by'];
}
