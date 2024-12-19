<?php

namespace App\Http\Controllers;
//test
use Illuminate\Http\Request;
use App\Looker;
use App\Looker_data;
use App\Looker_parent_dashboards;
use App\Looker_parent_phm;
use App\Snowflake_schema;
use App\Libraries\Helpers;
use PDF;

class SchedularController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        // $this->middleware('auth');
        $this->helper = new Helpers;
    }
    public function index()
    {
        $activeMenu = '34';
        $id = '1';
        $activeSubMenu = '0';
        $lookerData = Looker::find($id);
        
        return view('looker.edit',compact('lookerData','activeMenu','id','activeSubMenu'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFolder()
    {
    ini_set('max_execution_time', 1200);
        $clients= \DB::table('client_folder_mapping')
        ->select('client_folder_mapping.id','client_folder_mapping.folder_id','client_folder_mapping.folder_name')
        ->where('client_folder_mapping.is_active',1)
        ->where('client_folder_mapping.type',"Client")
        ->whereNotNull('client_folder_mapping.folder_id')
        ->get();
    
        $lookerSetting = Looker::find('1');
        $api_url = $lookerSetting->api_url;
        $client_id = $lookerSetting->client_id;
        $client_secret = $lookerSetting->client_secret;
        $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
        $method = "POST";
        $method1 = "GET";
        $resp= $this->curlCall($url, $method);
        $responseData = json_decode($resp,true);
        $query = array('access_token' => $responseData['access_token']);
        $mainArr1 = [];
        $mainArr2 = [];
        $mainArr3 = [];
        $mainArr4 = [];
        Looker_data::truncate();
        foreach($clients as $key => $value)
        {
            ///GET Dashboards Based ON Client id
            
            $folderChildUrl = $api_url . "folders/".$value->folder_id."/children?fields=id,name,dashboards";
            $childData= $this->curlCall($folderChildUrl, $method1,$query);
            $childData= json_decode($childData, true);   
            if(!empty($childData))
            {         
            $SubFolderDashboards = [];
            foreach ($childData as $fldr){                
                    $mainArr1['client_primary_id'] = $value->id;
                    $mainArr1['client_id'] = $value->folder_id;
                    $mainArr1['client_name'] = $value->folder_name;
                    $mainArr1['folder_id'] = $fldr['id'];
                    $mainArr1['folder_name'] = $fldr['name'];
                    foreach ($fldr['dashboards'] as $keyss => $valuesss) {                    
                        $SubFolderDashboards['dash_id'] = $valuesss['id'];
                        $SubFolderDashboards['title'] = $valuesss['title'];
                        $mainArr2 = array_merge($mainArr1,$SubFolderDashboards);
                        Looker_data::insert($mainArr2);
                    }
                }
            }
            else
            {
                    $mainArr4['client_primary_id']  = $value->id;
                    $mainArr4['client_id']          = $value->folder_id;
                    $mainArr4['client_name']        = $value->folder_name;
                    $mainArr4['folder_id']          = Null;
                    $mainArr4['folder_name']        = Null;
                    $mainArr4['dash_id']            = Null;
                    $mainArr4['title']              = Null;
                    Looker_data::insert($mainArr4);
            }
        }
        return redirect('home')->with('success', 'Data has been successfully updated!!');
    }

    public function getdash()
    {
        ini_set('max_execution_time', 1200);

        $clients= \DB::table('client_folder_mapping')
        ->select('client_folder_mapping.id','client_folder_mapping.folder_id','client_folder_mapping.folder_name')
        ->where('client_folder_mapping.is_active',1)
        ->where('client_folder_mapping.type',"Client")
        ->whereNotNull('client_folder_mapping.folder_id')
        ->get();

        $lookerSetting = Looker::find('1');
        $api_url = $lookerSetting->api_url;
        $client_id = $lookerSetting->client_id;
        $client_secret = $lookerSetting->client_secret;
        $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
        $method = "POST";
        $method1 = "GET";
        $resp= $this->curlCall($url, $method);
        $responseData = json_decode($resp,true);
        $query = array('access_token' => $responseData['access_token']);
        $mainArr1 = [];
        $mainArr2 = [];
        $mainArr3 = [];
        $mainArr4 = [];
        $mainArr5 = [];
        $i=0;
        foreach($clients as $key => $value)
        {
            ///GET Dashboards Based ON Client id
            
            $folderChildUrl = $api_url . "folders/".$value['folder_id']."/children?fields=id,name,dashboards";
            $childData= $this->curlCall($folderChildUrl, $method1,$query);
            $childData= json_decode($childData, true);  
            echo "<pre>";
            print_r($childData);
            exit(); 
            if(!empty($childData))
            {         
            $SubFolderDashboards = [];
            foreach ($childData as $fldr){                
                    $mainArr1['client_primary_id'] = $value['id'];
                    $mainArr1['client_id'] = $value['folder_id'];
                    $mainArr1['client_name'] = $value['folder_name'];
                    $mainArr1['folder_id'] = $fldr['id'];
                    $mainArr1['folder_name'] = $fldr['name'];
                    foreach ($fldr['dashboards'] as $keyss => $valuesss) {                    
                        $SubFolderDashboards['dash_id'] = $valuesss['id'];
                        $SubFolderDashboards['title'] = $valuesss['title'];
                        $mainArr2 = array_merge($mainArr1,$SubFolderDashboards);
                        if($i < 899)
                        {
                        array_push($mainArr3,$mainArr2);
                        }
                        else
                        {
                        array_push($mainArr5,$mainArr2);
                        }
                        $i++;
                    }
                }
            }
            else
            {
                    $mainArr4['client_primary_id']  = $value['id'];
                    $mainArr4['client_id']          = $value['folder_id'];
                    $mainArr4['client_name']        = $value['folder_name'];
                    $mainArr4['folder_id']          = Null;
                    $mainArr4['folder_name']        = Null;
                    $mainArr4['dash_id']            = Null;
                    $mainArr4['title']              = Null;
                    array_push($mainArr3,$mainArr4);
                    if($i < 900)
                    {
                    array_push($mainArr3,$mainArr4);
                    }
                    else
                    {
                    array_push($mainArr5,$mainArr4);
                    }
                    $i++;
            }
        }

        Looker_data::truncate();
        Looker_data::insert($mainArr3);
        if(!empty($mainArr5)){Looker_data::insert($mainArr5);}
        
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
     public function curlCall1($url, $method, $query=null){
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
             "Content-Type: image/png"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        
        //echo $responseData['access_token'];
        return $response;
    }
    public function parent_folder()
    {
            
        $lookerSetting = Looker::find('1');
        $api_url = $lookerSetting->api_url;
        $client_id = $lookerSetting->client_id;
        $client_secret = $lookerSetting->client_secret;

        $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
        $method = "POST";
        $resp= $this->curlCall($url, $method);
        $responseData = json_decode($resp,true);
        //echo $responseData['access_token'];

        //call to lookers folder api
        $query = array('access_token' => $responseData['access_token']);
        //$url1 = $api_url."folders";
        $url1 = $api_url . "folders/4051/children";
        $method1 = "GET";
        $folders= $this->curlCall($url1, $method1,$query);
        $folders= json_decode($folders, true);
        $folderData = array();
        $folderDataArr = array();
        $folderDataArr1 = array();
        foreach ($folders as $folder){
            $folderData['id']= $folder['id'];
            $folderData['name']= $folder['name'];
            $folderDataArr[] = $folderData;
        }
       
        Looker_parent_dashboards::truncate();
        Looker_parent_dashboards::insert($folderDataArr);
        
    }

    public function parent_phm()
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

        $method1 = 'GET';
        // $folderChildUrl = $api_url . "folders/88/children";
        $folderChildUrl = $api_url . "folders/2433/children";
        $childData= $this->curlCall($folderChildUrl, $method1,$query);
        $childData= json_decode($childData, true);

        $folderChildUrl1 = $api_url . "folders/88/children";
        $childData1= $this->curlCall($folderChildUrl1, $method1,$query);
        $childData1= json_decode($childData1, true);

        $folderChild = array();
        $folderChildArr = array();
        foreach ($childData as $fldr){
            $folderChild['id']= $fldr['id'];
            $folderChild['name']= $fldr['name'];
            //$folderChild['embed_url']= $fldr['embed_url'];
            $folderChildArr[] = $folderChild;
        }
        foreach ($childData1 as $fldr1){
            $folderChild1['id']= $fldr1['id'];
            $folderChild1['name']= $fldr1['name'];
            //$folderChild['embed_url']= $fldr['embed_url'];
            $folderChildArr[] = $folderChild1;
        }

        Looker_parent_phm::truncate();
        Looker_parent_phm::insert($folderChildArr);
    }
    public function schema()
    {
        $Sqlquery = "SHOW SCHEMAS";
        $Schema_nameS = "SCH_KAIROS_ARKANSAS_MUNICIPAL_LEAGUE";
        $schema_name = json_decode($this->helper->SnowFlack_Call($Sqlquery,$Schema_nameS));
        $arr = [];
        foreach($schema_name as $val)
                        {
                             $arr[]['schema_name']  = $val;               
                        }
                        
        Snowflake_schema::truncate();
        Snowflake_schema::insert($arr);

    }
    public function refresh_data()
    {
        ini_set('max_execution_time', 2000);
        $this->getFolder();
        $this->parent_folder();
        $this->parent_phm();
        $this->schema();
        return redirect('home')->with('success', 'Data has been successfully updated!!');
    }
    public function test()
    {
        $lookerSetting = Looker::find('1');
        $api_url = $lookerSetting->api_url;
        $client_id = $lookerSetting->client_id;
        $client_secret = $lookerSetting->client_secret;
        
        $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
        $method = "POST";
        $resp= $this->curlCall($url, $method);
        $responseData = json_decode($resp,true);
        //call to lookers folder api
        
        print_r($responseData);
        exit();


        $query = array('access_token' => $responseData['access_token']);
        $url1 = $api_url . "looks/3633"; 
        $method1 = "GET";
        $lookerData= $this->curlCall($url1, $method1,$query); 
        $lookerData1 = json_decode($lookerData,true);

        $imgurl = $lookerData1['image_embed_url'];
        $data = [
            'url' => $imgurl,
        ];
        $pdf = PDF::loadView('test',$data);
        return $pdf->stream('abc.pdf');
    
    }
   
}
