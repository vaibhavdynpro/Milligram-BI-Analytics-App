<?php

namespace App\Http\Controllers;
//test
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Looker;
use App\PatientSummary;
use App\PatientSummaryList;
use App\Libraries\Helpers;
use App\Http\Requests;
use PHPMailer\PHPMailer;
use PDF;
use DB;
use File;

class PatientSummaryPdfController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('phm');
        $this->helper = new Helpers;
    }
    public function index()
    {
        $activeMenu = '23';
        $activeSubMenu = '0';
        $ReportData= \DB::table('patient_summary_report')
        ->select('patient_summary_report.ps_report_id','patient_summary_report.name','patient_summary_report.folder_id','patient_summary_report.user_id','patient_summary_report.frequency','patient_summary_report.file_path','patient_summary_report.status','patient_summary_report.is_active','client_folder_mapping.folder_name',DB::raw("count('patient_summary_list.ps_list_id') as count"))
        ->leftjoin('patient_summary_list','patient_summary_report.ps_report_id','=','patient_summary_list.ps_report_id')
        ->join('client_folder_mapping','patient_summary_report.folder_id','=','client_folder_mapping.folder_id')
        ->join('users_folder_access','client_folder_mapping.id','=','users_folder_access.folder_primary_id')
        ->distinct()
        ->where(['patient_summary_report.is_active' => '1','client_folder_mapping.is_active' => '1','patient_summary_report.user_id' => auth()->user()->id,'users_folder_access.user_id' => auth()->user()->id])
        ->groupBy('patient_summary_report.ps_report_id','patient_summary_report.name','patient_summary_report.folder_id','patient_summary_report.user_id','patient_summary_report.frequency','patient_summary_report.file_path','patient_summary_report.status','patient_summary_report.is_active','client_folder_mapping.folder_name')
        ->get();
        // echo "<pre>";
        // print_r($ReportData);
        // exit();
        return view('PatientSummary.index',compact('activeMenu', 'activeSubMenu','ReportData'));
    }
    public function list($encrypt_id)
    {
        $activeMenu = '23';
        $activeSubMenu = '0';
        $id = $this->helper->encrypt_decrypt($encrypt_id,'decrypt');
        $results =  \DB::table('patient_summary_list')
        ->select('*')
        ->where(['ps_report_id' => $id])
        ->get();
        return view('PatientSummary.list',compact('activeMenu', 'activeSubMenu','results'));
    }
    public function create()
    {
       ini_set('max_execution_time', 900);
        $activeMenu = '23';
        $activeSubMenu = '0';
        
        $ClientFolders = \DB::table('client_folder_mapping')
                ->select('client_folder_mapping.id','client_folder_mapping.folder_id','client_folder_mapping.folder_name','client_folder_mapping.schema_name')
                ->join('users_folder_access','client_folder_mapping.id','=','users_folder_access.folder_primary_id')
                ->distinct()
                ->where(['users_folder_access.user_id' => auth()->user()->id,'client_folder_mapping.entity_id' => env('env_entity_id'),'client_folder_mapping.is_active' => 1])
                ->orderBy('client_folder_mapping.folder_name')
                ->get();
       
        return view('PatientSummary.create',compact('activeMenu', 'activeSubMenu','ClientFolders'));
    }

    public function store(Request $request)
    {
        
        $name ="";
        if($request->frequency == 1){
            $name = $request->client_name."_PatientReport_".date('mdyHi');
        }
        elseif($request->frequency == 2){
            $name =$request->client_name."__PatientReport_Weekly_".date('mdyHi');
        }
        elseif($request->frequency == 3){
            $name =$request->client_name."__PatientReport_Monthly_".date('mdyHi');
        }
        elseif($request->frequency == 4){
            $name =$request->client_name."__PatientReport_Quaterly_".date('mdyHi');
        }

        $exp = explode('/', $request->client_id);

        $ps_report_id = PatientSummary::create([
            'name'                    => $name,
            'folder_id'               => $exp[0],
            'user_id'                 => auth()->user()->id,
            'dash_id'                 => $request->dash_id,
            'frequency'               => $request->frequency,
            'created_by'              => auth()->user()->id,  
            ])->ps_report_id;
        $patient_list=[];
        foreach ($request->patientlist as $key => $value) {
            $arr['ps_report_id'] = $ps_report_id;
            $arr['patient_name'] = $value;
            array_push($patient_list,$arr);
        }
        PatientSummaryList::insert($patient_list);
        return redirect('all_reports')->with('success', 'Schedule Report request has been successfully created!!');       
    }
    public function destroy($id)
    {
        DB::table('patient_summary_report')
            ->where('ps_report_id', $id)
            ->update(['is_active' => '0']);
        return redirect('all_reports')->with('success','Report has been deleted successfully!!');
    }
    public function render_dashboards()
    {
        ini_set('max_execution_time', 900);
        $lookerSetting = Looker::find('1');
        $api_url = $lookerSetting->api_url;
        $client_id = $lookerSetting->client_id;
        $client_secret = $lookerSetting->client_secret;

        $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
        $method = "POST";
        $resp= $this->curlCall($url, $method);
        $responseData = json_decode($resp,true);
        //call to lookers folder api
        $query = array('access_token' => $responseData['access_token']);
        $ScheduleData=\DB::table('patient_summary_report')->select('ps_report_id')->where(['is_active' => 1,'frequency' => 1,'status' => 0])
        ->get();

        if(!empty($ScheduleData[0])){
            foreach($ScheduleData as $k => $v)
            {

                $Patient_arr= \DB::table('patient_summary_report')
                ->select('patient_summary_list.ps_report_id','patient_summary_list.patient_name','patient_summary_report.name as folder_name','patient_summary_report.frequency','patient_summary_report.dash_id')
                ->join('patient_summary_list','patient_summary_report.ps_report_id','=','patient_summary_list.ps_report_id')        
                ->where(['patient_summary_report.ps_report_id' => $v->ps_report_id,'patient_summary_report.is_active' => 1,'patient_summary_report.status' => 0])
                ->orderBy('patient_summary_report.ps_report_id','DESC')
                ->get();
                $count = count($Patient_arr) - 1;
                
                $time = time();
                if(!empty($Patient_arr[0])){
                    DB::table('patient_summary_report')->where('ps_report_id', $v->ps_report_id)->update(['status' => 1,'schedular' => 1,'response' => "Started"]);
                    foreach($Patient_arr as $key => $value){
                        $QueriesJson=[];
                        $QueriesJson['dashboard_filters'] = "Patient Name=".$value->patient_name;
                        $QueriesJson['dashboard_style'] = "tiled";
                        $payload = json_encode($QueriesJson);
                        $authorization = "Authorization: Bearer ".$responseData['access_token']; 
                        $url1 = $api_url . "render_tasks/lookml_dashboards/".$value->dash_id."/pdf?width=1500&height=500"; 
                        $lookerData= $this->curlCall1($url1, $method, $authorization, $payload);        
                        $lookerData1 = json_decode($lookerData,true);      
                        if(!empty($lookerData1))
                        {
                        $url2 = $api_url . "render_tasks/".$lookerData1['id']; 
                        $method1 = "GET";
                        $lookerData2= $this->curlCall($url2, $method1,$query);         
                        $lookerData3 = json_decode($lookerData2,true);  
                            if(!empty($lookerData3))
                            {
                                $this->get_render_task($lookerData3['id'],$value->patient_name,$value->ps_report_id,$Patient_arr[0],$time,$count,$key);
                            }                      
                        }

                        sleep(15);
                        
                        if($count == $key)
                        {
                            DB::table('patient_summary_report')->where('ps_report_id', $value->ps_report_id)->update(['status' => 1]);
                            $this->zip($Patient_arr[0],$time);
                            $ReportData=\DB::table('patient_summary_report')->select('patient_summary_report.ps_report_id','users.name','users.email')
                            ->join('users','patient_summary_report.user_id','=','users.id')
                            ->where(['ps_report_id' => $value->ps_report_id])
                            ->get();
                            $this->send_notification($ReportData[0]->name,$ReportData[0]->email);
                        }
                    }
                }
            }
        }
    }
    public function get_render_task($id,$patient_name,$report_id,$details,$time,$count,$key)
    {
        ini_set('max_execution_time', 900);
        $lookerSetting = Looker::find('1');
        $api_url = $lookerSetting->api_url;
        $client_id = $lookerSetting->client_id;
        $client_secret = $lookerSetting->client_secret;

        $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
        $method = "POST";
        $resp= $this->curlCall($url, $method);
        $responseData = json_decode($resp,true);
        //call to lookers folder api
        $query = array('access_token' => $responseData['access_token']);
        sleep(15);

        $url2 = $api_url . "render_tasks/".$id; 
        $method1 = "GET";
        $lookerData2= $this->curlCall($url2, $method1,$query); 
        $lookerData3 = json_decode($lookerData2,true);
        $status = $lookerData3['status'];
        
        do {
            $url2 = $api_url . "render_tasks/".$id; 
            $method1 = "GET";
            $lookerData2= $this->curlCall($url2, $method1,$query); 
            $lookerData3 = json_decode($lookerData2,true);
            $status = $lookerData3['status'];
            if($status == "failure")
            {
                break;
            }
            DB::table('patient_summary_report')->where('ps_report_id', $details->ps_report_id)->update(['status' => 1,'response' => $status]);
        } while ($status != "success");


            DB::table('patient_summary_report')->where('ps_report_id', $details->ps_report_id)->update(['status' => 2,'response' => $status]);
            $this->get_render_task_result($id,$patient_name,$details,$time,$count,$key);
            DB::table('patient_summary_list')->where('ps_report_id', $report_id)->where('patient_name', $patient_name)->update(['status' => 1]);
        
            
        
    }
    public function get_render_task_result($id,$patient_name,$details,$time,$count,$key)
    {
        $lookerSetting = Looker::find('1');
        $api_url = $lookerSetting->api_url;
        $client_id = $lookerSetting->client_id;
        $client_secret = $lookerSetting->client_secret;
        $folder = explode("_",$details->folder_name);
        
        if($details->frequency == 1){
            $name = $folder[0]."_PatientReport_".date('mdy')."_".$time;
        }
        elseif($details->frequency == 2){
            $name =$folder[0]."__PatientReport_Weekly_".date('mdy')."_".$time;
        }
        elseif($details->frequency == 3){
            $name =$folder[0]."__PatientReport_Monthly_".date('mdy')."_".$time;
        }
        elseif($details->frequency == 4){
            $name =$folder[0]."__PatientReport_Quaterly_".date('mdy')."_".$time;
        }

        $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
        $method = "POST";
        $resp= $this->curlCall($url, $method);
        $responseData = json_decode($resp,true);
        //call to lookers folder api
        $query = array('access_token' => $responseData['access_token']);

        $url2 = $api_url . "render_tasks/".$id."/results"; 
        $method1 = "GET";
        $lookerData2= $this->curlCall2($url2, $method1,$query,$patient_name,$name,$details,$count,$key,$time); 
        
    }
    
    public function curlCall($url, $method, $query=null){
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
            CURLOPT_POSTFIELDS => $query,
            // CURLOPT_HTTPHEADER => array(
            //  "Content-Type: application/x-www-form-urlencoded"
            // ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        
        //echo $responseData['access_token'];
        return $response;
    }
    public function curlCall1($url, $method, $authorization,$query=null){
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
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_HTTPHEADER => array(
             "Content-Type: application/json",
             $authorization
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        
        //echo $responseData['access_token'];
        return $response;
    }
    public function curlCall2($url, $method, $query=null,$patient_name,$name,$details,$count,$key,$time){ 
        if(!Storage::exists("/public/pdf/".$name)){
            Storage::makeDirectory("/public/pdf/".$name,0777);
        }
        $file = '../storage/app/public/pdf/'.$name.'/'.$patient_name.'.pdf';
        $path = 'public/pdf/'.$name.'/'.$patient_name.'.pdf';
        Storage::put($path, '');
        $fp = fopen(storage_path("app/".$path),'w');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HEADER => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_FILE => $fp,            
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    public function zip($details,$time)
    {   
        $folder = explode("_",$details->folder_name);
        if($details->frequency == 1){
            $name = $folder[0]."_PatientReport_".date('mdy')."_".$time;
        }
        elseif($details->frequency == 2){
            $name =$folder[0]."__PatientReport_Weekly_".date('mdy')."_".$time;
        }
        elseif($details->frequency == 3){
            $name =$folder[0]."__PatientReport_Monthly_".date('mdy')."_".$time;
        }
        elseif($details->frequency == 4){
            $name =$folder[0]."__PatientReport_Quaterly_".date('mdy')."_".$time;
        }


        $zip = new \ZipArchive();
        $fileName = '../storage/app/public/pdf/'.$name.'.zip';
        if ($zip->open(public_path($fileName), \ZipArchive::CREATE)== TRUE)
        {
            $files = File::files("../storage/app/public/pdf/".$name);
            foreach ($files as $key => $value){
                $relativeName = basename($value);
                $zip->addFile($value, $relativeName);
            }
            $zip->close();
        }
        Storage::deleteDirectory('public/pdf/'.$name);

        $s3Path = 'PatientSummary_Reports/' . $name.'.zip';
        $zipFilePath = 'public/pdf/'.$name.'.zip';
        $contents = Storage::get($zipFilePath);
        Storage::disk('s3')->put($s3Path, $contents);
        Storage::delete('public/pdf/'.$name.'.zip');
        DB::table('patient_summary_report')->where('ps_report_id', $details->ps_report_id)->update(['file_path' => $s3Path]);
        // return response()->download(public_path($fileName));
    }

    public function download_zip($folder,$filename)
    {
        $filepath=$folder.'/'.$filename;
        $headers = [
            'Content-Type'        => 'Content-Type: application/zip',
            'Content-Disposition' => 'attachment; filename='.$filename,
        ];

        return \Response::make(Storage::disk('s3')->get($filepath), 200, $headers);
    }
    public function get_mapping(Request $request)
    {
        $results =  \DB::table('mapping')
        ->select('*')
        ->where(['category' => $request->cat,'ids' =>$request->client_id])
        ->get();
        return response()->json(['success'=>200,'result'=>$results]);
    }
    public function get_patient(Request $request)
    {
        $limit = explode("-",$request->limit);
        $query = "CALL SCH_COMMON.DEMOGRAPHIC_VALUES('".$request->schema_name."',TO_ARRAY('name'),'".$limit[1]."','".$limit[0]."')";
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
           "query":"'.$query.'",
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
        return response()->json(['success'=>$request->schema_name,'patientlist'=>$resultset]);
    }
    public function send_notification($first_name,$email)
    {
        $text             = 'Hi '.$first_name.','."<br/>"."<br/>";
        $text             = $text.'Your scheduled Patient Summary Report Generated successfully.'."<br/>";
        $text             = $text.'Kindly login to  <a href="https://hca.kairosrp.com/">Kairos App</a>'."to download it.<br/>";
        $text             = $text.'Thank You,'."<br/>";
        $text             = $text.'Team Kairos';
        // echo $text;
        // exit();
        $mail             = new PHPMailer\PHPMailer(); // create a n
        $mail->IsSMTP();
        // $mail->SMTPDebug  = 1; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth   = true; // authentication enabled
        $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host       = "email-smtp.us-east-1.amazonaws.com";
        $mail->Port       = 587; ; // or 587
        $mail->IsHTML(true);
        $mail->Username = "AKIAR2DNWFHADOVB3X75";
        $mail->Password = "BE3hsje11JymCh+nRogY4SxHoVGIEoloN4fK3xb0YQak";
        $mail->SetFrom("hca@kairosrp.com", 'Kairos App');
        $mail->Subject = "Report Generated Successfully";
        $mail->Body    = $text;
        $mail->AddAddress($email);
        $check=1;
        // $mail->Send();
        if ($mail->Send()) {
        
            return true;
        }
        else{
            return false;
        }
    }
    public function patient_count()
    {
        $ClientFolders = \DB::table('mapping')
                ->select('*')
                ->get();
        if(!empty($ClientFolders))
        {
            foreach($ClientFolders as $val)
            {
                $query = "CALL SCH_COMMON.PATIENT_DEMOGRAPHICS_COUNT('".$val->schema_name."');";
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
                   "query":"'.$query.'",
                   "schema":"'.$val->schema_name.'"
                }',
                  CURLOPT_HTTPHEADER => array(
                    'x-api-key: nBCbDwJZYe8pLENWbFEjvaWH6tzdOklh5vLXWCVJ',
                    'Content-Type: application/json'
                  ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $resultset = json_decode($response);
                if(!empty($resultset[0]))
                {
                    DB::table('mapping')->where('id', $val->id)->update(['patient_count' => $resultset[0]->PATIENT_DEMOGRAPHICS_COUNT]);
                }
                // return response()->json(['success'=>$request->schema_name,'patientlist'=>$resultset]);
            }
            
        }

       
    }
    public function get_patientCount(Request $request)
    {
        $ClientFolders = \DB::table('mapping')
                ->select('*')
                ->where('ids', $request->client_id)
                ->get();
        if(!empty($ClientFolders[0]))
        {
            return response()->json(['success'=>200,'count'=>$ClientFolders[0]->patient_count]);
        }
        else
        {
            return response()->json(['success'=>200,'count'=>0]);
        }
    }
}