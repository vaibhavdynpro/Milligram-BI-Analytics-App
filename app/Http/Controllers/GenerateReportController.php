<?php

namespace App\Http\Controllers;
//test
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Looker;
use App\Report;
use App\Report_look;
use App\PatientSummary;
use App\PatientSummaryList;
use App\Libraries\Helpers;
use App\Http\Requests;
use PHPMailer\PHPMailer;
use PDF;
use DB;
use File;

class GenerateReportController extends Controller
{
	public function __construct()
    {
        // $this->middleware('auth');
        // $this->middleware('phm');
        $this->helper = new Helpers;
    }
    public function index()
    {
        $activeMenu = '23';
        $activeSubMenu = '0';
        $ReportData= \DB::table('report')
        ->select('report.*','client_folder_mapping.folder_name')
        ->join('phm','report.phm_folder_id','=','phm.client_id')
        ->join('client_folder_mapping','phm.client_id','=','client_folder_mapping.folder_id')
       	->where(['report.is_active' => '1','phm.is_active' => '1','client_folder_mapping.is_active' => '1','phm.entity_id' => env('env_entity_id'),'report.user_id' => auth()->user()->id])
        ->get();

        $PS_ReportData= \DB::table('patient_summary_report')
        ->select('patient_summary_report.ps_report_id','patient_summary_report.name','patient_summary_report.folder_id','patient_summary_report.user_id','patient_summary_report.frequency','patient_summary_report.file_path','patient_summary_report.status','patient_summary_report.is_active','client_folder_mapping.folder_name',DB::raw("count('patient_summary_list.ps_list_id') as count"))
        ->leftjoin('patient_summary_list','patient_summary_report.ps_report_id','=','patient_summary_list.ps_report_id')
        ->join('client_folder_mapping','patient_summary_report.folder_id','=','client_folder_mapping.folder_id')
        ->join('users_folder_access','client_folder_mapping.id','=','users_folder_access.folder_primary_id')
        ->distinct()
        ->where(['patient_summary_report.is_active' => '1','client_folder_mapping.is_active' => '1','patient_summary_report.user_id' => auth()->user()->id,'users_folder_access.user_id' => auth()->user()->id])
        ->groupBy('patient_summary_report.ps_report_id','patient_summary_report.name','patient_summary_report.folder_id','patient_summary_report.user_id','patient_summary_report.frequency','patient_summary_report.file_path','patient_summary_report.status','patient_summary_report.is_active','client_folder_mapping.folder_name')
        ->get();
        
        return view('reports.index',compact('activeMenu', 'activeSubMenu','ReportData','PS_ReportData'));
    }
    public function create()
    {
       ini_set('max_execution_time', 900);
        $activeMenu = '23';
        $activeSubMenu = '0';

        
        // $folderChildArr = \DB::table('users_folder_access as a')
        //         ->select('c.id','b.folder_name')
        //         ->join('client_folder_mapping as b','a.folder_id','=','b.parent_folder_id')
        //         ->join('looker_parent_phm as c','b.folder_id','=','c.id')
        //         ->where(['b.type' => "PHM"])
        //         ->where(['a.user_id' => auth()->user()->id])
        //         ->orderBy('b.folder_name')
        //         ->get();
        $folderChildArr = DB::select("SELECT b.folder_name,a.folder_primary_id,b.folder_id,b.phm_folder_id,b.parent_folder_id,c.folder_id as phm_folder_id,b.schema_name FROM users_folder_access as a INNER JOIN client_folder_mapping as b ON a.folder_primary_id = b.id and b.is_parent_phm =1 INNER JOIN client_folder_mapping as c ON b.folder_id= c.parent_folder_id and c.type='PHM' and c.is_parent_phm =1 WHERE a.user_id ='".auth()->user()->id."' AND b.entity_id='".env('env_entity_id')."' ORDER BY b.folder_name"); 
        return view('reports.create',compact('activeMenu', 'activeSubMenu','folderChildArr'));
    }
        
       
    public function store(Request $request)
    {
        $data = $request->all();
        $exp = explode('/', $data['client_id']);
        $cnt= count($data['years']);
        $years = implode(",",$data['years']);
        if($data['frequency'] == 1){
            $name = $data['name']."_".$cnt."_Years_PHM_Report_".date('mdy');
        }
        elseif($data['frequency'] == 2){
            $name = $data['name']."_".$cnt."_Years_PHM_Report_".date('mdy')."_WeeklySchedule";
        }
        elseif($data['frequency'] == 3){
            $name = $data['name']."_".$cnt."_Years_PHM_Report_".date('mdy')."_MonthlySchedule";
        }
        elseif($data['frequency'] == 4){
            $name = $data['name']."_".$cnt."_Years_PHM_Report_".date('mdy')."_QuarterlySchedule";
        }


        $dataset['name']            = $name;
        $dataset['year']            = $years;
        $dataset['user_id']         = auth()->user()->id;
        $dataset['phm_folder_id']   = $exp[0];
        $dataset['reporting_year']  = $data['reporting_yr'];
        $dataset['frequency']       = $data['frequency'];
        $dataset['storeLook_folder_id']  = $data['store_folder'];
        $dataset['schedule_time']   = date('Y-m-d H:i', strtotime(date("Y-m-d H:i"). ' +5 minutes'));    

        Report::insert($dataset);
        if($data['frequency'] == 1)
        {
        return redirect('all_reports')->with('success', 'Report will be available after 2 Hours!!');            
        }
        else
        {
        return redirect('all_reports')->with('success', 'Schedule Report request has been successfully created!!');                        
        }

    }
    public function download()
    {   
        ini_set('max_execution_time', 1500);
        ini_set("pcre.backtrack_limit", "10000000");
        $lookerSetting = Looker::find('1');
        $api_url = $lookerSetting->api_url;
        $client_id = $lookerSetting->client_id;
        $client_secret = $lookerSetting->client_secret;

        $ReportData= \DB::table('report')
        ->select('report.*')
        // ->where(['report.schedule_time' => date('Y-m-d H:i:s')])
        ->where(['report.looks_generated' => 0])
        ->orderBy('report.report_id','DESC')
        ->get();

        if(isset($ReportData) && !empty($ReportData))
        {
            if(isset($ReportData[0]->phm_folder_id))
                {
                $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
                $method = "POST";
                $resp= $this->curlCall($url, $method);
                $responseData = json_decode($resp,true);
                //call to lookers folder api
                $query = array('access_token' => $responseData['access_token']);
                
                
                $SectionData= \DB::table('sections')
                ->select('sections.id','sections.section_title','sections.section_text','sections.section_no')
                ->join('phm','sections.phm_id','=','phm.id')
                ->where(['phm.client_id' => $ReportData[0]->phm_folder_id])
                ->where(['phm.is_active' => 1])
                ->groupBy('sections.id','sections.section_title','sections.section_text','sections.section_no')
                ->orderBy('section_no')
                ->get();

                $SubSecData= \DB::table('sub_sections')
                ->select('sub_sections.id','sub_sections.sub_section_title','sub_sections.sub_section_text','sub_sections.sub_section_no','sub_sections.section_id','sub_sections.look_id')
                ->join('sections','sub_sections.section_id','=','sections.id')
                ->join('phm','sections.phm_id','=','phm.id')
                ->where(['phm.client_id' => $ReportData[0]->phm_folder_id])
                ->where(['phm.is_active' => 1])
                ->groupBy('sub_sections.id','sub_sections.sub_section_title','sub_sections.sub_section_text','sub_sections.sub_section_no','sub_sections.section_id','sub_sections.look_id')
                ->orderBy('section_id')
                ->get();

                $SubSectionData=[];
                $looks_data=[];
                $looks_data1=[];
                $look=[];
                foreach($SubSecData as $key => $value)
                {
                    if(isset($value->look_id) && !empty($value->look_id)){
                        $url1 = $api_url . "looks/".$value->look_id; 
                        $method1 = "GET";
                        $lookerData= $this->curlCall($url1, $method1,$query); 
                        $lookerData1 = json_decode($lookerData,true);

                        $folder_id = $lookerData1['folder_id'];
                        $model = $lookerData1['model']['id'];

                        $title = $lookerData1['title']."_".$ReportData[0]->report_id."_".auth()->user()->id."_".$ReportData[0]->year;

                        $view = $lookerData1['query']['view'];

                        $create_look_arr=[
                            "model" =>          $model,
                            "view" =>           $view,
                            "title" =>          $title,
                            "folder_id" =>      $ReportData[0]->storeLook_folder_id,
                            "public" =>         true
                        ];


                        $QueriesJson = $lookerData1['query'];
                        unset($QueriesJson['id']);
                        unset($QueriesJson['can']);
                        unset($QueriesJson['url']);
                        unset($QueriesJson['expanded_share_url']);
                        unset($QueriesJson['share_url']);
                        unset($QueriesJson['client_id']);
                        unset($QueriesJson['slug']);

                        foreach($QueriesJson['filters'] as $k => $val)                
                        {
                            if($k == "vw_medical.diagnosis_date" || $k == "vw_med_and_pharma_summary_1.PAID_YEAR" || $k == "vw_risk_group_migration.File_year" || $k == "vw_medical.Paid_year" || $k == "vw_medication_possession_ratio.year" || $k == "vw_medical.diagnosis_year" || $k == "vw_pharmacy.service_year" || $k == "vw_medical.reporting_year" || $k == "vw_pharmacy.reporting_year")
                            {
                                $QueriesJson['filters'][$k] = "".$ReportData[0]->year."";
                            }
                            if($k == "vw_medical.reporting_date_filter" || $k == "vw_pharmacy.reporting_date_filter")
                            {
                                $QueriesJson['filters'][$k] = "".$ReportData[0]->reporting_year."";

                            }
                        
                        }


                        $payload = json_encode($QueriesJson);
                       
                        $url2 = $api_url . "queries?fields=id";
                        $authorization = "Authorization: Bearer ".$responseData['access_token']; 
                        $lookerData2= $this->curlCall1($url2, $method, $authorization, $payload); 
                        $lookerData3 = json_decode($lookerData2,true);

                        $create_look_arr['query_id'] = $lookerData3['id'];
                        $create_Look_payload = json_encode($create_look_arr);

                        $url3 = $api_url . "looks";
                        $lookerData4= $this->curlCall1($url3, $method, $authorization, $create_Look_payload); 
                        $lookerData5 = json_decode($lookerData4,true);
                        $SubSectionData[$value->section_id][$key]['section_id'] =$value->section_id;
                        $SubSectionData[$value->section_id][$key]['sub_section_title'] =$value->sub_section_title;
                        $SubSectionData[$value->section_id][$key]['sub_section_text'] =$value->sub_section_text;
                        $SubSectionData[$value->section_id][$key]['sub_section_no'] =$value->sub_section_no;
                        $SubSectionData[$value->section_id][$key]['section_no'] =$value->section_id;
                        $SubSectionData[$value->section_id][$key]['look_id'] =$value->look_id;
                        $SubSectionData[$value->section_id][$key]['look_img_url'] =(isset($lookerData5['image_embed_url']))?$lookerData5['image_embed_url']:"";
                        $SubSectionData[$value->section_id][$key]['embed_url'] = (isset($lookerData5['embed_url']) && !empty($lookerData5['embed_url']))?$lookerData5['embed_url']:"";

                        
                        // $look[]=(isset($lookerData5['image_embed_url']))?$lookerData5['image_embed_url']:"";
                        if(isset($lookerData5['id']) && !empty($lookerData5['id']))
                        {
                        $looks_data['report_id']        = $ReportData[0]->report_id;
                        $looks_data['section_id']       = $value->section_id;
                        $looks_data['sub_section_id']   = $value->id;
                        $looks_data['sub_section_no']   = $value->sub_section_no;
                        $looks_data['look_id']          = $lookerData5['id'];
                        $looks_data['chart_type']       = $lookerData5['query']['vis_config']['type'];
                        $looks_data['embed_url']         = (isset($lookerData5['embed_url']) && !empty($lookerData5['embed_url']))?$lookerData5['embed_url']:"";
                        $looks_data['look_url']         = (isset($lookerData5['image_embed_url']) && !empty($lookerData5['image_embed_url']))?$lookerData5['image_embed_url']:"";
                        Report_look::insert($looks_data);
                        }
                    }
                    else
                    {
                        $looks_data1['report_id']        = $ReportData[0]->report_id;
                        $looks_data1['section_id']       = $value->section_id;
                        $looks_data1['sub_section_id']   = $value->id;
                        $looks_data1['sub_section_no']   = $value->sub_section_no;
                        Report_look::insert($looks_data1);
                    }
                }                       
                        
                        DB::table('report')->where('report_id', $ReportData[0]->report_id)->update(['looks_generated' => 1]);
                        $id = $ReportData[0]->report_id;
                        return view('reports.view_look',compact('SectionData','SubSectionData','id'));
                        // $this->view($ReportData[0]->report_id,$ReportData[0]->name);
                        // $pdf = PDF::loadView('reports.view_look',compact('SectionData','SubSectionData','id'));
                        // $path = 'public/pdf/'.$ReportData[0]->name.'_'.time().'.pdf';
                        // DB::table('report')->where('report_id', $ReportData[0]->report_id)->update(['looks_generated' => 2]);
                        // Storage::put($path, $pdf->output());
                        
            }

        }
        
    
        
    }
    public function view()
    {
      ini_set('max_execution_time', 2400);
        $ReportData= \DB::table('report')
        ->select('report.*')
        ->whereNull('report.file_path')
        ->where(['report.looks_generated' => 2])
        ->limit(1)
        ->get();

        if(!empty($ReportData[0])){

            $id =   $ReportData[0]->report_id;
            $name = $ReportData[0]->name;
        
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


        $SectionData= \DB::table('sections')
                ->select('sections.*')
                ->join('phm','sections.phm_id','=','phm.id')
                ->join('report','phm.client_id','=','report.phm_folder_id')
                ->where(['report.report_id' => $id])
                ->where(['phm.is_active' => 1])
                ->orderBy('section_no')
                ->get();
         $phmData= \DB::table('phm')
                ->select('phm.name')
                ->join('report','phm.client_id','=','report.phm_folder_id')
                ->where(['report.report_id' => $id])
                ->where(['phm.is_active' => 1])
                ->get();

        $SubSecData= DB::select("SELECT
        sub_sections.id,
        sub_sections.sub_section_title,
        sub_sections.sub_section_text,
        sub_sections.sub_section_no,
        sub_sections.section_id,
        sub_sections.phm_id,
        sub_sections.long_table,
        sections.section_no,
        report_look.look_url,
        report_look.look_id,
        report_look.chart_type,
        report_look.embed_url
        FROM
        report INNER JOIN report_look ON
        report.report_id = report_look.report_id
        INNER JOIN sub_sections on report_look.sub_section_id = sub_sections.id
        INNER JOIN sections on sub_sections.section_id = sections.id
        WHERE report.report_id = $id
        ORDER by sections.section_no ASC, sub_sections.sub_section_no ASC");
        
        $SubSectionData = [];
       
        foreach($SubSecData as $key => $value)
          {
            $SubSectionData[$value->section_id][$key]['section_id'] =$value->section_id;
            $SubSectionData[$value->section_id][$key]['sub_section_title'] =$value->sub_section_title;
            $SubSectionData[$value->section_id][$key]['sub_section_text'] =$value->sub_section_text;
            $SubSectionData[$value->section_id][$key]['sub_section_no'] =$value->sub_section_no;
            $SubSectionData[$value->section_id][$key]['look_id'] =$value->look_id;
            $SubSectionData[$value->section_id][$key]['sub_section_id'] =$value->id;
            $SubSectionData[$value->section_id][$key]['chart_type'] =$value->chart_type;
            $SubSectionData[$value->section_id][$key]['embed_url'] =$value->embed_url;
            $SubSectionData[$value->section_id][$key]['long_look'] =$value->long_table;
            if(isset($value->look_url) && $value->look_url != ""){
                if($value->long_table == 0){
                       
                    try {
                          $img= imagecreatefrompng($value->look_url); // Load and instantiate the image
                          if($img) {
                            $cropped=imagecropauto($img,IMG_CROP_WHITE);
                            if($cropped !== false){
                                imagedestroy($img);
                                ob_start();
                                imagepng($cropped);
                                $image = ob_get_contents();
                                $imgname = $value->look_id.'.png';
                                $filePath = 'phm_look/' . $imgname;
                                Storage::disk('s3')->put($filePath, $image); 
                                ob_end_clean();

                                $s3 = \Storage::disk('s3');
                                $client = $s3->getDriver()->getAdapter()->getClient();
                                $expiry = "+10 minutes";
                                $imgGetPath = 'phm_look/'.$value->look_id.".png";
                                $command = $client->getCommand('GetObject', [
                                  'Bucket' => 'kairos-app-storage', // bucket name
                                  'Key'    => $imgGetPath
                                ]);

                                $request = $client->createPresignedRequest($command, $expiry);
                                $imagepath =  (string) $request->getUri(); 
                                $SubSectionData[$value->section_id][$key]['look_img_url'] =$imagepath;
                              }
                              else
                              {
                                  $SubSectionData[$value->section_id][$keys]['look_img_url'] = "";
                              }
                          }
                      }
                      catch(Exception $e) {

                            echo 'Message: ' .$e->getMessage();                                  
                    }
                }
                else
                {
                    $url4 = $api_url . "looks/".$value->look_id."/run/html"; 
                    $method1 = "GET";
                    $htmlData= $this->curlCall($url4, $method1,$query);
                    $SubSectionData[$value->section_id][$key]['look_img_url'] =$htmlData;
                }
            }
            

            
          }
          // print_r($SubSectionData);
          // exit();

          $ReportData= \DB::table('report')
        ->select('report.year','client_folder_mapping.folder_name','client_folder_mapping.phm_logo')
        ->join('phm','report.phm_folder_id','=','phm.client_id')
        ->join('client_folder_mapping','phm.client_id','=','client_folder_mapping.folder_id')
        ->where(['report.report_id' => $id])
        ->get();
            DB::table('report')->where('report_id', $id)->update(['looks_generated' => 3]);
//             return view('reports.view',compact('SectionData','SubSectionData','phmData','id','ReportData'));
            $this->generate_pdf($name,$id,$SectionData,$SubSectionData,$phmData);
        }
    }
    public function generate_pdf($name,$id,$SectionData,$SubSectionData,$phmData){
        ini_set('max_execution_time', 2400);
        ini_set("pcre.backtrack_limit", "10000000");


        $ReportData= \DB::table('report')
        ->select('report.year','report.phm_folder_id','client_folder_mapping.folder_name','client_folder_mapping.phm_logo','users.email','users.name','report.reporting_year')
        ->join('phm','report.phm_folder_id','=','phm.client_id')
        ->join('client_folder_mapping','phm.client_id','=','client_folder_mapping.folder_id')
        ->join('users','report.user_id','=','users.id')
        ->where(['report.report_id' => $id])
        ->get();
        
        $SchemaData= \DB::table('client_folder_mapping')
        ->select('client_folder_mapping.schema_name')
        ->where(['client_folder_mapping.folder_id' => $ReportData[0]->phm_folder_id])
        ->get();
        $years = rtrim($ReportData[0]->year, ',');
        $date_range=$this->get_dates($years,$SchemaData[0]->schema_name,$ReportData[0]->reporting_year);

        if(!empty($date_range))
        {
        $date_range_data = json_decode($date_range);
            try {            
            $pdf = PDF::loadView('reports.view',compact('SectionData','SubSectionData','phmData','id','ReportData','date_range_data'));
            //Localstorage
            $path = 'public/pdf/'.$name.'_'.time().'.pdf';
            Storage::put($path, $pdf->output());

            $filePath = 'Generated_PHM/' . $name.'_'.time().'.pdf';
            Storage::disk('s3')->put($filePath, $pdf->output());

            $this->send_notification($ReportData[0]->name,$ReportData[0]->email,$path);

            //Remove from Local Storage
            unlink(storage_path('app/'.$path));
            
            DB::table('report')->where('report_id', $id)->update(['file_path' => $filePath,'looks_generated' => 5]);
            return $pdf->download($name.'_'.time().'.pdf');
            }
            catch(Exception $e) {
                  echo 'Message: ' .$e->getMessage();                                  
            }
        }            
        
    }

    public function view_report($encrypt_id)
    {
      ini_set('max_execution_time', 2400);
        $id = $this->helper->encrypt_decrypt($encrypt_id,'decrypt');

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

        $SectionData= \DB::table('sections')
                ->select('sections.*')
                ->join('phm','sections.phm_id','=','phm.id')
                ->join('report','phm.client_id','=','report.phm_folder_id')
                ->where(['report.report_id' => $id])
                ->orderBy('section_no')
                ->get();
         $phmData= \DB::table('phm')
                ->select('phm.name')
                ->join('report','phm.client_id','=','report.phm_folder_id')
                ->where(['report.report_id' => $id])
                ->get();

        $SubSecData= $clientData = DB::select("SELECT
        sub_sections.id,
        sub_sections.sub_section_title,
        sub_sections.sub_section_text,
        sub_sections.sub_section_no,
        sub_sections.section_id,
        sub_sections.phm_id,
        sub_sections.long_table,
        sections.section_no,
        report_look.look_url,
        report_look.look_id,
        report_look.chart_type,
        report_look.embed_url
        FROM
        report INNER JOIN report_look ON
        report.report_id = report_look.report_id
        INNER JOIN sub_sections on report_look.sub_section_id = sub_sections.id
        INNER JOIN sections on sub_sections.section_id = sections.id
        WHERE report.report_id = $id
        ORDER by sections.section_no ASC, sub_sections.sub_section_no ASC");
        $SubSectionData = [];
        foreach($SubSecData as $key => $value)
          {
            $SubSectionData[$value->section_id][$key]['section_id'] =$value->section_id;
            $SubSectionData[$value->section_id][$key]['sub_section_title'] =$value->sub_section_title;
            $SubSectionData[$value->section_id][$key]['sub_section_text'] =$value->sub_section_text;
            $SubSectionData[$value->section_id][$key]['sub_section_no'] =$value->sub_section_no;
            $SubSectionData[$value->section_id][$key]['sub_section_id'] =$value->id;
            $SubSectionData[$value->section_id][$key]['long_look'] =$value->long_table;
            // $SubSectionData[$value->section_id][$key]['look_img_url'] =$value->look_url;
            if($value->long_table == 0){
                $s3 = \Storage::disk('s3');
                $client = $s3->getDriver()->getAdapter()->getClient();
                $expiry = "+10 minutes";
                $imgGetPath = 'phm_look/'.$value->look_id.".png";
                $command = $client->getCommand('GetObject', [
                  'Bucket' => 'kairos-app-storage', // bucket name
                  'Key'    => $imgGetPath
                ]);

                $request = $client->createPresignedRequest($command, $expiry);
                $imagepath =  (string) $request->getUri(); 
                $SubSectionData[$value->section_id][$key]['look_img_url'] =$imagepath;
            }
            else
            {
                $url4 = $api_url . "looks/".$value->look_id."/run/html"; 
                $method1 = "GET";
                $htmlData= $this->curlCall($url4, $method1,$query);
                $SubSectionData[$value->section_id][$key]['look_img_url'] =$htmlData;   
            }
          }

          $ReportData= \DB::table('report')
            ->select('report.year','client_folder_mapping.folder_name','client_folder_mapping.phm_logo')
            ->join('phm','report.phm_folder_id','=','phm.client_id')
            ->join('client_folder_mapping','phm.client_id','=','client_folder_mapping.folder_id')
            ->where(['report.report_id' => $id])
            ->get();

          return view('reports.view_report',compact('SectionData','SubSectionData','phmData','ReportData'));

    }
    public function d()
    {
        // ini_set('max_execution_time', 1500);
        // ini_set("pcre.backtrack_limit", "5000000");
       

        // $lookerSetting = Looker::find('1');
        // $api_url = $lookerSetting->api_url;
        // $client_id = $lookerSetting->client_id;
        // $client_secret = $lookerSetting->client_secret;

        // $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
        // $method = "POST";
        // $resp= $this->curlCall($url, $method);
        // $responseData = json_decode($resp,true);
        // //call to lookers folder api
        // $query = array('access_token' => $responseData['access_token']);
                
        // $url1 = $api_url . "looks/7171/run/html"; 
        // $method1 = "GET";
        // $lookerData= $this->curlCall($url1, $method1,$query);
        // $lookerData1= base64_encode($lookerData);
        // echo "<pre>";
        // print_r($lookerData1);
        // exit();

        //  return view('reports.d',compact('lookerData'));
        $pdf = PDF::loadView('reports.d');
        return $pdf->download('a.pdf');





    }
    public function destroy($id)
    {
        DB::table('report')
            ->where('report_id', $id)
            ->update(['is_active' => '0']);
        Report_look::where('report_id',$id)->delete();
        return redirect('all_reports')->with('success','Report has been deleted successfully!!');
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

  public function get_file($encrypt_id)
    {   
        $id = $this->helper->encrypt_decrypt($encrypt_id,'decrypt');
        $ReportData= \DB::table('report')
        ->select('report.*')
        ->where(['report.report_id' => $id])
        ->orderBy('report.report_id','DESC')
        ->get();
        $file_name = $ReportData[0]->file_path;
        $path = storage_path().'/'.'app/'.$file_name;    
        return response()->download($path);
    }

    public function update_flag(Request $request)
    {
        $flag = $request->flag;
        $report_id = $request->report_id;
        DB::table('report')
                        ->where('report_id', $report_id)
                        ->update(['looks_generated' => $flag]);
        return response()->json(['success'=>'Data is updated successfully']);
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
                   "query":"SELECT DISTINCT YEAR(DIAGNOSIS_DATE) as \\"name\\" FROM STG_TAB_MEDICAL_DATA WHERE DIAGNOSIS_DATE IS NOT NULL ORDER BY \\"name\\" DESC",
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
    public function get_dates($years,$schema,$reporting_year)
    {        
        if($reporting_year == "Service")
        {
            if($schema == "SCH_AHC_UPSON_REGIONAL" || $schema == "SCH_AHC_DESOTO_MEMORIAL")
            {       
            $query ="WITH MED AS(SELECT MAX(DIAGNOSIS_DATE) MAX_DATE_MED,MIN(DIAGNOSIS_DATE) MIN_DATE_MED,1 AS ID FROM STG_TAB_MEDICAL_DATA WHERE YEAR(DIAGNOSIS_DATE)  IN(".$years."))SELECT MED.* FROM MED";
            }
            else
            {
            $query ="WITH MED AS(SELECT MAX(DIAGNOSIS_DATE) MAX_DATE_MED,MIN(DIAGNOSIS_DATE) MIN_DATE_MED,1 AS ID FROM STG_TAB_MEDICAL_DATA WHERE YEAR(DIAGNOSIS_DATE)  IN(".$years.")),PHARMA AS(SELECT MAX(DATE_FILLED) MAX_DATE_PHARMA,MIN(DATE_FILLED) MIN_DATE_PHARMA,1 AS ID FROM STG_TAB_PHARMACY_DATA WHERE YEAR(DATE_FILLED) IN(".$years."))SELECT MED.*,PHARMA.* FROM MED LEFT JOIN PHARMA ON MED.ID=PHARMA.ID";
            }
        }
        else
        {
            if($schema == "SCH_AHC_UPSON_REGIONAL" || $schema == "SCH_AHC_DESOTO_MEMORIAL")
            {       
            $query ="WITH MED AS(SELECT MAX(PAID_DATE) MAX_DATE_MED,MIN(PAID_DATE) MIN_DATE_MED,1 AS ID FROM STG_TAB_MEDICAL_DATA WHERE YEAR(PAID_DATE)  IN(".$years."))SELECT MED.* FROM MED";
            }
            else
            {
            $query ="WITH MED AS(SELECT MAX(PAID_DATE) MAX_DATE_MED,MIN(PAID_DATE) MIN_DATE_MED,1 AS ID FROM STG_TAB_MEDICAL_DATA WHERE YEAR(PAID_DATE)  IN(".$years.")),PHARMA AS(SELECT MAX(PAID_DATE) MAX_DATE_PHARMA,MIN(PAID_DATE) MIN_DATE_PHARMA,1 AS ID FROM STG_TAB_PHARMACY_DATA WHERE YEAR(PAID_DATE) IN(".$years."))SELECT MED.*,PHARMA.* FROM MED LEFT JOIN PHARMA ON MED.ID=PHARMA.ID";
            }
        }
        
        
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
           "schema":"'.$schema.'"
        }',
          CURLOPT_HTTPHEADER => array(
            'x-api-key: nBCbDwJZYe8pLENWbFEjvaWH6tzdOklh5vLXWCVJ',
            'Content-Type: application/json'
          ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
        $resultset = json_decode($response);
        return response()->json(['success'=>$schema,'dates'=>$resultset]);

    }
    public function download_formatted_pdf($folder,$filename)
    {
        $filepath=$folder.'/'.$filename;
        $headers = [
            'Content-Type'        => 'Content-Type: application/zip',
            'Content-Disposition' => 'attachment; filename='.$filename,
        ];

        return \Response::make(Storage::disk('s3')->get($filepath), 200, $headers);
    }
    public function weekly()
    {   
        ini_set('max_execution_time', 4000);
        ini_set("pcre.backtrack_limit", "10000000");
        $lookerSetting = Looker::find('1');
        $api_url = $lookerSetting->api_url;
        $client_id = $lookerSetting->client_id;
        $client_secret = $lookerSetting->client_secret;

        $ReportData= \DB::table('report')
        ->select('report.*')
        // ->where(['report.schedule_time' => date('Y-m-d H:i:s')])
        ->where(['report.looks_generated' => 0])
        ->where(['report.frequency' => 2])
        ->where(['report.is_active' => 1])
        ->get();

        $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
        $method = "POST";
        $resp= $this->curlCall($url, $method);
        $responseData = json_decode($resp,true);
        //call to lookers folder api
        $query = array('access_token' => $responseData['access_token']);
        // echo "<pre>";
        // print_r($ReportData);
        // exit();

        if(isset($ReportData) && !empty($ReportData))
        {
            foreach($ReportData as $keys =>$values)
            {
                if(isset($values->phm_folder_id))
                    {
                    
                    $SectionData= \DB::table('sections')
                    ->select('sections.id','sections.section_title','sections.section_text','sections.section_no')
                    ->join('phm','sections.phm_id','=','phm.id')
                    ->where(['phm.client_id' => $values->phm_folder_id])
                    ->groupBy('sections.id','sections.section_title','sections.section_text','sections.section_no')
                    ->orderBy('section_no')
                    ->get();

                    $SubSecData= \DB::table('sub_sections')
                    ->select('sub_sections.id','sub_sections.sub_section_title','sub_sections.sub_section_text','sub_sections.sub_section_no','sub_sections.section_id','sub_sections.look_id')
                    ->join('sections','sub_sections.section_id','=','sections.id')
                    ->join('phm','sections.phm_id','=','phm.id')
                    ->where(['phm.client_id' => $values->phm_folder_id])
                    ->groupBy('sub_sections.id','sub_sections.sub_section_title','sub_sections.sub_section_text','sub_sections.sub_section_no','sub_sections.section_id','sub_sections.look_id')
                    ->orderBy('section_id')
                    ->get();

                    $SubSectionData=[];
                    $looks_data=[];
                    $looks_data1=[];
                    $look=[];
                    foreach($SubSecData as $key => $value)
                    {
                        if(isset($value->look_id) && !empty($value->look_id)){
                            $url1 = $api_url . "looks/".$value->look_id; 
                            $method1 = "GET";
                            $lookerData= $this->curlCall($url1, $method1,$query); 
                            $lookerData1 = json_decode($lookerData,true);

                            $folder_id = $lookerData1['folder_id'];
                            $model = $lookerData1['model']['id'];

                            $title = $lookerData1['title']."_".$values->report_id."_".auth()->user()->id."_".$values->year;

                            $view = $lookerData1['query']['view'];

                            $create_look_arr=[
                                "model" =>          $model,
                                "view" =>           $view,
                                "title" =>          $title,
                                "folder_id" =>      $values->storeLook_folder_id,
                                "public" =>         true
                            ];


                            $QueriesJson = $lookerData1['query'];
                            unset($QueriesJson['id']);
                            unset($QueriesJson['can']);
                            unset($QueriesJson['url']);
                            unset($QueriesJson['expanded_share_url']);
                            unset($QueriesJson['share_url']);
                            unset($QueriesJson['client_id']);
                            unset($QueriesJson['slug']);

                            foreach($QueriesJson['filters'] as $k => $val)                
                            {
                                if($k == "vw_medical.diagnosis_date" || $k == "vw_med_and_pharma_summary_1.PAID_YEAR" || $k == "vw_risk_group_migration.File_year" || $k == "vw_medical.Paid_year" || $k == "vw_medication_possession_ratio.year" || $k == "vw_medical.diagnosis_year" || $k == "vw_pharmacy.service_year" || $k == "vw_medical.reporting_year" || $k == "vw_pharmacy.reporting_year")
                                {
                                    $QueriesJson['filters'][$k] = "".$values->year."";
                                }
                                if($k == "vw_medical.reporting_date_filter" || $k == "vw_pharmacy.reporting_date_filter")
                                {
                                    $QueriesJson['filters'][$k] = "".$values->reporting_year."";

                                }
                            
                            }


                            $payload = json_encode($QueriesJson);
                           
                            $url2 = $api_url . "queries?fields=id";
                            $authorization = "Authorization: Bearer ".$responseData['access_token']; 
                            $lookerData2= $this->curlCall1($url2, $method, $authorization, $payload); 
                            $lookerData3 = json_decode($lookerData2,true);

                            $create_look_arr['query_id'] = $lookerData3['id'];
                            $create_Look_payload = json_encode($create_look_arr);

                            $url3 = $api_url . "looks";
                            $lookerData4= $this->curlCall1($url3, $method, $authorization, $create_Look_payload); 
                            $lookerData5 = json_decode($lookerData4,true);
                            $SubSectionData[$value->section_id][$key]['section_id'] =$value->section_id;
                            $SubSectionData[$value->section_id][$key]['sub_section_title'] =$value->sub_section_title;
                            $SubSectionData[$value->section_id][$key]['sub_section_text'] =$value->sub_section_text;
                            $SubSectionData[$value->section_id][$key]['sub_section_no'] =$value->sub_section_no;
                            $SubSectionData[$value->section_id][$key]['section_no'] =$value->section_id;
                            $SubSectionData[$value->section_id][$key]['look_id'] =$value->look_id;
                            $SubSectionData[$value->section_id][$key]['look_img_url'] =(isset($lookerData5['image_embed_url']))?$lookerData5['image_embed_url']:"";
                            $SubSectionData[$value->section_id][$key]['embed_url'] = (isset($lookerData5['embed_url']) && !empty($lookerData5['embed_url']))?$lookerData5['embed_url']:"";

                            
                            // $look[]=(isset($lookerData5['image_embed_url']))?$lookerData5['image_embed_url']:"";
                            if(isset($lookerData5['id']) && !empty($lookerData5['id']))
                            {
                            $looks_data['report_id']        = $values->report_id;
                            $looks_data['section_id']       = $value->section_id;
                            $looks_data['sub_section_id']   = $value->id;
                            $looks_data['sub_section_no']   = $value->sub_section_no;
                            $looks_data['look_id']          = $lookerData5['id'];
                            $looks_data['chart_type']       = $lookerData5['query']['vis_config']['type'];
                            $looks_data['embed_url']         = (isset($lookerData5['embed_url']) && !empty($lookerData5['embed_url']))?$lookerData5['embed_url']:"";
                            $looks_data['look_url']         = (isset($lookerData5['image_embed_url']) && !empty($lookerData5['image_embed_url']))?$lookerData5['image_embed_url']:"";
                            Report_look::insert($looks_data);
                            }
                        }
                        else
                        {
                            $looks_data1['report_id']        = $values->report_id;
                            $looks_data1['section_id']       = $value->section_id;
                            $looks_data1['sub_section_id']   = $value->id;
                            $looks_data1['sub_section_no']   = $value->sub_section_no;
                            Report_look::insert($looks_data1);
                        }
                    }                       
                            
                            DB::table('report')->where('report_id', $values->report_id)->update(['looks_generated' => 1]);
                            $id = $values->report_id;
                            $pdf = PDF::loadView('reports.view_look_weekly',compact('SectionData','SubSectionData','id'));
                            $path = 'public/pdf/'.$values->name.'_'.time().'.pdf';
                            DB::table('report')->where('report_id', $values->report_id)->update(['looks_generated' => 2]);
                            Storage::put($path, $pdf->output());
                           
                            
                }
            }
        }
        
    
        
    }
    public function view_weekly()
    {
      ini_set('max_execution_time', 2400);
        $ReportData= \DB::table('report')
        ->select('report.*')
        ->whereNull('report.file_path')
        ->where(['report.looks_generated' => 2])
        ->where(['report.frequency' => 2])
        ->where(['report.is_active' => 1])
        ->get();
        
        if(!empty($ReportData[0])){
            foreach($ReportData as $keys =>$values)
            {
                $id =   $values->report_id;
                $name = $values->name;
                
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


                $SectionData= \DB::table('sections')
                        ->select('sections.*')
                        ->join('phm','sections.phm_id','=','phm.id')
                        ->join('report','phm.client_id','=','report.phm_folder_id')
                        ->where(['report.report_id' => $id])
                        ->orderBy('section_no')
                        ->get();
                 $phmData= \DB::table('phm')
                        ->select('phm.name')
                        ->join('report','phm.client_id','=','report.phm_folder_id')
                        ->where(['report.report_id' => $id])
                        ->get();

                $SubSecData= DB::select("SELECT
                sub_sections.id,
                sub_sections.sub_section_title,
                sub_sections.sub_section_text,
                sub_sections.sub_section_no,
                sub_sections.section_id,
                sub_sections.phm_id,
                sub_sections.long_table,
                sections.section_no,
                report_look.look_url,
                report_look.look_id,
                report_look.chart_type,
                report_look.embed_url
                FROM
                report INNER JOIN report_look ON
                report.report_id = report_look.report_id
                INNER JOIN sub_sections on report_look.sub_section_id = sub_sections.id
                INNER JOIN sections on sub_sections.section_id = sections.id
                WHERE report.report_id = $id
                ORDER by sections.section_no ASC, sub_sections.sub_section_no ASC");
                
                $SubSectionData = [];
               
                foreach($SubSecData as $key => $value)
                {
                    $SubSectionData[$value->section_id][$key]['section_id'] =$value->section_id;
                    $SubSectionData[$value->section_id][$key]['sub_section_title'] =$value->sub_section_title;
                    $SubSectionData[$value->section_id][$key]['sub_section_text'] =$value->sub_section_text;
                    $SubSectionData[$value->section_id][$key]['sub_section_no'] =$value->sub_section_no;
                    $SubSectionData[$value->section_id][$key]['look_id'] =$value->look_id;
                    $SubSectionData[$value->section_id][$key]['sub_section_id'] =$value->id;
                    $SubSectionData[$value->section_id][$key]['chart_type'] =$value->chart_type;
                    $SubSectionData[$value->section_id][$key]['embed_url'] =$value->embed_url;
                    $SubSectionData[$value->section_id][$key]['long_look'] =$value->long_table;
                    if(isset($value->look_url) && $value->look_url != ""){
                        if($value->long_table == 0){
                               
                            try {
                                  $img= imagecreatefrompng($value->look_url); // Load and instantiate the image
                                  if($img) {
                                    $cropped=imagecropauto($img,IMG_CROP_WHITE);
                                    if($cropped !== false){
                                        imagedestroy($img);
                                        ob_start();
                                        imagepng($cropped);
                                        $image = ob_get_contents();
                                        $imgname = $value->look_id.'.png';
                                        $filePath = 'phm_look/' . $imgname;
                                        Storage::disk('s3')->put($filePath, $image); 
                                        ob_end_clean();

                                        $s3 = \Storage::disk('s3');
                                        $client = $s3->getDriver()->getAdapter()->getClient();
                                        $expiry = "+10 minutes";
                                        $imgGetPath = 'phm_look/'.$value->look_id.".png";
                                        $command = $client->getCommand('GetObject', [
                                          'Bucket' => 'kairos-app-storage', // bucket name
                                          'Key'    => $imgGetPath
                                        ]);

                                        $request = $client->createPresignedRequest($command, $expiry);
                                        $imagepath =  (string) $request->getUri(); 
                                        $SubSectionData[$value->section_id][$key]['look_img_url'] =$imagepath;
                                      }
                                      else
                                      {
                                          $SubSectionData[$value->section_id][$keys]['look_img_url'] = "";
                                      }
                                  }
                              }
                              catch(Exception $e) {

                                    echo 'Message: ' .$e->getMessage();                                  
                            }
                        }
                        else
                        {
                            $url4 = $api_url . "looks/".$value->look_id."/run/html"; 
                            $method1 = "GET";
                            $htmlData= $this->curlCall($url4, $method1,$query);
                            $SubSectionData[$value->section_id][$key]['look_img_url'] =$htmlData;
                        }
                    }
                    

                    
                  }
                  // print_r($SubSectionData);
                  // exit();

                  $ReportData= \DB::table('report')
                    ->select('report.year','client_folder_mapping.folder_name','client_folder_mapping.phm_logo')
                    ->join('phm','report.phm_folder_id','=','phm.client_id')
                    ->join('client_folder_mapping','phm.client_id','=','client_folder_mapping.folder_id')
                    ->where(['report.report_id' => $id])
                    ->get();

                    DB::table('report')->where('report_id', $id)->update(['looks_generated' => 3]);
                    // return view('reports.view',compact('SectionData','SubSectionData','phmData','id','ReportData'));
                    $this->generate_pdf($name,$id,$SectionData,$SubSectionData,$phmData);
            }
        }
    }
    public function get_dates_range()
    {
        $date_range=$this->get_dates('2020,2021','SCH_AHC_CRISP_REGIONAL');
        $dats = json_decode($date_range);
        print_r($dats);
        exit();
    }
    public function send_notification($first_name,$email,$file)
    {
        $text             = 'Hi '.$first_name.','."<br/>"."<br/>";
        $text             = $text.'Your Schedule PHM Report Generated successfully.'."<br/>";
        $text             = $text.'Kindly login to  <a href="https://hca.kairosrp.com/">Kairos Platform</a>'." to download it.<br/>";
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
        $mail->AddAttachment(storage_path('app/'.$file));
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
