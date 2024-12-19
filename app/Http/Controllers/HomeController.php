<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Looker;
use App\Client_folder_mapping;
use App\users_folder_access;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response as Download;
use Illuminate\Support\Facades\View;
use App\Libraries\Helpers;

class HomeController extends Controller
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
        $this->helper->autologout();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {    	
		$user_id = auth()->user()->id;
		$activeMenu = '1';
		$activeSubMenu = 1;
		$folderAccess = users_folder_access::select('users_folder_access.folder_id','users_folder_access.folder_primary_id')
		->join('client_folder_mapping','users_folder_access.folder_primary_id','=','client_folder_mapping.id')
		->where("users_folder_access.user_id","=",$user_id)
		->where("client_folder_mapping.entity_id","=",env('env_entity_id'))
		->orderBy('client_folder_mapping.folder_name')
		->get()->toArray();
		if(!empty($folderAccess))
		{
			$folderAccess1 = array_column($folderAccess, 'folder_id');
	        $folderAccess2 = array_column($folderAccess, 'folder_primary_id');
			$folder_id = $folderAccess1[0];
			$primary_folder_id = $folderAccess2[0];
			return $this->processDashboard($this->helper->encrypt_decrypt($folder_id),$this->helper->encrypt_decrypt($primary_folder_id));
		}
		else
		{
			return view('blank',compact('activeMenu', 'activeSubMenu'));
		}
        
		
		
	}
	public function processDashboard($encry_folder_id,$encrypt_primary_folder_id)
    {
    	$user_id = auth()->user()->id;
    	$folder_id = $this->helper->encrypt_decrypt($encry_folder_id,'decrypt');
    	$primary_folder_id = $this->helper->encrypt_decrypt($encrypt_primary_folder_id,'decrypt');
		$activeMenu = '1';
		$activeSubMenu = $primary_folder_id;
		//call to looker`s login api
		$lookerSetting = Looker::find('1');
		$api_url = $lookerSetting->api_url;
		$client_id = $lookerSetting->client_id;
		$client_secret = $lookerSetting->client_secret;


		$chkAccess= \DB::table('temp_restricted_access')
                ->select('*')
                ->where(['user_id' => $user_id])
                ->get();
          
		$url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
		$method = "POST";
		$resp= $this->curlCall($url, $method);
		$responseData = json_decode($resp,true);
		
		if(!empty($responseData))
		{
		//call to lookers folder api
		$query = array('access_token' => $responseData['access_token']);
		//$url1 = "https://dynpro.cloud.looker.com:443/api/3.1/folders/30";
		$url1 = $api_url . "folders/".$folder_id;
		$method1 = "GET";
		$lookerData= $this->curlCall($url1, $method1,$query);
		$lookerData= json_decode($lookerData, true);

		$lookerDashboards = array();
		$lookerDashboardsArr = array();
		$reportFlag = 0;
		foreach ($lookerData['dashboards'] as $dash){
			if(empty($chkAccess[0]))
			{
			$lookerDashboards['id']= $dash['id'];
			$lookerDashboards['title']= $dash['title'];
			$lookerDashboardsArr[] = $lookerDashboards;				
			}
			else
			{
				$reportFlag = 1;
			}
		}

		$lookerLooks = array();
		$lookerLooksArr = array();
		foreach ($lookerData['looks'] as $look){
			$lookerLooks['id']= $look['id'];
			$lookerLooks['title']= $look['title'];
			$lookerLooks['embed_url']= $look['embed_url'];
			$lookerLooksArr[] = $lookerLooks;
		}
		// print_r($folder_id);
		

		$phmForClient = \DB::table('client_folder_mapping')
        ->select('*')
        ->where('folder_id' ,'=', $folder_id)
        ->get();

        $userDashAccess = \DB::table('grp_role_usr_mapping')
        ->select('users_dasboards_mapping.client_primary_id','users_dasboards_mapping.client_id','users_dasboards_mapping.dashboard_id','users_dasboards_mapping.sub_dashboard_id')
        ->join('users_dasboards_mapping','grp_role_usr_mapping.grp_usr_mapping_id','=','users_dasboards_mapping.grp_usr_mapping_id')
        ->where('grp_role_usr_mapping.user_id' ,'=', auth()->user()->id)
        ->where('users_dasboards_mapping.client_id' ,'=', $folder_id)
        ->get();
        $dashArr = [];
        $subDashArr = [];
        foreach($userDashAccess as $kk =>$dash)
        {
        	$dashArr[] = $dash->dashboard_id;
        	$subDashArr[] = $dash->sub_dashboard_id;
        }
        $dashArr1 = array_unique($dashArr);
        
   
        // echo "<pre>";
        // print_r($dashArr1);
        // exit();
		$folderChildUrl = $api_url . "folders/".$folder_id."/children";
		$childData= $this->curlCall($folderChildUrl, $method1,$query);
		$childData= json_decode($childData, true);
		$folderChild = array();
		$folderChildArr = array();
		$SubFolderDashboards = [];
		foreach ($childData as $fldr){
			if(in_array($fldr['id'], $dashArr1))
			{
				$folderChild['id']= $fldr['id'];
				$folderChild['name']= $fldr['name'];
				//$folderChild['embed_url']= $fldr['embed_url'];
				$folderChildArr[] = $folderChild;
				foreach ($fldr['dashboards'] as $keyss => $valuesss) {
					if(in_array($valuesss['id'], $subDashArr))
					{
						$SubFolderDashboards[$fldr['id']][$keyss]['id'] = $valuesss['id'];
						$SubFolderDashboards[$fldr['id']][$keyss]['title'] = $valuesss['title'];
					}
				}
			}
			
		}
		$result1 = \DB::table('client_folder_mapping')->select('folder_name')->where('folder_id', $lookerData['parent_id'])->first();


		// get PHM Upload copy 
		$PhmReportData= \DB::table('users')
        ->select('client_folder_mapping.folder_id','phm.name','phm.file_path')
        ->join('users_folder_access','users.id','=','users_folder_access.user_id')
        ->join('client_folder_mapping','users_folder_access.folder_id','=','client_folder_mapping.parent_folder_id')
        ->join('phm','client_folder_mapping.folder_id','=','phm.client_id')
        ->where('users.id',auth()->user()->id)
        ->where('client_folder_mapping.parent_folder_id',$folder_id)
        ->whereNotNull('phm.file_path')
        ->groupBy('users_folder_access.folder_id','client_folder_mapping.folder_id','phm.name','phm.file_path')
        ->get();

        // GET Image path based On Client
		$LogoPath= \DB::table('client_folder_mapping')
        ->select('client_folder_mapping.logo','client_folder_mapping.folder_name')
        ->where('client_folder_mapping.id',$primary_folder_id)
        ->get();
        $logopathURL = "";
        if(!empty($LogoPath[0]->logo) && $LogoPath[0]->logo != Null)
                    {
                    $s3 = \Storage::disk('s3');
                    $client = $s3->getDriver()->getAdapter()->getClient();
                    $expiry = "+360 minutes";

                    $command = $client->getCommand('GetObject', [
                      'Bucket' => 'kairos-app-storage', // bucket name
                      'Key'    => $LogoPath[0]->logo
                    ]);

                    $request = $client->createPresignedRequest($command, $expiry);
                    $logopathURL =  (string) $request->getUri(); // it will return signed URL
                    }
 
		$breadCrumb['parent_id'] = $lookerData['parent_id'];
		if($result1){
		$breadCrumb['parent_name'] = $result1->folder_name;
		}else{
			$breadCrumb['parent_name'] = '';
		}
		$breadCrumb['id'] = $lookerData['id'];
		$breadCrumb['name'] = $lookerData['name'];
		$breadCrumbArr[] = $breadCrumb;
		//print_r($breadCrumb1); exit;
		$style_array = ['#b8efec','blue','pink'];
		$color_array = ['bg-success','bg-info','bg-warning','bg-danger','bg-success','bg-info','bg-warning','bg-danger'];
		$msg = "";

		if(!empty($LogoPath[0]->folder_name) && $LogoPath[0]->folder_name != Null){
		$Client_Name_Title=$LogoPath[0]->folder_name;
		}
		View::share('logoImgPath', $logopathURL);
        return view('home',compact('breadCrumbArr','phmForClient','activeMenu', 'activeSubMenu', 'folderChildArr', 'lookerDashboardsArr','lookerLooksArr','SubFolderDashboards','color_array','style_array','msg','PhmReportData','folder_id','reportFlag','Client_Name_Title'));
		}
		else
		{
			$reportFlag = 0;	
			$msg = "Contact To administrator";
        	return view('home',compact('activeMenu', 'activeSubMenu','msg','reportFlag'));
		}
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
			// 	"Content-Type: application/x-www-form-urlencoded"
			// ),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		
		//echo $responseData['access_token'];
		return $response;
	}

	/**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
   
	 public function deleteLookerUser(Request $request)
	 {
	 	$user_id = auth()->user()->id;

	 	$CheckUserLogin = \DB::table('account_master')
        ->select('*')
        ->where(['actual_user'=>$user_id,'flag' =>1,'is_active'=>1])
        ->get();

        if(!empty($CheckUserLogin[0]))
        {        	
        	\DB::table('account_master')->where('account_id', $CheckUserLogin[0]->account_id)->update(['flag' => 0,'actual_user'=>Null]);
        }
		// $first_name = $request->user()->name;
		// $last_name = $request->user()->last_name;

		
		// //$url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
		// $lookerSetting = Looker::find('1');
  //       $api_url = $lookerSetting->api_url;
  //       $client_id = $lookerSetting->client_id;
  //       $client_secret = $lookerSetting->client_secret;
  //       $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
		// $method = "POST";
		// $resp= $this->curlCall($url, $method);
		// $responseData = json_decode($resp,true);
		// //call to lookers folder api
		// $query = array('access_token' => $responseData['access_token']);
		// $url1 = "https://dynpro.cloud.looker.com:443/api/3.1/users/search?first_name=".$first_name."&last_name=".$last_name;
		// //$url1 = $api_url . "folders/".$folder_id;
		// $method1 = "GET";
		// $usrData= $this->curlCall($url1, $method1,$query);
		// $usrData= json_decode($usrData, true);

		// foreach($usrData as $data){
			
		// 	if(!empty($data['credentials_embed']))
		// 	{
		// 		if($data['credentials_embed'][0]['type']=='embed' && $request->user()->external_user_id == $data['credentials_embed'][0]['external_user_id'])
		// 		{
		// 			$url2 = "https://dynpro.cloud.looker.com:443/api/3.1/users/".$data['id'];
		// 			$method2 = "DELETE";
		// 			$deleteData= $this->curlCall($url2, $method2,$query);
		// 		}
		// 	//$deleteData= json_decode($deleteData, true);
		// 	}
		// }
	 }




    public function getDashboard(Request $request)
 	{
 	$user_id =$request->user()->id;
 	$user_group_id = $request->user()->user_group_id; 	
	$dashboard_id = $request->dashboard_id;
	$client_id = $request->client_id;
	$FolderDtl = $this->getFolderDtl($client_id);	
	$ToggleAccess = $request->Access;

	// $first_name = $request->user()->name;
	// $last_name = $request->user()->last_name;
	// $external_user_id = $request->user()->external_user_id;
	$models = $FolderDtl[0]->models;
	$group_id = intval($FolderDtl[0]->group_id);
	$external_group_id = $FolderDtl[0]->external_group_id;
	$user_attributes = $request->user()->user_attributes;
	if($ToggleAccess != "")
	{
		if($ToggleAccess == "true"){$user_permission="explorer";}else{$user_permission="viewer";}
	}
	else
	{
	$user_permission = "viewer";		
	}



	/* ========Pull Tabe Logic Start============== */
	$PullTable_Group_Id = 0;
	$permission = "";
	$first_name = "";
	$last_name = "";
	$external_user_id = "";
	$LicenceMessage = "";
   
	if($user_group_id == "1001" || $user_group_id == "1003" || $user_group_id == "1011"){$PullTable_Group_Id =1;}
	else{$PullTable_Group_Id =2;}
     $PullTable_Group_Id =1;

	if($user_permission == "explorer" || $user_permission == "schedular"){$permission = "explorer";}else{	$permission = "viewer";}

	/* ========Check User Already Logged in or Not============== */
	$CheckUserLogin = \DB::table('account_master')
        ->select('*')
        ->where(['group' => $PullTable_Group_Id,'actual_user'=>$user_id,'flag' =>1,'permission' =>$permission,'is_active'=>1])
        ->get();

    if(!empty($CheckUserLogin[0]))
    {
    	/* ========If user Already Logged In set same user from Pull Table=================== */
    	$first_name = $CheckUserLogin[0]->first_name;
		$last_name = $CheckUserLogin[0]->last_name;
		$external_user_id = $CheckUserLogin[0]->external_user_id;
		\DB::table('account_master')->where('account_id', $CheckUserLogin[0]->account_id)->update(['updated_at' => date('Y-m-d G:i:s')]);
    }
    else
    {
    	/* ========else user Already Not Logged In set New user from Pull Table============== */
    	$AvailableUsers = \DB::table('account_master')
        ->select('*')
        ->where(['group' => $PullTable_Group_Id,'flag' =>0,'permission' =>$permission,'is_active'=>1])
        ->get();

        if(!empty($AvailableUsers[0]))
        {
    	/* ========Assign User from Pull Table============== */
	    	$first_name = $AvailableUsers[0]->first_name;
			$last_name = $AvailableUsers[0]->last_name;
			$external_user_id = $AvailableUsers[0]->external_user_id;
			\DB::table('account_master')->where('account_id', $AvailableUsers[0]->account_id)->update(['flag' => 1,'actual_user'=>$user_id,'updated_at' => date('Y-m-d G:i:s')]);
        }
        else
        {
    	/* ========User Not Avaible Show Message============== */
    		$LicenceMessage = "All user licences are being used at the current moment. Please try again after sometime.";
    		return response()->json(['success'=>'Data is successfully added','url'=>"",'LicenceMessage'=>$LicenceMessage]);
        }

       
    }

	/* ========Pull Tabe Logic END================ */

	$api_url = config('looker_setting.api_url');
	if($permission =='viewer'){
		$permission1 = config('looker_setting.viewer');
	}
	else if($permission =='schedular'){
		$permission1 = config('looker_setting.schedular');
	}
	else{
		$permission1 = config('looker_setting.explorer');
	}

	$lookerSetting = Looker::find('1');
	$host = $lookerSetting->host;
	$secret = $lookerSetting->secret;
	//$secret = "e9294885fad0c1790f85af3b547b54cc70ea7040badd7341f84678928785103b";
	$embedpath= "/embed/dashboards/".$dashboard_id;
	//$host = "dynpro.cloud.looker.com";
	$path = "/login/embed/" . urlencode($embedpath);

	$json_nonce = json_encode(md5(uniqid()));
	$json_current_time = json_encode(time());
	$json_session_length = json_encode(3600);
	$json_external_user_id = json_encode($external_user_id);
	$json_first_name = json_encode($first_name);
	$json_last_name = json_encode($last_name);
	//$json_permissions = json_encode( array ( "see_user_dashboards", "see_lookml_dashboards", "access_data", "see_looks" ) );
	$json_permissions = json_encode( $permission1 );
	$json_models = json_encode( array ( $models ) );
	$json_group_ids = json_encode( array ( $group_id ) );  // just some example group ids
	$json_external_group_id = json_encode($external_group_id);
	$json_user_attributes = json_encode( array ( "an_attribute_name" => "my_value") );  // just some example attributes
	// NOTE: accessfilters must be present and be a json hash. If you don't need access filters then the php
	// way to make an empty json hash as an alternative to the below seems to be:
	 $accessfilters = new \stdClass();
	 //$accessfilters = (object) ['a' => 'new object'];
	/* $accessfilters = array (
	  "<your_model_name>"  =>  array ( "view_name.dimension_name" => "<value>" )
	); */
	$json_accessfilters = json_encode($accessfilters);

	$stringtosign = "";
	$stringtosign .= $host . "\n";
	$stringtosign .= $path . "\n";
	$stringtosign .= $json_nonce . "\n";
	$stringtosign .= $json_current_time . "\n";
	$stringtosign .= $json_session_length . "\n";
	$stringtosign .= $json_external_user_id . "\n";
	$stringtosign .= $json_permissions . "\n";
	$stringtosign .= $json_models . "\n";
	$stringtosign .= $json_group_ids . "\n";
	$stringtosign .= $json_external_group_id . "\n";
	$stringtosign .= $json_user_attributes . "\n";
	$stringtosign .= $json_accessfilters;

	$signature = trim(base64_encode(hash_hmac("sha1", utf8_encode($stringtosign), $secret, $raw_output = true)));
	// , $raw_output = true

	$queryparams = array (
		'nonce' =>  $json_nonce,
		'time'  =>  $json_current_time,
		'session_length'  =>  $json_session_length,
		'external_user_id'  =>  $json_external_user_id,
		'permissions' =>  $json_permissions,
		'models'  =>  $json_models,
		'group_ids' => $json_group_ids,
		'external_group_id' => $json_external_group_id,
		'user_attributes' => $json_user_attributes,
		'access_filters'  =>  $json_accessfilters,
		'first_name'  =>  $json_first_name,
		'last_name' =>  $json_last_name,
		'force_logout_login'  =>  false,
		'signature' =>  $signature
	);

	$querystring = "";
	foreach ($queryparams as $key => $value) {
	  if (strlen($querystring) > 0) {
		$querystring .= "&";
	  }
	  if ($key == "force_logout_login") {
		$value = "true";
	  }
	  $querystring .= "$key=" . urlencode($value);
	}

	$final = "https://" . $host . $path . "?" . $querystring;
    return response()->json(['success'=>'Data is successfully added111','url'=>$final,'LicenceMessage'=>$LicenceMessage]);
 }

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPhmReport($folder_id)
    {
        $activeMenu = '2322';
        $activeSubMenu = '77889';
		
        $phmData= \DB::table('phm')
        ->select('phm.*','client_folder_mapping.folder_name')
        ->join('client_folder_mapping','phm.client_id','=','client_folder_mapping.folder_id')
        ->where(['client_id' => $folder_id, 'is_master' => 0])
        ->get();

        return view('phm.showPhm',compact('activeMenu', 'activeSubMenu','phmData'));
    }
    public function iframe()
    {
    	return view('iframe');
    }
    public function getFolderDtl($folder_id)
    {
    	$FolderDtl = \DB::table('client_folder_mapping')
        ->select('*')
        ->where('folder_id' ,'=', $folder_id)
        ->get();

        return $FolderDtl;
    }
    public function getPHMReport($folder,$filename)
    {
    	$filepath=$folder.'/'.$filename;
        $headers = [
            'Content-Type'        => 'Content-Type: application/zip',
            'Content-Disposition' => 'attachment; filename='.$filename,
        ];

        return \Response::make(Storage::disk('s3')->get($filepath), 200, $headers);
    }
    public function chk()
	{
		$first_name = 'Mahi';
		$last_name = 'Kapadane';
		
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
		$url1 = "https://dynpro.cloud.looker.com:443/api/3.1/users/search?first_name=".$first_name."&last_name=".$last_name;
		$method1 = "GET";
		$usrData= $this->curlCall($url1, $method1,$query);
		$usrData1= json_decode($usrData, true);
		foreach($usrData1 as $data){
			
			if(!empty($data['credentials_embed']))
			{
				if($data['credentials_embed'][0]['type']=='embed' && 17 == $data['credentials_embed'][0]['external_user_id'])
				{
					$url2 = "https://dynpro.cloud.looker.com:443/api/3.1/users/".$data['id'];
					$method2 = "DELETE";
					$deleteData= $this->curlCall($url2, $method2,$query);
				}
			//$deleteData= json_decode($deleteData, true);
			}
		}
		// echo "<pre>";
		// print_r($usrData);
	}

	public function FreePullTable(Request $request)
	{
		$user_id = $request->user_id;
		$access = $request->access;
		$u_access ="";
		if($access == "true"){$u_access = "viewer";}else{$u_access = "explorer";}
		\DB::table('account_master')->where('actual_user', $user_id)->where('permission', $u_access)->update(['flag' => 0,'actual_user'=>Null]);
	} 
	public function metabase()
	{
		return view('iframe');
	}
    

}
