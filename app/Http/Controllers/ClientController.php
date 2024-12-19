<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Client;
use App\User;
use App\Looker;
use App\users_folder_access;
use App\Looker_parent_dashboards;
use App\Looker_parent_phm;
use App\Snowflake_schema;
use PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response as Download;
use App\Libraries\Helpers;

class ClientController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('clients');
        $this->helper = new Helpers;
			
		/* $userData = User::find('1');
		
		if($userData->is_admin =='0' || $userData->is_admin =='')
		{
			echo $userData->is_admin."ttt";
		
			//return redirect('home')->with('success', 'user created..');
			return redirect()->route('home');
		} */
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
		// $clientData=Client::all();
        $clientData = DB::select("select id,folder_name,schema_name,is_approved,IF(id IN(select id from(
        select distinct a.id,IF(b.id is NOT null,true,false) as phm from(
        (select id,folder_id from client_folder_mapping where type='Client') a
        left JOIN
        (select id,parent_folder_id from client_folder_mapping WHERE type='PHM'
        and parent_folder_id is not null) b
        on a.`folder_id`=b.`parent_folder_id`)) derived where phm=true),true,false) phm
        from client_folder_mapping where type='Client' AND entity_id='".env('env_entity_id')."' AND is_active='1'");
             
        return view('clients.index',compact('clientData','activeMenu', 'activeSubMenu'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
        $activeMenu = '2';
        $activeSubMenu = '0';
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

        // $method1 = 'GET';
        // $folderChildUrl = $api_url . "folders/88/children";
        // $childData= $this->curlCall($folderChildUrl, $method1,$query);
        // $childData= json_decode($childData, true);
        // $folderChild = array();
        // $folderChildArr = array();
        // foreach ($childData as $fldr){
        //     $folderChild['id']= $fldr['id'];
        //     $folderChild['name']= $fldr['name'];
        //     //$folderChild['embed_url']= $fldr['embed_url'];
        //     $folderChildArr[] = $folderChild;
        // }
        $folderChildArr= Looker_parent_phm::select('*')->get()->toArray();
        $folderDataArr = Looker_parent_dashboards::select('*')->get()->toArray();

        // $Sqlquery = "SHOW SCHEMAS";
        // $Schema_nameS = "SCH_KAIROS_ARKANSAS_MUNICIPAL_LEAGUE";
        // $schema_name = json_decode($this->helper->SnowFlack_Call($Sqlquery,$Schema_nameS));

        // $conn = odbc_connect("testodbc1","HIMANSHU","Node2me@git");
        // $sql1="show SCHEMAS";
        // $schema_name = odbc_exec($conn, $sql1);
        $schema_name = Snowflake_schema::select('*')->get();
        $userdata = User::select('id','name','last_name')->where("iss","=","")->get()->toArray();

        $groupArr = $this->getGroups();

        return view('clients.create',compact('activeMenu','activeSubMenu','userdata','schema_name','folderChildArr', 'folderDataArr','groupArr'));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $filePath ="";
        if ($request->hasFile('uploadFile')) {
         $this->validate($request, [
             'uploadFile' => 'dimensions:max_width=225,max_height=50'
             ]);

        $file = $request->file('uploadFile');
        $name = time() .'_'. $file->getClientOriginalName();
        $filePath = 'CLIENT-LOGOS/' . $name;
        Storage::disk('s3')->put($filePath, file_get_contents($file)); 
        }
        Client::insert([
            'folder_id' => $request->folder_id,
            'entity_id' => env('env_entity_id'),
            'folder_name' => $request->folder_name,                    
            'schema_name' => $request->schema_name,            
            'contact_email' => $request->contact_email,
            'external_group_id' => $request->external_group_id,               
            'group_id' => $request->group_id,               
            'models' => $request->models,               
            'access_filters' => $request->access_filters,                
            'is_approved' => $request->is_approved,  
            'logo' =>  $filePath,  
            'type' => 'Client', 
            'created_by' => auth()->user()->id,         
        ]);

        if(!empty($request->phm_folder_id)){

            foreach ($request->phm_folder_id as $key => $value) {
                if(!empty($value)){
                 Client::insert([
                    'folder_id' => $value,
                    'parent_folder_id' => $request->folder_id,
                    'folder_name' => $request->folder_name,                    
                    'schema_name' => $request->schema_name,            
                    'contact_email' => $request->contact_email, 
                    'external_group_id' => $request->external_group_id,               
                    'group_id' => $request->group_id,               
                    'models' => $request->models,               
                    'access_filters' => $request->access_filters,
                    'is_approved' => $request->is_approved,            
                    'type' => 'PHM'         
                ]);
                }
            }
        }
        
		return redirect('clients')->with('success', 'Client has been successfully created!!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
	public function edit($encrypt_id)
    {
        $id = $this->helper->encrypt_decrypt($encrypt_id,'decrypt');
        $activeMenu = '2';
        $activeSubMenu = '0';
       
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

        // $method1 = 'GET';
        // $folderChildUrl = $api_url . "folders/88/children";
        // $childData= $this->curlCall($folderChildUrl, $method1,$query);
        // $childData= json_decode($childData, true);
        // $folderChild = array();
        // $folderChildArr = array();
        // foreach ($childData as $fldr){
        //     $folderChild['id']= $fldr['id'];
        //     $folderChild['name']= $fldr['name'];
        //     //$folderChild['embed_url']= $fldr['embed_url'];
        //     $folderChildArr[] = $folderChild;
        // }

        // $folderDataArr = $this->getFolders();

        $folderChildArr= Looker_parent_phm::select('*')->get()->toArray();
        $folderDataArr = Looker_parent_dashboards::select('*')->get()->toArray();
        
        $clientData = Client::find($id);
        $phmfolderdata= \DB::table('client_folder_mapping')
        ->select('client_folder_mapping.folder_id')
        ->where('parent_folder_id',$clientData->folder_id)
        ->get();
    
        $phmfolderid = [];
        foreach ($phmfolderdata as $key => $value) {
            $phmfolderid[] = $value->folder_id;
        }
        // $conn = odbc_connect("testodbc1","HIMANSHU","Node2me@git");
        // $sql1="show SCHEMAS";
        // $schema_name = odbc_exec($conn, $sql1);

        // $Sqlquery = "SHOW SCHEMAS";
        // $Schema_nameS = "SCH_KAIROS_ARKANSAS_MUNICIPAL_LEAGUE";
        // $schema_name = json_decode($this->helper->SnowFlack_Call($Sqlquery,$Schema_nameS));
        $schema_name = Snowflake_schema::select('*')->get();

        $userdata = User::select('id','name','last_name')->where("iss","=","")->get()->toArray();

        $groupArr = $this->getGroups();

        $logopath ="";
        if(!empty($clientData->logo) && $clientData->logo != Null)
        {
        $s3 = \Storage::disk('s3');
        $client = $s3->getDriver()->getAdapter()->getClient();
        $expiry = "+1 minutes";

        $command = $client->getCommand('GetObject', [
          'Bucket' => 'kairos-app-storage', // bucket name
          'Key'    => $clientData->logo
        ]);

        $request = $client->createPresignedRequest($command, $expiry);
        $logopath =  (string) $request->getUri(); // it will return signed URL
        }
        return view('clients.edit',compact('clientData', 'id', 'activeMenu','activeSubMenu','userdata','schema_name','folderChildArr', 'folderDataArr','phmfolderid','groupArr','logopath'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
	public function update(Request $request, $id)
    {
		$activeMenu = '2';
		$activeSubMenu = '0';
		
        $filePath ="";
        $client = Client::find($id);
        if ($request->hasFile('uploadFile')) {
            $this->validate($request, [
             'uploadFile' => 'dimensions:max_width=225,max_height=50'
             ]);
        $file = $request->file('uploadFile');
        $name = time() .'_'.$request->folder_name.'_'. $file->getClientOriginalName();
        $filePath = 'CLIENT-LOGOS/' . $name;
        Storage::disk('s3')->put($filePath, file_get_contents($file)); 
        $client->logo                 = $filePath;  
        }    
		
		$client->folder_id            = $request->folder_id;
		$client->folder_name          = $request->folder_name;
        $client->schema_name          = $request->schema_name;
        $client->contact_email        = $request->contact_email;
        $client->external_group_id    = $request->external_group_id;              
        $client->group_id             = $request->group_id;             
        $client->models               = $request->models;             
        $client->access_filters       = $request->access_filters;  
        $client->is_approved          = $request->is_approved;	
        $client->updated_by           = auth()->user()->id;     
        $client->save();

        if($request->is_approved == 1 && $request->folder_id != "")
        {
            DB::table('users_folder_access')
            ->where('folder_primary_id', $id)
            ->update(['folder_id' => $request->folder_id]);
        }
        Client::where('type', 'PHM')->where('parent_folder_id', $request->old_folder_id)->delete();
        
        if(!empty($request->phm_folder_id)){
            foreach ($request->phm_folder_id as $key => $value) {
                if(!empty($value)){
                 Client::insert([
                    'folder_id' => $value,
                    'parent_folder_id' => $request->folder_id,
                    'folder_name' => $request->folder_name,                    
                    'schema_name' => $request->schema_name,            
                    'contact_email' => $request->contact_email,                                     
                    'external_group_id' => $request->external_group_id,                                     
                    'group_id' => $request->group_id,                                     
                    'models' => $request->models,                                     
                    'access_filters' => $request->access_filters,                                     
                    'is_approved' => $request->is_approved,            
                    'type' => 'PHM'         
                ]);
                }
            }
        }

        return redirect('clients')->with('success', 'Client has been successfully updated!!');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $user = Client::find($id);
        // $user->delete();
        DB::table('client_folder_mapping')
            ->where('id', $id)
            ->update(['is_active' => '0']);
        return redirect('clients')->with('success','Client has been deleted successfully!!');
    }
    public function getFolders(){
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
        //$url1 = "https://dynpro.cloud.looker.com:443/api/3.1/folders";
        //$url1 = $api_url."folders";
        $url1 = $api_url . "folders/319/children";
        $method1 = "GET";
        $folders= $this->curlCall($url1, $method1,$query);
        $folders= json_decode($folders, true);
        $folderData = array();
        $folderDataArr = array();
        foreach ($folders as $folder){
            $folderData['id']= $folder['id'];
            $folderData['name']= $folder['name'];
            $folderDataArr[] = $folderData;
        }
        return $folderDataArr;
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
    public function getGroups(){
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
        //$url1 = "https://dynpro.cloud.looker.com:443/api/3.1/groups";
        $url2 = $api_url."groups";
        $method2 = "GET";
        $groups= $this->curlCall($url2, $method2,$query);
        $groups= json_decode($groups, true);
                
        $groupData = array();
        $folderDataArr = array();
        foreach ($groups as $group){
            $groupData['id']= $group['id'];
            $groupData['name']= $group['name'];
            $groupDataArr[] = $groupData;
        }
        return $groupDataArr;
       // return $groupData;
    }
}
