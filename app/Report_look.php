<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report_look extends Model
{
    protected $table = 'report_look';
    protected $primaryKey = 'id';
    protected $fillable = [ 'report_id', 'section_id', 'sub_section_id', 'sub_section_no','chart_type','look_id','look_url','embed_url'];
}
