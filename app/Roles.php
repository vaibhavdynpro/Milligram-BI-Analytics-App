<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'roles';
     protected $primaryKey = 'role_id';
    protected $fillable = [ 'role_unique_id','role_id','role','phm','users','user_add','user_edit','user_delete','user_view','looker','matillion','group_module','roles','clients','client_add','client_edit','client_delete','client_view','group_add',
'group_edit',
'group_delete',
'group_view', 
'report_add',
'report_edit',
'report_delete',
'report_view',
'role_add',
'role_edit',
'role_delete',
'role_view',
'dashboards',
'generate_report',
'generate_report_add',
'generate_report_edit',
'generate_report_delete',
'generate_report_view',
'invite_user',
'is_active','created_by'];
}
