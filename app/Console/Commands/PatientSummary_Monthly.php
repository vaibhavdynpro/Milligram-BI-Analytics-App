<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;

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

class PatientSummary_Monthly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PatientSummary_Monthly:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('max_execution_time', 18000);
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

        // $ScheduleData=\DB::table('patient_summary_report')->select('ps_report_id')->where(['is_active' => 1,'frequency' => 1,'schedular' => 1])
        // ->whereNotIn('status', [0,6])
        // ->get();

        $ScheduleData=\DB::table('patient_summary_report')->select('ps_report_id')->where(['is_active' => 1,'frequency' => 3,'status' => 0])
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
                if(!empty($Patient_arr)){
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
                        

                        if($count == $key)
                        {
                            DB::table('patient_summary_report')->where('ps_report_id', $value->ps_report_id)->update(['status' => 5]);
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
        ini_set('max_execution_time', 900);
        $lookerSetting = Looker::find('1');
        $api_url = $lookerSetting->api_url;
        $client_id = $lookerSetting->client_id;
        $client_secret = $lookerSetting->client_secret;
        $folder = explode("_",$details->folder_name);
        $name = "";
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
        DB::table('patient_summary_report')->where('ps_report_id', $details->ps_report_id)->update(['status' => 4]);
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
        DB::table('patient_summary_report')->where('ps_report_id', $details->ps_report_id)->update(['response' => $name]);   

        $zip = new \ZipArchive();
        $fileName = 'app/public/pdf/'.$name.'.zip';
        if ($zip->open(storage_path($fileName), \ZipArchive::CREATE)== TRUE)
        {
            $files = File::files(storage_path('app/public/pdf/'.$name));
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
        DB::table('patient_summary_report')->where('ps_report_id', $details->ps_report_id)->update(['file_path' => $s3Path,'status' => 6]);
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
}
