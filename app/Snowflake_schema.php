<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Snowflake_schema extends Model
{
    protected $table = 'snowflake_schema';
    protected $primaryKey = 'tbl_id';
    protected $fillable = [ 'schema_name','created_at'];
}
