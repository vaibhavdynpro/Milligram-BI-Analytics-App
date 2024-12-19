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
use App\Groups;
use App\Grp_role_usr_mapping;
use App\Users_dasboards_mapping;
use PHPMailer\PHPMailer;
use App\Libraries\Helpers;

class UsersController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
		// $this->middleware('admin');
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
		// $userData=User::all();
        // $userData = DB::select("select * FROM users WHERE is_active='1' AND entity_id = '".env('env_entity_id')."' OR role = 1");
        $userData =\DB::table('users')
        ->select('users.*','roles.role as role_name','groups.group_name')
        ->leftjoin('roles','users.role','=','roles.role_id')
        ->leftjoin('groups','users.user_group_id','=','groups.group_id')
        ->where('users.is_active',1)
        ->where('users.entity_id',env('env_entity_id'))
        ->where('users.is_signup',0)
        ->orWhere('users.role',1)
        ->get();
         
        $listData = [];
        if(auth()->user()->role == 1)
        { 
        $listData =\DB::table('users')
        ->select('users.*','roles.role as role_name','groups.group_name')
        ->leftjoin('roles','users.role','=','roles.role_id')
        ->leftjoin('groups','users.user_group_id','=','groups.group_id')
        ->where('users.is_active',1)
        ->where('users.entity_id',env('env_entity_id'))
        ->where('users.is_signup',1)
        ->get();
        }
   
        $session_data = DB::select("SELECT user_id,last_activity,id FROM sessions s1 WHERE last_activity = (SELECT MAX(last_activity) FROM sessions s2 WHERE s1.user_id = s2.user_id) ORDER BY last_activity"); 
        return view('users.index',compact('userData','session_data','activeMenu', 'activeSubMenu','listData'));
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
        $folderDataArr = $this->getFolders();
		$groupArr = $this->getGroups();
        $roleData=$this->getRoles();
        
        $loooker_data= \DB::table('looker_data')
        ->select('looker_data.client_primary_id','looker_data.client_id','looker_data.client_name')
        ->join('client_folder_mapping','looker_data.client_primary_id','=','client_folder_mapping.id')
        ->where('client_folder_mapping.is_active',1)
        ->where('client_folder_mapping.type',"Client")
        ->where('client_folder_mapping.entity_id',env('env_entity_id'))
        ->whereNotNull('client_folder_mapping.folder_id')
        ->distinct()
        ->orderBy('looker_data.client_name')
        ->get();     
        $ClientDash = [];
        $ClientSubDash = [];
        foreach($loooker_data as $key => $value)
        {
            $ClientDash[$key]['id']         = $value->client_primary_id;
            $ClientDash[$key]['folder_id']  = $value->client_id;
            $ClientDash[$key]['name']       = $value->client_name;


            $folder_data= \DB::table('looker_data')
            ->select('looker_data.folder_id','looker_data.folder_name')
            ->where('looker_data.client_primary_id',$value->client_primary_id)
            ->where('looker_data.client_id',$value->client_id)
            ->distinct()
            ->get(); 

            $folderChildArr=[];
            foreach($folder_data as $keys => $values)
            {
                if($values->folder_id != "" && isset($values->folder_id))
                {
                    $folderChild['id']      = $values->folder_id;
                    $folderChild['name']    = $values->folder_name;
                    $folderChildArr[]       = $folderChild;

                    $dashboard_data= \DB::table('looker_data')
                    ->select('looker_data.dash_id','looker_data.title')
                    ->where('looker_data.folder_id',$values->folder_id)
                    ->distinct()
                    ->get(); 
                    foreach($dashboard_data as $keyss => $valuess)
                    {
                        $SubFolderDashboards[$values->folder_id][$keyss]['id'] = $valuess->dash_id;
                        $SubFolderDashboards[$values->folder_id][$keyss]['title'] = $valuess->title;
                    }
                }
            }
            $ClientDash[$key]['dashboard']      = $folderChildArr;
            $ClientSubDash[$key]['dashboard']   = $SubFolderDashboards;
        }

        $roleData = \DB::table('roles')
        ->select('*')
        ->where('is_active',1)
        ->get();

        $GroupData = DB::table('groups')
        ->select('group_id','group_name')
                    ->where('is_active', 1)
                    ->where('groups.entity_id',env('env_entity_id'))
                    ->get();
        return view('users.create',compact('activeMenu','ClientDash','roleData','ClientSubDash', 'folderDataArr', 'groupArr', 'activeSubMenu','roleData','GroupData'));
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


    public function getFolders(){
  //       $lookerSetting = Looker::find('1');
		// $api_url = $lookerSetting->api_url;
		// $client_id = $lookerSetting->client_id;
		// $client_secret = $lookerSetting->client_secret;

  //       $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
		// $method = "POST";
		// $resp= $this->curlCall($url, $method);
		// $responseData = json_decode($resp,true);
		// //echo $responseData['access_token'];

		// //call to lookers folder api
  //       $query = array('access_token' => $responseData['access_token']);
  //       //$url1 = "https://dynpro.cloud.looker.com:443/api/3.1/folders";
		// //$url1 = $api_url."folders";
  //       $url1 = $api_url . "folders/319/children";
		// $method1 = "GET";
		// $folders= $this->curlCall($url1, $method1,$query);
		// $folders= json_decode($folders, true);
		// $folderData = array();
		// $folderDataArr = array();
		// foreach ($folders as $folder){
		// 	$folderData['id']= $folder['id'];
		// 	$folderData['name']= $folder['name'];
		// 	$folderDataArr[] = $folderData;
  //       }
  //       return $folderDataArr;

         $phmfolderdata= \DB::table('client_folder_mapping')
        ->select('client_folder_mapping.id','client_folder_mapping.folder_id','client_folder_mapping.folder_name')
        ->where('type','Client')
        ->where('client_folder_mapping.is_active',1)
        ->where('client_folder_mapping.entity_id',env('env_entity_id'))
        ->whereNotNull('folder_id')
        ->get();
        $phmfolderid = [];
        foreach ($phmfolderdata as $key => $value) {
            $phmfolderid[$key]['id']        = $value->id;
            $phmfolderid[$key]['folder_id'] = $value->folder_id;
            $phmfolderid[$key]['name']      = $value->folder_name;
        }
        return $phmfolderid;
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
     * Send email on user creation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function send_welcome_mail($first_name,$email,$password){

        $text             = 'Hi '.$first_name.','."<br/>"."<br/>";
        $text             = $text.'Welcome to kairos.Your Credentials for the platform are:'."<br/>";
        $text             = $text.'Site: <a href="https://hca.kairosrp.com/">Kairos App</a>'."<br/>";
        $text             = $text.'UserName: '.$email."<br/>";
        $text             = $text.'Password: '.$password."<br/>"."<br/>";
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
        $mail->Subject = "Welcome to Kairos";
        $mail->Body    = $text;
        $mail->AddAddress($email, "New User");
        $check=1;
        // $mail->Send();
        if ($mail->Send()) {
        
            return true;
        }
        else{
            return false;
        }

    }

    public function send_user_mail(Request $request){
        $userData = User::find($request->id);
        $this->send_welcome_mail($userData->name,$userData->email,'testing');
        return redirect('users')->with('success', 'Credentials Sent');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

		// $is_admin = ($request->is_admin == "on")? '1':'0';
        $userDtl = DB::table('users')         
            ->orderBy('id','DESC')
            ->limit(1)
            ->get()
            ->toArray();
        $extr_usr_id= $userDtl[0]->external_user_id + 1;


        $group_id = "";
        if($request->groupFlag == 1)
        {
            $group_id = Groups::create([
                'group_name'        => $request->group,
                'entity_id'         => env('env_entity_id'),
                'created_by'        => auth()->user()->id,             
            ])->group_id;            
        }
        else
        {
            $group_id = $request->groupDD;
        }

		$id = User::create([
            'name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
            'user_group_id' => $group_id,
            'entity_id' => env('env_entity_id'),
            'external_user_id' => $extr_usr_id,
            'permissions' => $request->permissions,
            'user_attributes' => $request->user_attributes,
            'password' => Hash::make($request->password),
            'created_by' => auth()->user()->id,
            
        ])->id;

        $RoleDtl =$this->getRoleDtl($request->role);

            /*============Remove existing records==============*/ 
            $query = 'DELETE grp_role_usr_mapping,users_dasboards_mapping FROM grp_role_usr_mapping 
            INNER JOIN users_dasboards_mapping ON grp_role_usr_mapping.grp_usr_mapping_id = users_dasboards_mapping.grp_usr_mapping_id  
            WHERE grp_role_usr_mapping.user_id = ?';
            \DB::delete($query, array($id));
            users_folder_access::where('user_id', $id)->delete();


            /*============Generate Unique Id==============*/ 

            $user_unique_id = $group_id.'-'.$RoleDtl[0]->role_unique_id.'-'.$id;

            /*============Store Group Role User Mapping==============*/ 
            $grp_usr_mapping_id = Grp_role_usr_mapping::create([
                'user_id'           => $id,
                'group_id'          => $group_id,
                'role_id'           => $request->role,
                'user_unique_id'    => $user_unique_id,
                'users'             => ($request->users == "on")?1:0,
                'looker'            => ($request->looker == "on")?1:0,
                'snowflake'         => ($request->snowflake == "on")?1:0,
                'roles'             => ($request->roles == "on")?1:0,
                'group_module'      => ($request->group_module == "on")?1:0,
                'clients'           => ($request->clients == "on")?1:0,
                // 'dashboards'        => ($request->dashboards == "on")?1:0,
                'reports'           => ($request->reports == "on")?1:0,
                'user_add'          => ($request->Usersadd == "on")?1:0,
                'user_edit'         => ($request->Usersedit == "on")?1:0,
                'user_delete'       => ($request->Usersdelete == "on")?1:0,
                'user_view'         => ($request->Usersview == "on")?1:0,
                'client_add'        => ($request->Clientadd == "on")?1:0,
                'client_edit'       => ($request->Clientedit == "on")?1:0,
                'client_delete'     => ($request->Clientdelete == "on")?1:0,
                'client_view'       => ($request->ClientView == "on")?1:0,
                'role_add'          => ($request->role_add == "on")?1:0,
                'role_edit'         => ($request->role_edit == "on")?1:0,
                'role_delete'       => ($request->role_delete == "on")?1:0,
                'role_view'         => ($request->role_view == "on")?1:0,
                'group_add'         => ($request->group_add == "on")?1:0,
                'group_edit'        => ($request->group_edit == "on")?1:0,
                'group_delete'      => ($request->group_delete == "on")?1:0,
                'group_view'        => ($request->group_view == "on")?1:0,
                'report_add'        => ($request->report_add == "on")?1:0,
                'report_edit'       => ($request->report_edit == "on")?1:0,
                'report_delete'     => ($request->report_delete == "on")?1:0,
                'report_view'       => ($request->report_view == "on")?1:0,
                'generate_report'       => ($request->gene_report == "on")?1:0,
                'generate_report_add'   => ($request->gene_report_add == "on")?1:0,
                'generate_report_edit'  => ($request->gene_report_edit == "on")?1:0,
                'generate_report_delete'=> ($request->gene_report_delete == "on")?1:0,
                'generate_report_view'  => ($request->gene_report_view == "on")?1:0,
                'invite_user'  => ($request->invite_user == "on")?1:0,
                'created_by'        => auth()->user()->id,             
            ])->grp_usr_mapping_id;


            /*============Store User Dashboard Mapping==============*/ 
            if(!empty($request->sub_dashboards_id))
            {
                foreach($request->sub_dashboards_id as $key => $value)
                {
                    $arr = explode("_",$value);            
                    $usr_dash_id = Users_dasboards_mapping::create([
                    'grp_usr_mapping_id'           => $grp_usr_mapping_id,
                    'client_primary_id'            => (isset($arr[0]))?$arr[0]:null,
                    'client_id'                    => (isset($arr[1]))?$arr[1]:null,
                    'dashboard_id'                 => (isset($arr[2]))?$arr[2]:null,
                    'sub_dashboard_id'             => (isset($arr[3]))?$arr[3]:null,
                    'created_by'                   => auth()->user()->id,             
                    ])->usr_dash_id;
                }

                foreach($request->clientids as $keyy => $valuee)
                {
                    $arr1 = explode("_",$valuee);
                    $usr_fdr_id = users_folder_access::create([
                    'user_id'               => $id,
                    'folder_id'             => (isset($arr1[0]))?$arr1[1]:null,
                    'folder_primary_id'     => (isset($arr1[1]))?$arr1[0]:null,
                    'created_by'            => auth()->user()->id,             
                    ])->usr_fdr_id;
                }
            }
            $user                   = User::find($id);        
            $user->unique_id        = $user_unique_id;
            $user->user_group_id    = $group_id;
            $user->role             = $request->role;
            $user->save();

        return redirect('users')->with('success', 'User has been successfully created & Email Sent!!');
        

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
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
        $userData = User::find($id);
       
        $folderAccess = users_folder_access::select('folder_primary_id')->where("user_id","=",$id)->get()->toArray();
        $folderAccess = array_column($folderAccess, 'folder_primary_id');
        //print_r($folderAccess);exit;
        $folderDataArr = $this->getFolders();
        $groupArr = $this->getGroups();
        // $roleData=$this->getRoles();
        $loooker_data= \DB::table('looker_data')
        ->select('looker_data.client_primary_id','looker_data.client_id','looker_data.client_name')
        ->join('client_folder_mapping','looker_data.client_primary_id','=','client_folder_mapping.id')
        ->where('client_folder_mapping.is_active',1)
        ->where('client_folder_mapping.type',"Client")
        ->where('client_folder_mapping.entity_id',env('env_entity_id'))
        ->whereNotNull('client_folder_mapping.folder_id')
        ->orderBy('looker_data.client_name')
        ->distinct()
        ->get();     
        $ClientDash = [];
        $ClientSubDash = [];
        foreach($loooker_data as $key => $value)
        {
            $ClientDash[$key]['id']         = $value->client_primary_id;
            $ClientDash[$key]['folder_id']  = $value->client_id;
            $ClientDash[$key]['name']       = $value->client_name;


            $folder_data= \DB::table('looker_data')
            ->select('looker_data.folder_id','looker_data.folder_name')
            ->where('looker_data.client_primary_id',$value->client_primary_id)
            ->where('looker_data.client_id',$value->client_id)
            ->distinct()
            ->get(); 

            $folderChildArr=[];
            foreach($folder_data as $keys => $values)
            {
                if($values->folder_id != "" && isset($values->folder_id))
                {
                    $folderChild['id']      = $values->folder_id;
                    $folderChild['name']    = $values->folder_name;
                    $folderChildArr[]       = $folderChild;

                    $dashboard_data= \DB::table('looker_data')
                    ->select('looker_data.dash_id','looker_data.title')
                    ->where('looker_data.folder_id',$values->folder_id)
                    ->distinct()
                    ->get(); 
                    foreach($dashboard_data as $keyss => $valuess)
                    {
                        $SubFolderDashboards[$values->folder_id][$keyss]['id'] = $valuess->dash_id;
                        $SubFolderDashboards[$values->folder_id][$keyss]['title'] = $valuess->title;
                    }
                }
            }
            $ClientDash[$key]['dashboard']      = $folderChildArr;
            $ClientSubDash[$key]['dashboard']   = $SubFolderDashboards;
        }
        
       
        $GroupData = DB::table('groups')
        ->select('group_id','group_name')
                    ->where('is_active', 1)
                    ->where('groups.entity_id',env('env_entity_id'))
                    ->get();
        $activeflag = $userData->is_active;
        $RolesData = [];
        $SelectedClientData = [];
        $grp_dtl =[];
        $roleData=[];
        if($userData->role !=""){
        $RolesData = \DB::table('grp_role_usr_mapping')
            ->select('grp_role_usr_mapping.grp_usr_mapping_id','grp_role_usr_mapping.users','grp_role_usr_mapping.looker','grp_role_usr_mapping.snowflake','grp_role_usr_mapping.group_module','grp_role_usr_mapping.processing','grp_role_usr_mapping.roles','grp_role_usr_mapping.clients','grp_role_usr_mapping.dashboards','grp_role_usr_mapping.reports','grp_role_usr_mapping.user_add','grp_role_usr_mapping.user_edit','grp_role_usr_mapping.user_delete','grp_role_usr_mapping.user_view','grp_role_usr_mapping.client_add','grp_role_usr_mapping.client_edit','grp_role_usr_mapping.client_delete','grp_role_usr_mapping.client_view','grp_role_usr_mapping.user_id','users.name','users.last_name','groups.group_id','groups.group_name','roles.role_id','roles.role',
        'grp_role_usr_mapping.role_add',
'grp_role_usr_mapping.role_edit',
'grp_role_usr_mapping.role_delete',
'grp_role_usr_mapping.role_view',
'grp_role_usr_mapping.group_add',
'grp_role_usr_mapping.group_edit',
'grp_role_usr_mapping.group_delete',
'grp_role_usr_mapping.group_view',
'grp_role_usr_mapping.report_add',
'grp_role_usr_mapping.report_edit',
'grp_role_usr_mapping.report_delete',
'grp_role_usr_mapping.report_view',
'grp_role_usr_mapping.generate_report',
'grp_role_usr_mapping.generate_report_add',
'grp_role_usr_mapping.generate_report_edit',
'grp_role_usr_mapping.generate_report_delete',
'grp_role_usr_mapping.generate_report_view',
'grp_role_usr_mapping.invite_user'
)
            ->leftjoin('users','grp_role_usr_mapping.user_id','=','users.id')
            ->leftjoin('groups','grp_role_usr_mapping.group_id','=','groups.group_id')
            ->leftjoin('roles','grp_role_usr_mapping.role_id','=','roles.role_id')
            ->where(['grp_role_usr_mapping.user_id' => $id])
            ->get();
            // echo "<pre>";
            // print_r($RolesData);
            // exit();
        
         
            if(!empty($RolesData[0]))
            {
                $roleData= \DB::table('group_role_dashboards_mapping as a')
        ->select('a.group_id','a.role_id','b.role')
        ->select('a.group_id','a.role_id','b.role')
        ->join('roles as b','a.role_id','=','b.role_id')
        ->join('groups as c','a.group_id','=','c.group_id')
        ->where('a.is_active',1)
        ->where('a.group_id',$RolesData[0]->group_id)
        ->where('c.entity_id',env('env_entity_id'))
        ->groupBy('a.group_id','a.role_id','b.role')
        ->get(); 
                // print_r($RolesData);
                // exit();
                $userDashAccess = \DB::table('users_dasboards_mapping')
                ->select('*')
                 ->where('users_dasboards_mapping.grp_usr_mapping_id',$RolesData[0]->grp_usr_mapping_id)
                ->get(); 
                
                $SelectedClientData=[];
                foreach($userDashAccess as $value)
                {                    
                    $SelectedClientData[$value->client_primary_id][$value->dashboard_id][]=$value->sub_dashboard_id;
                }     
                       
            }
        }
        else
        {
            $grp_dtl[0]['group_id'] =$userData->user_group_id;
            $activeflag = $userData->is_active;
        }

        return view('users.edit',compact('userData', 'id', 'activeMenu','activeSubMenu', 'folderDataArr', 'groupArr', 'folderAccess','roleData','ClientDash','ClientSubDash','GroupData','SelectedClientData','RolesData','grp_dtl','activeflag'));
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
	
        $group_id = "";
        if($request->groupFlag == 1)
        {
            $group_id = Groups::create([
                'group_name'        => $request->group,
                'entity_id'         => env('env_entity_id'),
                'created_by'        => auth()->user()->id,             
            ])->group_id;            
        }
        else
        {
            $group_id = $request->groupDD;
        }
        
        $RoleDtl =$this->getRoleDtl($request->role);

        $user_unique_id = $group_id.'-'.$RoleDtl[0]->role_unique_id.'-'.$id;
        $user = User::find($id);
		
		$user->unique_id = $user_unique_id;
        $user->name = $request->first_name;
		$user->last_name = $request->last_name;
		$user->email = $request->email;
		$user->permissions = $request->permissions;
		$user->user_attributes = $request->user_attributes;
        $user->user_group_id = $group_id;
        if(isset($request->role))
        {
        $user->role = $request->role;            
        }
		if($request->password !=''){
			$user->password = Hash::make($request->password);
        }
        if(isset($request->is_active))
        {
        $user->is_active = $request->is_active;            
        }
        if(isset($request->is_active) && $request->is_active == 1)
        {
        $user->is_signup = 0;            
        }
        
        $user->updated_by = auth()->user()->id;
        //$user->folders =  $folders;
               
        $user->save();
        $grumDtl =$this->getGrumDtl($id);
       
        $grp_usr_mapping_id = "";
        if(isset($grumDtl[0]) && !empty($grumDtl[0]))
        {
             
            $grp_usr_mapping_id = $grumDtl[0]->grp_usr_mapping_id;
        $roles = Grp_role_usr_mapping::find($grumDtl[0]->grp_usr_mapping_id);
        
        $roles->group_id          = $group_id;
        $roles->role_id           = $request->role;
        $roles->users             = ($request->users == "on")?1:0;
        $roles->looker            = ($request->looker == "on")?1:0;
        $roles->snowflake         = ($request->snowflake == "on")?1:0;
        $roles->roles             = ($request->roles == "on")?1:0;
        $roles->clients           = ($request->clients == "on")?1:0;
        // $roles->dashboards        = ($request->dashboards == "on")?1:0;      
        $roles->reports           = ($request->reports == "on")?1:0;      
        $roles->user_add          = ($request->user_add == "on")?1:0;      
        $roles->user_edit         = ($request->user_edit == "on")?1:0;      
        $roles->user_delete       = ($request->user_delete == "on")?1:0;      
        $roles->user_view         = ($request->user_view == "on")?1:0;      
        $roles->client_add        = ($request->client_add == "on")?1:0;      
        $roles->client_edit       = ($request->client_edit == "on")?1:0;      
        $roles->client_delete     = ($request->client_delete == "on")?1:0;      
        $roles->client_view       = ($request->client_view == "on")?1:0;      
        $roles->role_add          = ($request->role_add == "on")?1:0;     
        $roles->role_edit         = ($request->role_edit == "on")?1:0;     
        $roles->role_delete       = ($request->role_delete == "on")?1:0;     
        $roles->role_view         = ($request->role_view == "on")?1:0;     
        $roles->group_module      = ($request->group_module == "on")?1:0;     
        $roles->group_add         = ($request->group_add == "on")?1:0;     
        $roles->group_edit        = ($request->group_edit == "on")?1:0;     
        $roles->group_delete      = ($request->group_delete == "on")?1:0;     
        $roles->group_view        = ($request->group_view == "on")?1:0;     
        $roles->report_add        = ($request->report_add == "on")?1:0;     
        $roles->report_edit       = ($request->report_edit == "on")?1:0;     
        $roles->report_delete     = ($request->report_delete == "on")?1:0;     
        $roles->report_view       = ($request->report_view == "on")?1:0;     
        $roles->generate_report   = ($request->gene_report == "on")?1:0; 
        $roles->generate_report_add        = ($request->gene_report_add == "on")?1:0;
        $roles->generate_report_edit       = ($request->gene_report_edit == "on")?1:0;
        $roles->generate_report_delete     = ($request->gene_report_delete == "on")?1:0;
        $roles->generate_report_view       = ($request->gene_report_view == "on")?1:0;
        $roles->invite_user       = ($request->invite_user == "on")?1:0;
        $roles->updated_by        = auth()->user()->id;     
        $roles->save();
        Users_dasboards_mapping::where('grp_usr_mapping_id', $grumDtl[0]->grp_usr_mapping_id)->delete();
        users_folder_access::where('user_id', $id)->delete();
        }
        else
        {
        /*============Store Group Role User Mapping==============*/ 
            $grp_usr_mapping_id = Grp_role_usr_mapping::create([
                'user_id'           => $id,
                'group_id'          => $group_id,
                'role_id'           => $request->role,
                'user_unique_id'    => $user_unique_id,
                'users'             => ($request->users == "on")?1:0,
                'looker'            => ($request->looker == "on")?1:0,
                'snowflake'         => ($request->snowflake == "on")?1:0,
                'roles'             => ($request->roles == "on")?1:0,
                'group_module'      => ($request->group_module == "on")?1:0,
                'clients'           => ($request->clients == "on")?1:0,
                'dashboards'        => ($request->dashboards == "on")?1:0,
                'reports'           => ($request->reports == "on")?1:0,
                'user_add'          => ($request->Usersadd == "on")?1:0,
                'user_edit'         => ($request->Usersedit == "on")?1:0,
                'user_delete'       => ($request->Usersdelete == "on")?1:0,
                'user_view'         => ($request->Usersview == "on")?1:0,
                'client_add'        => ($request->Clientadd == "on")?1:0,
                'client_edit'       => ($request->Clientedit == "on")?1:0,
                'client_delete'     => ($request->Clientdelete == "on")?1:0,
                'client_view'       => ($request->ClientView == "on")?1:0,
                'role_add'          => ($request->role_add == "on")?1:0,
                'role_edit'         => ($request->role_edit == "on")?1:0,
                'role_delete'       => ($request->role_delete == "on")?1:0,
                'role_view'         => ($request->role_view == "on")?1:0,
                'group_add'         => ($request->group_add == "on")?1:0,
                'group_edit'        => ($request->group_edit == "on")?1:0,
                'group_delete'      => ($request->group_delete == "on")?1:0,
                'group_view'        => ($request->group_view == "on")?1:0,
                'report_add'        => ($request->report_add == "on")?1:0,
                'report_edit'       => ($request->report_edit == "on")?1:0,
                'report_delete'     => ($request->report_delete == "on")?1:0,
                'report_view'       => ($request->report_view == "on")?1:0,
                'created_by'        => auth()->user()->id,             
            ])->grp_usr_mapping_id;
        }
        /*============Store User Dashboard Mapping==============*/ 
        $MainData = [];
        $MainData1 = [];
            foreach($request->sub_dashboards_id as $key => $value)
            {
                $arr = explode("_",$value);   
                if(isset($arr[2]) && $arr[2] != "")
                {
                    $subarr['grp_usr_mapping_id']   =$grp_usr_mapping_id;
                    $subarr['client_primary_id']    =(isset($arr[0]))?$arr[0]:null;
                    $subarr['client_id']            =(isset($arr[1]))?$arr[1]:null;
                    $subarr['dashboard_id']         =(isset($arr[2]))?$arr[2]:null;
                    $subarr['sub_dashboard_id']     =(isset($arr[3]))?$arr[3]:null;
                    $subarr['created_by']           =auth()->user()->id;
                    array_push($MainData, $subarr);
                }
                else
                {
                    $subarr1['grp_usr_mapping_id']  =$grp_usr_mapping_id;
                    $subarr1['client_primary_id']   =(isset($arr[0]))?$arr[0]:null;
                    $subarr1['client_id']           =(isset($arr[1]))?$arr[1]:null;
                    $subarr1['created_by']          =auth()->user()->id;
                    array_push($MainData1, $subarr1);
                }                
               
            }
        
            if(!empty($MainData)){Users_dasboards_mapping::insert($MainData);}
            if(!empty($MainData1)){Users_dasboards_mapping::insert($MainData1);}
            foreach($request->clientids as $keyy => $valuee)
            {
                $arr1 = explode("_",$valuee);
                $usr_fdr_id = users_folder_access::create([
                'user_id'               => $id,
                'folder_id'             => (isset($arr1[0]))?$arr1[1]:null,
                'folder_primary_id'     => (isset($arr1[1]))?$arr1[0]:null,
                'created_by'            => auth()->user()->id,             
                ])->usr_fdr_id;
            }
        if($request->is_signup == 1 && $request->is_active == 1)
        {
            $this->Welcome_mail($request->email,$request->first_name);
        }

        return redirect('users')->with('success', 'User has been successfully updated!!');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $user = User::find($id);
        // $user->delete();
        DB::table('users')
            ->where('id', $id)
            ->update(['is_active' => '0']);
        DB::table('users_folder_access')->where('user_id', $id)->delete();
        return redirect('users')->with('success','User has been deleted successfully!!');
    }
    public function terminate($id)
    {
        DB::table('sessions')->where('id', $id)->delete();
        return redirect('users')->with('success','Session Terminatted successfully!!');
    }
    public function getRoles()
    {
        return $roledata= \DB::table('roles')
        ->select('role_id','role')
        ->where('is_active',1)
        ->where('role','!=','Super Admin')
        ->get();
    }
    public function profile($encrypt_id)
    {   
        $id = $this->helper->encrypt_decrypt($encrypt_id,'decrypt');       
        $activeMenu = '2';
        $activeSubMenu = '0';
        $userData =\DB::table('users')
        ->select('users.*','roles.role as role_name','groups.group_name')
        ->join('roles','users.role','=','roles.role_id')
        ->join('groups','users.user_group_id','=','groups.group_id')
        ->where('users.is_active',1)
        ->where('users.id',$id)
        ->get();

        if(isset($id)){
            $role = auth()->user()->role;
                if($role == 1)
                {
                    $headerData = \DB::table('client_folder_mapping')
                    ->select('client_folder_mapping.id','client_folder_mapping.folder_id','client_folder_mapping.folder_name','client_folder_mapping.logo')
                    ->distinct()
                    ->where(['client_folder_mapping.entity_id' => env('env_entity_id'),'client_folder_mapping.type' => 'Client','client_folder_mapping.is_active' => 1])
                    ->get();
                }
                else
                {
                    $headerData = \DB::table('client_folder_mapping')
                    ->select('client_folder_mapping.id','client_folder_mapping.folder_id','client_folder_mapping.folder_name','client_folder_mapping.logo')
                    ->join('users_folder_access','client_folder_mapping.id','=','users_folder_access.folder_primary_id')
                    ->distinct()
                    ->where(['users_folder_access.user_id' => $id,'client_folder_mapping.entity_id' => env('env_entity_id'),'client_folder_mapping.is_active' => 1])
                    ->get();
                }            
            }
        // Get Profile picture from s3
            $logopath = "";
            if(!empty($userData[0]->profile_pic) && $userData[0]->profile_pic != Null)
            {
            $s3 = \Storage::disk('s3');
            $client = $s3->getDriver()->getAdapter()->getClient();
            $expiry = "+1 minutes";

            $command = $client->getCommand('GetObject', [
              'Bucket' => 'kairos-app-storage', // bucket name
              'Key'    => $userData[0]->profile_pic
            ]);

            $request = $client->createPresignedRequest($command, $expiry);
            $logopath =  (string) $request->getUri(); // it will return signed URL
            }

        return view('users.profile',compact('userData','activeMenu', 'activeSubMenu','headerData','logopath'));
    }
    public function profile_edit($encrypt_id)
    {   
        $id = $this->helper->encrypt_decrypt($encrypt_id,'decrypt');       
        $activeMenu = '2';
        $activeSubMenu = '0';
        $userData =\DB::table('users')
        ->select('users.*','roles.role as role_name')
        ->join('roles','users.role','=','roles.role_id')
        ->where('users.is_active',1)
        ->where('users.id',$id)
        ->get();

        if(isset($id)){
            $role = auth()->user()->role;
                if($role == 1)
                {
                    $headerData = \DB::table('client_folder_mapping')
                    ->select('client_folder_mapping.id','client_folder_mapping.folder_id','client_folder_mapping.folder_name','client_folder_mapping.logo')
                    ->distinct()
                    ->where(['client_folder_mapping.entity_id' => env('env_entity_id'),'client_folder_mapping.type' => 'Client','client_folder_mapping.is_active' => 1])
                    ->get();
                }
                else
                {
                    $headerData = \DB::table('client_folder_mapping')
                    ->select('client_folder_mapping.id','client_folder_mapping.folder_id','client_folder_mapping.folder_name','client_folder_mapping.logo')
                    ->join('users_folder_access','client_folder_mapping.id','=','users_folder_access.folder_primary_id')
                    ->distinct()
                    ->where(['users_folder_access.user_id' => $id,'client_folder_mapping.entity_id' => env('env_entity_id'),'client_folder_mapping.is_active' => 1])
                    ->get();
                }            
            }

        
            // Get Profile picture from s3
            $logopath = "";
            if(!empty($userData[0]->profile_pic) && $userData[0]->profile_pic != Null)
            {
            $s3 = \Storage::disk('s3');
            $client = $s3->getDriver()->getAdapter()->getClient();
            $expiry = "+1 minutes";

            $command = $client->getCommand('GetObject', [
              'Bucket' => 'kairos-app-storage', // bucket name
              'Key'    => $userData[0]->profile_pic
            ]);

            $request = $client->createPresignedRequest($command, $expiry);
            $logopath =  (string) $request->getUri(); // it will return signed URL
            }

        return view('users.profile_edit',compact('userData','id','activeMenu', 'activeSubMenu','headerData','logopath'));
    }
    public function updateProfile(Request $request, $id)
    {

        $activeMenu = '2';
        $activeSubMenu = '0';
        if ($request->hasFile('file')) {
        $this->validate($request, [
            'uploadFile' => 'mimes:jpeg,png,jpg,gif,svg|max:1024'
        ]);

        $file = $request->file('file');
        $name = time() .'_'. $file->getClientOriginalName();
        $filePath = 'PROFILE-PHOTOS/' . $name;
        Storage::disk('s3')->put($filePath, file_get_contents($file)); 
        }
        $user = User::find($id);
        
        $user->name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->contact = $request->contact;
        $user->profile_pic = $filePath;

        if($request->password !=''){
            $user->password = Hash::make($request->password);
        }
        $user->updated_by = auth()->user()->id;               
        $user->save();
        $encrypt_id = $this->helper->encrypt_decrypt($id,'encrypt');
        return redirect('/users/profile/'.$encrypt_id)->with('success', 'Profile has been successfully updated!!');
    }
    public function getRoleDtl($id)
    {
        $RolesData = DB::table('roles')
        ->select('role_unique_id')
                    ->where('role_id', $id)
                    ->get();
        return $RolesData;

    }
    public function getGrumDtl($id)
    {
        $GRUMData = DB::table('grp_role_usr_mapping')
        ->select('grp_usr_mapping_id')
                    ->where('user_id', $id)
                    ->get();
        return $GRUMData;

    }
    public function Welcome_mail($useremail,$name)
    {
    $text             ='Hello '.$name."<br/><br/>";
    $text             = $text.'Your account on the Kairos system is available. Details to access the account are included below.'."<br/><br/>";
    $text             = $text.'System Access:'."<br/><br/>";
    $text             = $text.'The website to access the analytics system is: https://hca.kairosrp.com'."<br/><br/>";
    $text             = $text.'To sign in, use your email address (all lowercase) provided at the time of signup and password.'."<br/><br/>";
    $text             = $text.'Edit User Profile:'."<br/><br/>";
    $text             = $text.'You can edit your user profile by clicking the gear symbol in the top-right corner of the screen and then clicking your name. This will open an interface where you can update your name, email address, password, and change your profile picture.'."<br/><br/>";
    $text             = $text.'Please note: If you update your email address on your user profile, you will need to use the new email address as your user ID when logging into the system.'."<br/><br/>";
    $text             = $text.'To update your password, click the "Reset Password" link that appears on the user profile interface. This will take you to a screen where you can enter your email address and request a one-time password. When you receive the one-time password, you can then enter it and set a new password.'."<br/><br/>";
    $text             = $text.'Please let me know if you have any questions.'."<br/><br/>";
    $text             = $text.'Thank you'."<br/><br/>";
    $text             = $text. 'Thanks & Regards,'."<br/>";
    $text             = $text. 'Kairos Admin Team';
    $mail             = new PHPMailer\PHPMailer(); // create a n
    $mail->IsSMTP();
    
    //$mail->SMTPDebug  = 2; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth   = true; // authentication enabled
    $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host       = "email-smtp.us-east-1.amazonaws.com";
    $mail->Port       = 587; ; // or 587
    $mail->IsHTML(true);
    $mail->Username = "AKIAR2DNWFHADOVB3X75";
    $mail->Password = "BE3hsje11JymCh+nRogY4SxHoVGIEoloN4fK3xb0YQak";
    $mail->SetFrom("hca@kairosrp.com", 'Kairos App');
    $mail->Subject = "Welcome Mail";
    $mail->Body    = $text;
    $mail->AddAddress($useremail);
    
    $mail->Send();

    }
}
