<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
class ELTController extends Controller
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
    }
    public function curl_call($url,$method,$body=null,$content_type=null)
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS =>$body,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Basic aGltYW5zaHU6V2ludGVyQDIwMjA=",
            $content_type
        ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);      
        return $response;
    }
    public function get_projects()
    {
        $url = "http://ec2-54-146-94-8.compute-1.amazonaws.com:8080/rest/v1/group/name/KAIROS_ELT_PROJECTS_DEV/project";
        $response=$this->curl_call($url,'GET');
        return $response;
    }

    public function index()
    {
        $activeMenu = '3';
		$activeSubMenu = '0';
        $project = json_decode($this->get_projects());
        return view('elt.index',compact('activeMenu','activeSubMenu','activeSubMenu'))->with('project',$project);
    }

    public function jobs($proj)
    {
        $url = "http://ec2-54-146-94-8.compute-1.amazonaws.com:8080/rest/v1/group/name/KAIROS_ELT_PROJECTS_DEV/project/name/".$proj."/version/name/default/job";
        $response=$this->curl_call($url,'GET');
        $jobs_all = json_decode($response);
        $activeMenu = 3;
		$activeSubMenu = '0';
        /**
         * FILTERING ONLY ORCHESTRATION JOBS FOR ACTIVE SCHEDULING
         */
        $load_jobs = array_filter($jobs_all,function ($value) {
            if(substr($value,0,9)=='Data_Load'){   
                return true;
            }
        });
        return view('elt.jobs',compact('activeMenu','proj','activeSubMenu'))->with('jobs',$load_jobs);

    }


    public function scheduler($proj,$job)
    {
        $url = "http://ec2-54-146-94-8.compute-1.amazonaws.com:8080/rest/v1/group/name/KAIROS_ELT_PROJECTS_DEV/project/name/".$proj."/schedule/name/".$job."/export";
        $response=$this->curl_call($url,'GET');
        $scheduler_details = json_decode($response);
        // print_r($scheduler_details->objects[0]);
        // die();
        $activeMenu = 3;
		$activeSubMenu = '0';
        return view('elt.schedule',compact('activeMenu','proj','activeSubMenu'))->with('schedule_details',$scheduler_details->objects[0]);
    }

    public function rescheduleJob(Request $request)
    {
        function check_uncheck($toggle,$week_Active=null)
        {
            if($week_Active=="on"){
                if($toggle=="on"){
                    return true;
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
            
        }
        $schedule_name =$request->job_name; //JOB NAME IS IDENTICAL AS SCHEDULER NAME IN MATILLION
        $proj = $request->proj_name;
        $url = "http://ec2-54-146-94-8.compute-1.amazonaws.com:8080/rest/v1/group/name/KAIROS_ELT_PROJECTS_DEV/project/name/".$proj."/schedule/name/".$schedule_name."/update?ignoreUnresolved=false%20WITH%20POST%20DATA%20arg1";
        $week_active_flag = $request->toggle_days;

        //RESOLVE DAYS OF MONTH IF SELECTED//
        if($request->toggle_month=="on"){
            $days_of_month=$request->days;
        }else{
            $days_of_month="";
        }
            
        $params = array(
            "name"=>$request->job_name,
            "minute"=>$request->minutes,
            "hour"=>$request->hours,
            "dayOfWeek"=>check_uncheck($week_active_flag,"on"),
            "monday"=>check_uncheck($request->mon,$week_active_flag),
            "tuesday"=>check_uncheck($request->tue,$week_active_flag),
            "wednesday"=>check_uncheck($request->wed,$week_active_flag),
            "thursday"=>check_uncheck($request->thu,$week_active_flag),
            "friday"=>check_uncheck($request->fri,$week_active_flag),
            "saturday"=>check_uncheck($request->sat,$week_active_flag),
            "sunday"=>check_uncheck($request->sun,$week_active_flag),
            "daysOfMonth"=>$days_of_month,
            "enabled"=>check_uncheck($request->enabled,"on"),
            "timezone"=>$request->tz,
            "versionName"=>"default",
            "jobName"=>$schedule_name,
            "environmentName"=>$request->env,
            "preventDuplicateJob"=>check_uncheck($request->prevent_dup,"on"),
        );

        $body = json_encode($params);
        $content_type = "Content-Type: application/json";
        $response = json_decode($this->curl_call($url,"POST",$body,$content_type));
        

    /**
     * TODO
     * fetch Error message from response if any
     * send back  response success/failure
     */
        if($response->success){
            $msg = "Successfully Rescheduled Job";
            return redirect()->back()->with('success', $msg);
        }
        else{
            $msg = $response->msg;
            return redirect()->back()->with('failed', $msg);
        }

        
    }

    public function tasklog($proj,$job)
    {
        $url = "http://ec2-54-146-94-8.compute-1.amazonaws.com:8080/rest/v1/group/name/KAIROS_ELT_PROJECTS_DEV/project/name/".$proj."/task/filter/by/job/name/".$job."";
        $response = json_decode($this->curl_call($url,'GET'));
        $activeMenu = 3;
		$activeSubMenu = '0';
        return view('elt.tasklog',compact('activeMenu','proj','activeSubMenu'))->with('task_details',$response);

    }

    public function deep_dive($projectName,$id)
    {
       $url = "http://ec2-54-146-94-8.compute-1.amazonaws.com:8080/rest/v1/group/name/KAIROS_ELT_PROJECTS_DEV/project/name/".$projectName."/task/id/".$id."";
       $response = json_decode($this->curl_call($url,'GET'),true);
       $activeMenu = 3;
	   $activeSubMenu = '0';
        return view('elt.tasklog_extended',compact('activeMenu','activeSubMenu'))->with('task_extended',$response['tasks']);

    }

}
