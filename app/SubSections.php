<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubSections extends Model
{
    protected $table = 'sub_sections';
    protected $fillable = [ 'sub_section_title', 'sub_section_text', 'sub_section_no', 'phm_id', 'section_id', 'look_img_url','look_id','look_name'];

}
