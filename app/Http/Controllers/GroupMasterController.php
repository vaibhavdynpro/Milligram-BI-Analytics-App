<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Looker;
use App\Groups;
use App\User;
use App\GroupMaster;
use App\Grp_role_usr_mapping;
use App\Users_dasboards_mapping;
use App\users_folder_access;
use PHPMailer\PHPMailer;
use App\Libraries\Helpers;

class GroupMasterController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles');
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
        // $random =  $this->helper->generateRandomString(4);        
        $activeMenu = '2';
        $activeSubMenu = '0';
		// $roleData=Roles::all();
        $groupData = \DB::table('group_role_dashboards_mapping as a')
        ->select('a.group_id','a.role_id','b.group_name','c.role')
        ->join('groups as b','a.group_id','=','b.group_id')
        ->join('roles as c','a.role_id','=','c.role_id')
        ->where('b.is_active',1)
        ->groupBy('a.group_id','a.role_id','b.group_name','c.role')
        ->get();  
        return view('group_master.index',compact('groupData','activeMenu', 'activeSubMenu'));
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
    
        $roleData = \DB::table('roles')
        ->select('*')
        ->where('is_active',1)
        ->get();


        $GroupData = DB::table('groups')
        ->select('group_id','group_name')
                    ->where('is_active', 1)
                    ->where('entity_id', env('env_entity_id'))
                    ->get();
        
        
        return view('group_master.create',compact('activeMenu','activeSubMenu','ClientDash','roleData','ClientSubDash','GroupData'));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->groupFlag == 0)
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
                   


        /*============Store group Dashboard Mapping==============*/ 
        foreach($request->sub_dashboards_id as $key => $value)
        {
            $arr = explode("_",$value);            
            $usr_dash_id = GroupMaster::create([
            'group_id'                      => $group_id,
            'role_id'                       => $request->role,
            'client_primary_id'            => (isset($arr[0]))?$arr[0]:null,
            'client_id'                    => (isset($arr[1]))?$arr[1]:null,
            'dashboard_id'                 => (isset($arr[2]))?$arr[2]:null,
            'sub_dashboard_id'             => (isset($arr[3]))?$arr[3]:null,
            'created_by'                   => auth()->user()->id,             
            ])->usr_dash_id;
        }            
    
      return redirect('group_master')->with('success', 'Group Master has been successfully created!!');
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
    
	public function edit($group_encrypt_id,$role_encrypt_id)
    {
        $activeMenu = '2';
        $activeSubMenu = '0';
        $group_id = $this->helper->encrypt_decrypt($group_encrypt_id,'decrypt');
        $role_id = $this->helper->encrypt_decrypt($role_encrypt_id,'decrypt');
   
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
    
        $roleData = \DB::table('roles')
        ->select('*')
        ->where('is_active',1)
        ->get();
  
        $GroupData = DB::table('groups')
        ->select('group_id','group_name')
                    ->where('is_active', 1)
                    ->where('entity_id', env('env_entity_id'))
                    ->get();
        $GroudDtl = DB::table('groups')
        ->select('group_id','group_name')
                    ->where('is_active', 1)
                    ->where('group_id', $group_id)
                    ->where('entity_id', env('env_entity_id'))
                    ->get();
        $dashobardMapping = \DB::table('group_role_dashboards_mapping')
        ->select('*')
         ->where('group_role_dashboards_mapping.group_id',$group_id)
         ->where('group_role_dashboards_mapping.role_id',$role_id)
        ->get();

        $SelectedClientData=[];
        foreach($dashobardMapping as $value)
        {
            $SelectedClientData[$value->client_primary_id][$value->dashboard_id][]=$value->sub_dashboard_id;
        }
        
        return view('group_master.edit',compact('group_id','role_id', 'activeMenu','activeSubMenu','ClientDash','ClientSubDash','GroupData','roleData','SelectedClientData','GroudDtl'));
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
     
        if($request->groupFlag == 1){
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
        
        $role_id = $request->role;
      
        GroupMaster::where('group_id', $request->group_id)->where('role_id', $role_id)->delete();
        $MainData = [];
        $MainData1 = [];
        /*============Store User Dashboard Mapping==============*/ 
            foreach($request->sub_dashboards_id as $key => $value)
            {
                $arr = explode("_",$value);
                if(isset($arr[2]) && $arr[2] != "")
                {
                    $subarr['group_id']         =$group_id;
                    $subarr['role_id']          =$role_id;
                    $subarr['client_primary_id']=(isset($arr[0]))?$arr[0]:null;
                    $subarr['client_id']        =(isset($arr[1]))?$arr[1]:null;
                    $subarr['dashboard_id']     =(isset($arr[2]))?$arr[2]:null;
                    $subarr['sub_dashboard_id'] =(isset($arr[3]))?$arr[3]:null;
                    $subarr['created_by']       =auth()->user()->id;
                    array_push($MainData, $subarr);
                }
                else
                {
                    $subarr1['group_id']         =$group_id;
                    $subarr1['role_id']          =$role_id;
                    $subarr1['client_primary_id']=(isset($arr[0]))?$arr[0]:null;
                    $subarr1['client_id']        =(isset($arr[1]))?$arr[1]:null;
                    $subarr1['created_by']       =auth()->user()->id;
                    array_push($MainData1, $subarr1);
                }
                
            }

        if(!empty($MainData)){GroupMaster::insert($MainData);}
        if(!empty($MainData1)){GroupMaster::insert($MainData1);}
        return redirect('group_master')->with('success', 'Group Master has been successfully updated!!');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($group_id,$role_id)
    {
        DB::table('group_role_dashboards_mapping')
            ->where('group_id', $group_id)
            ->where('role_id', $role_id)
            ->update(['is_active' => '0']);
         DB::table('groups')
            ->where('group_id', $group_id)
            ->update(['is_active' => '0']);
        return redirect('group_master')->with('success','Group Master has been deleted successfully!!');
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
    public function getRoleDetails(Request $request)
    {
        $role_id = $request->role_id;
        $group_id = $request->group_id;
        $RolesData = DB::table('roles')
                    ->where('role_id', $role_id)
                    ->get();
        $userRoleData= \DB::table('users')
        ->select('users.id','users.name','users.last_name','roles.role','roles.role_id')
        ->leftjoin('roles','users.role','=','roles.role_id')
         ->where('users.entity_id',env('env_entity_id'))
         ->where('users.role',$role_id)
         ->where('users.user_group_id',$group_id)
        ->get();
        return response()->json(['success'=>'Data is successfully fetched','roleData'=>$RolesData,'userRoleData'=>$userRoleData]);
    }
    public function getGroup(Request $request)
    {
        
        $GroupData = DB::table('groups')
        ->select('group_name')
                    ->where('is_active', 1)
                    ->where('entity_id', env('env_entity_id'))
                    ->get();
        
        return response()->json(['success'=>'Data is successfully fetched','GroupData'=>$GroupData]);
    }
    public function getRoleDtl($id)
    {
        $RolesData = DB::table('roles')
        ->select('role_unique_id')
                    ->where('role_id', $id)
                    ->get();
        return $RolesData;

    }
    public function checkGroupName(Request $request)
    {
    $groupname = $request->groupname;
    $flag = $request->flag;
    if($flag == 1)
    {
         $GroupData = DB::table('groups')
            ->select('group_name')
            ->where('is_active', 1)
            ->where('entity_id', env('env_entity_id'))
            ->where('group_name', $groupname)
            ->get();
    }
    else
    {
        $GroupData = DB::table('groups')
            ->select('group_name')
            ->where('is_active', 1)
            ->where('entity_id', env('env_entity_id'))
            ->where('group_name', $groupname)
            ->where('group_id', '!=',  $request->groupid )
            ->get();
    }
   
    $resp = 0;
    if(count($GroupData)>0)
    {
        $resp = 1;
    }
    return response()->json(['success'=>'Data is successfully added111','resp'=>$resp]);
    }


}
