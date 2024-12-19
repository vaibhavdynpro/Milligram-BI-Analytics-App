<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Helpers;

class SnowflakeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('matillion');
        $this->helper = new Helpers;
    }

    public function index()
    {
        $activeMenu = '3';
		$activeSubMenu = '0';
        $project = "i will send it!!!!!";
        return view('snowflake.index',compact('activeMenu','activeSubMenu','activeSubMenu'))->with('project',$project);
    }

    public function dataprocessing()
    {
        $activeMenu = '3';
		$activeSubMenu = '0';
        $project = "HIMANSHU";
        $Sqlquery = "SHOW SCHEMAS";
        $Schema_nameS = "SCH_KAIROS_ARKANSAS_MUNICIPAL_LEAGUE";
        $schema_name = json_decode($this->helper->SnowFlack_Call($Sqlquery,$Schema_nameS));
        return view('snowflake.ml-orch',compact('activeMenu','activeSubMenu','activeSubMenu','schema_name'))->with('project',$project);
    
    }
    public function execute(Request $request){
        $years = implode(', ',$request->folders);
        $twelve_month_flag = $request->duration;
        $schema = $request->schema_name;
        $performance_paid_amt_flag = $request->performance_paid_amt_flag;
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://wq69q71jza.execute-api.us-east-1.amazonaws.com/sf-processing-task-stage',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
        "base_years": "'.$years.'",
        "twelve_month_flag": "'.$twelve_month_flag.'",
        "schema": "'.$schema.'",
        "performance_paid_amt_flag":"'.$performance_paid_amt_flag.'"
        }',
        CURLOPT_HTTPHEADER => array(
            'x-api-key: nBCbDwJZYe8pLENWbFEjvaWH6tzdOklh5vLXWCVJ',
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        print_r($response);
        exit();

    }
    public function get_base_years(Request $request)
    {
        $curl = curl_init();
  
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://8gw1bd7nnd.execute-api.us-east-1.amazonaws.com/sf-deploy',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>'{
                   "query":"SELECT DISTINCT YEAR(DIAGNOSIS_DATE) as \\"name\\" FROM STG_TAB_MEDICAL_DATA ORDER BY \\"name\\" DESC",
                   "schema":"'.$request->schema_name.'"
                }',
                  CURLOPT_HTTPHEADER => array(
                    'x-api-key: nBCbDwJZYe8pLENWbFEjvaWH6tzdOklh5vLXWCVJ',
                    'Content-Type: application/json'
                  ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $resultset = json_decode($response);
                return response()->json(['success'=>$request->schema_name,'base_years'=>$resultset]);

    }
}
