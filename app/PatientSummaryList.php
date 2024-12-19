<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PatientSummaryList extends Model
{
    protected $table = 'patient_summary_list';
    protected $primaryKey = 'ps_list_id';
    protected $fillable = [ 'ps_report_id', 'patient_name', 'file_path', 'status', 'is_active', 'created_by','created_at'];
}
