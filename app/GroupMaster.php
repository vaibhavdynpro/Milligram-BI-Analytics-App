<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupMaster extends Model
{
    protected $table = 'group_role_dashboards_mapping';
     protected $primaryKey = 'id';
    protected $fillable = [ 'group_id','role_id','client_primary_id','client_id','dashboard_id','sub_dashboard_id','is_active','created_by','updated_by'];
}
