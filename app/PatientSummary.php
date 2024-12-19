<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PatientSummary extends Model
{
    protected $table = 'patient_summary_report';
    protected $primaryKey = 'ps_report_id';
    protected $fillable = [ 'folder_id', 'user_id', 'name', 'dash_id', 'frequency', 'status', 'file_path', 'is_active', 'created_by','created_at','response','schedular'];
}
