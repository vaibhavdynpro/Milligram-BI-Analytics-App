<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
    protected $table = 'groups';
    protected $primaryKey = 'group_id';
    protected $fillable = [ 'group_name','entity_id' ,'is_active','created_by'];

}
