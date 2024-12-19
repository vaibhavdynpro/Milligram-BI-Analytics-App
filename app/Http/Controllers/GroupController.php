<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Looker;
use App\Groups;
use App\User;
use App\Grp_role_usr_mapping;
use App\Users_dasboards_mapping;
use App\users_folder_access;
use PHPMailer\PHPMailer;
use App\Libraries\Helpers;

class GroupController extends Controller
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
        $groupData = \DB::table('groups as a')
        ->select('a.group_id','a.group_name','b.grp_usr_mapping_id','b.user_id','b.role_id','c.role','d.name','d.last_name')
        ->join('grp_role_usr_mapping as b','a.group_id','=','b.group_id')
        ->join('roles as c','b.role_id','=','c.role_id')
        ->join('users as d','b.user_id','=','d.id')
        ->where('d.entity_id',env('env_entity_id'))
        ->where('d.is_active',1)
        ->get();  
        // $groupData = \DB::table('grp_role_usr_mapping as a')
        // ->select('a.group_id','a.role_id','b.group_name','c.role')
        // ->join('groups as b','a.group_id','=','b.group_id')
        // ->join('roles as c','a.role_id','=','c.role_id')
        // ->distinct()
        // ->get(); 
        return view('group.index',compact('groupData','activeMenu', 'activeSubMenu'));
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

        $userRoleData= \DB::table('users')
        ->select('users.id','users.name','users.last_name','roles.role','roles.role_id','groups.group_name','groups.group_id')
        ->leftjoin('roles','users.role','=','roles.role_id')
        ->leftjoin('groups','users.user_group_id','=','groups.group_id')
         ->where('users.entity_id',env('env_entity_id'))
         ->where('users.is_active',1)
        ->get();
        $GroupData = DB::table('groups')
        ->select('group_id','group_name')
                    ->where('is_active', 1)
                    ->where('entity_id', env('env_entity_id'))
                    ->get();
        
        
        return view('group.create',compact('activeMenu','activeSubMenu','ClientDash','roleData','ClientSubDash','userRoleData','GroupData'));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // echo "<pre>";
        
       
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
        foreach($request->Userslist as $val){

            /*============Remove existing records==============*/ 
            $query = 'DELETE grp_role_usr_mapping,users_dasboards_mapping FROM grp_role_usr_mapping 
            INNER JOIN users_dasboards_mapping ON grp_role_usr_mapping.grp_usr_mapping_id = users_dasboards_mapping.grp_usr_mapping_id  
            WHERE grp_role_usr_mapping.user_id = ?';
            \DB::delete($query, array($val));
            users_folder_access::where('user_id', $val)->delete();


            /*============Generate Unique Id==============*/ 

            $user_unique_id = $group_id.'-'.$RoleDtl[0]->role_unique_id.'-'.$val;

            /*============Store Group Role User Mapping==============*/ 
            $grp_usr_mapping_id = Grp_role_usr_mapping::create([
                'user_id'           => $val,
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
                'role_add'          => ($request->role_add == "on")?1:0,
                'role_edit'         => ($request->role_edit == "on")?1:0,
                'role_delete'       => ($request->role_delete == "on")?1:0,
                'role_view'         => ($request->role_view == "on")?1:0,
                'group_add'         => ($request->group_add == "on")?1:0,
                'group_edit'        => ($request->group_edit == "on")?1:0,
                'group_delete'      => ($request->group_delete == "on")?1:0,
                'group_view'        => ($request->group_view == "on")?1:0,
                'client_add'        => ($request->Clientadd == "on")?1:0,
                'client_edit'       => ($request->Clientedit == "on")?1:0,
                'client_delete'     => ($request->Clientdelete == "on")?1:0,
                'client_view'       => ($request->ClientView == "on")?1:0,
                'report_add'        => ($request->report_add == "on")?1:0,
                'report_edit'       => ($request->report_edit == "on")?1:0,
                'report_delete'     => ($request->report_delete == "on")?1:0,
                'report_view'       => ($request->report_view == "on")?1:0,
                'generate_report'       => ($request->gene_report == "on")?1:0,
                'generate_report_add'   => ($request->gene_report_add == "on")?1:0,
                'generate_report_edit'  => ($request->gene_report_edit == "on")?1:0,
                'generate_report_delete'=> ($request->gene_report_delete == "on")?1:0,
                'generate_report_view'  => ($request->gene_report_view == "on")?1:0,
                'invite_user'       => ($request->invite_user == "on")?1:0,
                'created_by'        => auth()->user()->id,             
            ])->grp_usr_mapping_id;


            /*============Store User Dashboard Mapping==============*/ 
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
                'user_id'               => $val,
                'folder_id'             => (isset($arr1[0]))?$arr1[1]:null,
                'folder_primary_id'     => (isset($arr1[1]))?$arr1[0]:null,
                'created_by'            => auth()->user()->id,             
                ])->usr_fdr_id;
            }
            $user                   = User::find($val);        
            $user->unique_id        = $user_unique_id;
            $user->user_group_id    = $group_id;
            $user->role             = $request->role;
            $user->save();
            
        }
      return redirect('groups')->with('success', 'Group Role Users Mapping has been successfully created!!');
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
        $activeMenu = '2';
        $activeSubMenu = '0';
        $id = $this->helper->encrypt_decrypt($encrypt_id,'decrypt');
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
    
        

        $userRoleData= \DB::table('users')
        ->select('users.id','users.name','users.last_name','roles.role','roles.role_id','groups.group_name','groups.group_id')
        ->leftjoin('roles','users.role','=','roles.role_id')
        ->leftjoin('groups','users.user_group_id','=','groups.group_id')
         ->where('users.entity_id',env('env_entity_id'))
        ->get();
        $GroupData = DB::table('groups')
        ->select('group_id','group_name')
                    ->where('is_active', 1)
                    ->where('entity_id', env('env_entity_id'))
                    ->get();
        $RolesData = \DB::table('grp_role_usr_mapping')
            ->select('grp_role_usr_mapping.grp_usr_mapping_id','grp_role_usr_mapping.users','grp_role_usr_mapping.looker','grp_role_usr_mapping.snowflake','grp_role_usr_mapping.group_module','grp_role_usr_mapping.processing','grp_role_usr_mapping.roles','grp_role_usr_mapping.clients','grp_role_usr_mapping.dashboards','grp_role_usr_mapping.reports','grp_role_usr_mapping.user_add','grp_role_usr_mapping.user_edit','grp_role_usr_mapping.user_delete','grp_role_usr_mapping.user_view','grp_role_usr_mapping.client_add','grp_role_usr_mapping.client_edit','grp_role_usr_mapping.client_delete','grp_role_usr_mapping.client_view','grp_role_usr_mapping.user_id','users.name','users.last_name','groups.group_id','groups.group_name','roles.role_id','roles.role','grp_role_usr_mapping.role_add',
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
            ->where(['grp_role_usr_mapping.grp_usr_mapping_id' => $id])
            ->get();
        $userDashAccess = \DB::table('users_dasboards_mapping')
        ->select('*')
         ->where('users_dasboards_mapping.grp_usr_mapping_id',$RolesData[0]->grp_usr_mapping_id)
        ->get();

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
        
        $SelectedClientData=[];
        foreach($userDashAccess as $value)
        {
            $SelectedClientData[$value->client_primary_id][$value->dashboard_id][]=$value->sub_dashboard_id;
        }
     
        // echo "<pre>";
        // print_r($dashAccess);
        // print_r($clientAcc);
        // print_r($clientAcc);
        // exit();
        return view('group.edit',compact('id', 'activeMenu','activeSubMenu','ClientDash','roleData','ClientSubDash','userRoleData','GroupData','RolesData','SelectedClientData'));
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
        $user_unique_id = $group_id.'-'.$RoleDtl[0]->role_unique_id.'-'.$request->user_id;


        $roles = Grp_role_usr_mapping::find($id);
		
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
        $roles->updated_by       = auth()->user()->id;     
        $roles->save();
        Users_dasboards_mapping::where('grp_usr_mapping_id', $id)->delete();
        users_folder_access::where('user_id', $request->user_id)->delete();
        $MainData = [];
        $MainData1 = [];
        /*============Store User Dashboard Mapping==============*/ 
            foreach($request->sub_dashboards_id as $key => $value)
            {
                $arr = explode("_",$value);     
                if(isset($arr[2]) && $arr[2] != "")
                {
                    $subarr['grp_usr_mapping_id']   =$id;
                    $subarr['client_primary_id']    =(isset($arr[0]))?$arr[0]:null;
                    $subarr['client_id']            =(isset($arr[1]))?$arr[1]:null;
                    $subarr['dashboard_id']         =(isset($arr[2]))?$arr[2]:null;
                    $subarr['sub_dashboard_id']     =(isset($arr[3]))?$arr[3]:null;
                    $subarr['created_by']           =auth()->user()->id;
                    array_push($MainData, $subarr);
                }
                else
                {
                    $subarr1['grp_usr_mapping_id']  =$id;
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
                'user_id'               => $request->user_id,
                'folder_id'             => (isset($arr1[0]))?$arr1[1]:null,
                'folder_primary_id'     => (isset($arr1[1]))?$arr1[0]:null,
                'created_by'            => auth()->user()->id,             
                ])->usr_fdr_id;
            }
            $user                   = User::find($request->user_id);        
            $user->unique_id        = $user_unique_id;
            $user->user_group_id    = $group_id;
            $user->role             = $request->role;
            $user->save();
        

        return redirect('groups')->with('success', 'User Aceess has been successfully updated!!');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $user = Roles::find($id);
        // $user->delete();
        $query = 'DELETE grp_role_usr_mapping,users_dasboards_mapping FROM grp_role_usr_mapping 
            INNER JOIN users_dasboards_mapping ON grp_role_usr_mapping.grp_usr_mapping_id = users_dasboards_mapping.grp_usr_mapping_id  
            WHERE grp_role_usr_mapping.user_id = ?';
            \DB::delete($query, array($id));
            users_folder_access::where('user_id', $id)->delete();
        return redirect('groups')->with('success','User Access has been deleted successfully!!');
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
        foreach($loooker_data as $key => $value)
        {
            $ClientDash[$key]['id']         = $value->client_primary_id;
            $ClientDash[$key]['folder_id']  = $value->client_id;
            $ClientDash[$key]['name']       = $value->client_name;
        }
        return response()->json(['success'=>'Data is successfully fetched','roleData'=>$RolesData,'userRoleData'=>$userRoleData,'dashobardMapping'=>$SelectedClientData,'ClientList' =>$ClientDash]);
    }
    public function getGroupRoleDetails(Request $request)
    {
        $group_id = $request->group_id;
        $rolesData= \DB::table('group_role_dashboards_mapping as a')
        ->select('a.group_id','a.role_id','b.role')
        ->select('a.group_id','a.role_id','b.role')
        ->join('roles as b','a.role_id','=','b.role_id')
        ->join('groups as c','a.group_id','=','c.group_id')
        ->where('a.is_active',1)
        ->where('a.group_id',$group_id)
        ->where('c.entity_id',env('env_entity_id'))
        ->groupBy('a.group_id','a.role_id','b.role')
        ->get();     
        return response()->json(['success'=>'Data is successfully fetched','rolesData'=>$rolesData]);
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


}
