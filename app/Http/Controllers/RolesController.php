<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Roles;
use App\User;
use App\Looker;
use App\users_folder_access;
use PHPMailer\PHPMailer;
use App\Libraries\Helpers;

class RolesController extends Controller
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
        $activeMenu = '2';
        $activeSubMenu = '0';
		// $roleData=Roles::all();
        $roleData = \DB::table('roles')
        ->select('*')
        ->where('is_active',1)
        ->get();
        return view('roles.index',compact('roleData','activeMenu', 'activeSubMenu'));
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
        $userRoleData= \DB::table('users')
        ->select('users.id','users.name','users.last_name','roles.role')
        ->leftjoin('roles','users.role','=','roles.role_id')
        ->where('users.entity_id',env('env_entity_id'))
        ->get();
        $clients= \DB::table('client_folder_mapping')
        ->select('client_folder_mapping.id','client_folder_mapping.folder_id','client_folder_mapping.folder_name')
        ->where('client_folder_mapping.is_active',1)
        ->where('client_folder_mapping.type',"Client")
        ->whereNotNull('client_folder_mapping.folder_id')
        ->get();

        
        return view('roles.create',compact('activeMenu','activeSubMenu','userRoleData'));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $unique_ids =  $this->helper->generateRandomString(4);      

		$id = Roles::create([
            'role_unique_id'    => $unique_ids,
            'role'              => $request->role,
            'users'             => ($request->users == "on")?1:0,
            'looker'            => ($request->looker == "on")?1:0,
            'matillion'         => ($request->matillion == "on")?1:0,
            'roles'             => ($request->roles == "on")?1:0,
            'clients'           => ($request->clients == "on")?1:0,
            'dashboards'        => ($request->dashboards == "on")?1:0,
            'group_module'      => ($request->group_module == "on")?1:0,
            'phm'               => ($request->phm == "on")?1:0,
            'generate_report'   => ($request->gene_report == "on")?1:0,
            'user_add'          => ($request->user_add == "on")?1:0,
            'user_edit'         => ($request->user_edit == "on")?1:0,
            'user_delete'       => ($request->user_delete == "on")?1:0,
            'user_view'         => ($request->user_view == "on")?1:0,            
            'role_add'          => ($request->role_add == "on")?1:0,
            'role_edit'         => ($request->role_edit == "on")?1:0,
            'role_delete'       => ($request->role_delete == "on")?1:0,
            'role_view'         => ($request->role_view == "on")?1:0,
            'client_add'        => ($request->client_add == "on")?1:0,
            'client_edit'       => ($request->client_edit == "on")?1:0,
            'client_delete'     => ($request->client_delete == "on")?1:0,
            'client_view'       => ($request->client_view == "on")?1:0,
            'group_add'         => ($request->group_add == "on")?1:0,
            'group_edit'        => ($request->group_edit == "on")?1:0,
            'group_delete'      => ($request->group_delete == "on")?1:0,
            'group_view'        => ($request->group_view == "on")?1:0,
            'report_add'        => ($request->report_add == "on")?1:0,
            'report_edit'       => ($request->report_edit == "on")?1:0,
            'report_delete'     => ($request->report_delete == "on")?1:0,
            'report_view'       => ($request->report_view == "on")?1:0,
            'generate_report_add'        => ($request->gene_report_add == "on")?1:0,
            'generate_report_edit'       => ($request->gene_report_edit == "on")?1:0,
            'generate_report_delete'     => ($request->gene_report_delete == "on")?1:0,
            'generate_report_view'       => ($request->gene_report_view == "on")?1:0,
            'invite_user'       => ($request->invite_user == "on")?1:0,
            'created_by'        => auth()->user()->id,             
        ])->role_id;

        
        // foreach ($request->Users as $key => $value) {
        //     $user                = User::find($value);        
        //     $user->role          = $id;
        //     $user->save();
        // }
        
		return redirect('roles')->with('success', 'Role has been successfully created!!');
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
        $RolesData = DB::table('roles')
                    ->where('role_id', $id)
                    ->get();
        $userRoleData= \DB::table('users')
        ->select('users.id','users.name','users.last_name','roles.role','roles.role_id')
        ->leftjoin('roles','users.role','=','roles.role_id')
         ->where('users.entity_id',env('env_entity_id'))
        ->get();
        return view('roles.edit',compact('RolesData', 'id', 'activeMenu','activeSubMenu','userRoleData'));
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
		
        $roles = Roles::find($id);
		
		$roles->role              = $request->role;
        $roles->users             = ($request->users == "on")?1:0;
        $roles->looker            = ($request->looker == "on")?1:0;
        $roles->matillion         = ($request->matillion == "on")?1:0;
        $roles->roles             = ($request->roles == "on")?1:0;
        $roles->clients           = ($request->clients == "on")?1:0;
        $roles->dashboards        = ($request->dashboards == "on")?1:0;	   
        $roles->group_module      = ($request->group_module == "on")?1:0;  
        $roles->phm               = ($request->phm == "on")?1:0;      
        $roles->generate_report   = ($request->gene_report == "on")?1:0;      
        $roles->user_add          = ($request->user_add == "on")?1:0;      
        $roles->user_edit         = ($request->user_edit == "on")?1:0;      
        $roles->user_delete       = ($request->user_delete == "on")?1:0;      
        $roles->user_view         = ($request->user_view == "on")?1:0;      
        $roles->role_add          = ($request->role_add == "on")?1:0;      
        $roles->role_edit         = ($request->role_edit == "on")?1:0;      
        $roles->role_delete       = ($request->role_delete == "on")?1:0;      
        $roles->role_view         = ($request->role_view == "on")?1:0;      
        $roles->client_add        = ($request->client_add == "on")?1:0;      
        $roles->client_edit       = ($request->client_edit == "on")?1:0;      
        $roles->client_delete     = ($request->client_delete == "on")?1:0;      
        $roles->client_view       = ($request->client_view == "on")?1:0; 
        $roles->group_add         = ($request->group_add == "on")?1:0;
        $roles->group_edit        = ($request->group_edit == "on")?1:0;
        $roles->group_delete      = ($request->group_delete == "on")?1:0;
        $roles->group_view        = ($request->group_view == "on")?1:0;
        $roles->report_add        = ($request->report_add == "on")?1:0;
        $roles->report_edit       = ($request->report_edit == "on")?1:0;
        $roles->report_delete     = ($request->report_delete == "on")?1:0;
        $roles->report_view       = ($request->report_view == "on")?1:0;
        $roles->generate_report_add        = ($request->gene_report_add == "on")?1:0;
        $roles->generate_report_edit       = ($request->gene_report_edit == "on")?1:0;
        $roles->generate_report_delete     = ($request->gene_report_delete == "on")?1:0;
        $roles->generate_report_view       = ($request->gene_report_view == "on")?1:0;    
        $roles->invite_user       = ($request->invite_user == "on")?1:0;    
        $roles->updated_by       = auth()->user()->id;     
        $roles->save();
        
        // foreach ($request->Users as $key => $value) {
        //     $user                = User::find($value);        
        //     $user->role          = $id;
        //     $user->save();
        // }
        

        return redirect('roles')->with('success', 'Roles has been successfully updated!!');
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
        DB::table('roles')
            ->where('role_id', $id)
            ->update(['is_active' => '0']);
        return redirect('roles')->with('success','Role has been deleted successfully!!');
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
}
