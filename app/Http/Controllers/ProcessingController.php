<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Roles;
use App\Looker;
use App\users_folder_access;
use PHPMailer\PHPMailer;
use App\Libraries\Helpers;

class ProcessingController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->helper = new Helpers;

    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activeMenu = '2';
        $activeSubMenu = '0';
		$Tabels = DB::select("SHOW TABLES");

        //print_r($Tabels);
        //exit();
        return view('processing.index',compact('Tabels','activeMenu', 'activeSubMenu'));
    }
    public function getColumn(Request $request)
    {
        $tabel = explode(",",$request->tabel);
        $where = "";
        $count = count($tabel);
        foreach($tabel as $key => $val)
        {
            if($count-1 == $key)
            {
            $where .= "TABLE_NAME = '".$val."'";
            }
            else
            {
            $where .= "TABLE_NAME = '".$val."' OR ";
            
            }
        }
        $sql = "select *
        from INFORMATION_SCHEMA.COLUMNS WHERE ".$where."";
        // echo "<pre>";
        // $sql = "select *
        // from INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' OR TABLE_NAME ='entity'";
        $columns = DB::select($sql);
        $arr =[];
        $i = 0;
            foreach($columns as $k => $v)
            {
                if(!isset($arr[$v->TABLE_NAME]))
                {
                $i=0;
                $arr[$v->TABLE_NAME][$i] = $v->COLUMN_NAME;
                $i++;
                }
                else
                {
                $arr[$v->TABLE_NAME][$i] = $v->COLUMN_NAME;                    
                $i++;
                }
            }
        // print_r($arr);
        // exit();
        return response()->json(['success'=>'Data is successfully fetch','columns'=>$arr]);
    }
      public function getsingletblColumn(Request $request)
    {
        $tabel = $request->tabel;
        $sql = "select *
        from INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='".$tabel."'";
        
        $columns = DB::select($sql);
        
        return response()->json(['success'=>'Data is successfully fetch','columns'=>$columns]);
    }
}
