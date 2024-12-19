<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Helpers;
use App\User;
use App\users_folder_access;
use App\Client_folder_mapping;
use DB;
class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
        // auth()->setDefaultDriver('api');
        $this->helper = new Helpers;
    }
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $token = $request->bearerToken();
        $userAuthentication = $this->helper->AuthenticatedUserDtl($token);
        if($userAuthentication['status'] == 200)
        {
            $orgn_ids = [];
            if($userAuthentication['data']->sub == "create_user")
            {
                $dataset = json_decode(json_encode($userAuthentication['data']), true);
                $is_exits = User::where('unique_id', $dataset['unique_id'])->exists();
                if(!$is_exits)
                {
                    if(empty($dataset['first_name']))
                    {
                        return $this->helper->error(201,'First Name is required');
                    }
                    elseif (empty($dataset['last_name'])) {
                        return $this->helper->error(201,'Last Name is required');
                    }
                    else
                    {
                        $userDtl = DB::table('users')         
                        ->orderBy('id','DESC')
                        ->limit(1)
                        ->get()
                        ->toArray();
                        $extr_usr_id= $userDtl[0]->external_user_id + 1;

                        $user_dtl['name']       = $dataset['first_name'];
                        $user_dtl['last_name']  = $dataset['last_name'];
                        $user_dtl['unique_id']  = $dataset['unique_id'];
                        $user_dtl['usergroup']  = $dataset['usergroup'];
                        $user_dtl['email']      = $dataset['first_name'].'.'.$dataset['last_name'].'@909healthcare.com';
                        $user_dtl['iss']        = $dataset['iss'];
                        $user_dtl['external_user_id'] = $extr_usr_id;
                        if(isset($dataset['user_meta']['email']) && !empty($dataset['user_meta']['email']))
                        {
                        $is_exits1 = User::where('email', $dataset['user_meta']['email'])->exists();
                            if(!$is_exits1)
                            {
                                $user_dtl['email']        = $dataset['user_meta']['email'];
                            }
                            else
                            {
                                return $this->helper->error(201,'Email Already Exits');
                            }
                        }
                        
                            $result = User::create($user_dtl);
                            if (!empty($result)) {
                                if(!empty($dataset['organizations'])){
                                foreach ($dataset['organizations'] as $key => $value) {
                                    $client_dtl = $this->helper->check_client($value['organization_unique_id']);
                                    if(!empty($client_dtl))
                                    {
                                        $user_dtl = $this->helper->get_userid($dataset['unique_id']);
                                        $user_clint_map['user_id']      = $user_dtl[0]->id;
                                        $user_clint_map['folder_primary_id']    = $client_dtl[0]->id;
                                        users_folder_access::insert($user_clint_map);
                                    }
                                    else
                                    {
                                        $orgn_dtl['folder_name']            = $value['organization_name'];
                                        $orgn_dtl['organization_unique_id'] = $value['organization_unique_id'];
                                        $orgn_dtl['iss']                    = $dataset['iss'];
                                        $orgn_dtl['type']                    = 'Client';
                                        $orgn_ids[] = $value['organization_unique_id'];
                                        Client_folder_mapping::insert($orgn_dtl);
                                        $client_dtl = $this->helper->check_client($value['organization_unique_id']);
                                        $user_dtl = $this->helper->get_userid($dataset['unique_id']);
                                        $user_clint_map['user_id']      = $user_dtl[0]->id;
                                        $user_clint_map['folder_primary_id']    = $client_dtl[0]->id;
                                        users_folder_access::insert($user_clint_map);
                                        $this->helper->new_client_req_mail(1);
                                    }
                                  }
                                }
                                $datas = $this->show($dataset['unique_id']);
                                $datas[0]->organization = $this->show_orgn($orgn_ids);
                                return $this->helper->success($datas);
                            }
                       
                    }
                }
                else
                {
                    return $this->helper->error(201,'User Already Exits');
                }
            }
            else
            {
                return $this->helper->error(201,'Invalid Subject');
            }
                
                
        }
        elseif($userAuthentication == 440)
        {
            return $this->helper->error(440,'Token expired');
        }
        else
        {
            return $this->helper->error(401,'Unauthorized');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return DB::table('users')
            ->select('unique_id','name as first_name','last_name','usergroup','email')            
            ->where('unique_id', $id)
            ->get()
            ->toArray();
        if (!empty($getData)) {
            DB::commit();
            return $getData;
        }
        else{
            DB::rollBack();
            return false;
        }
    }
    public function show_orgn($ids)
    {
        return DB::table('client_folder_mapping')
            ->select('organization_unique_id','folder_name as organization_name','contact_email','owner_unique_id','is_approved')            
            ->wherein('organization_unique_id', $ids)
            ->get()
            ->toArray();
        if (!empty($getData)) {
            DB::commit();
            return $getData;
        }
        else{
            DB::rollBack();
            return false;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $token = $request->bearerToken();
        $userAuthentication = $this->helper->AuthenticatedUserDtl($token);
        if($userAuthentication['status'] == 200)
        {
            if($userAuthentication['data']->sub == "update_user")
            {
                $orgn_ids=[];
                $dataset = json_decode(json_encode($userAuthentication['data']), true);
                $is_exits = User::where('unique_id',$dataset['unique_id'])->where('unique_id','!=', $dataset['unique_id'])->exists();
                if(!$is_exits)
                {
                    if(empty($dataset['first_name']))
                    {
                        return $this->helper->error(201,'First Name is required');
                    }
                    elseif (empty($dataset['last_name'])) {
                        return $this->helper->error(201,'Last Name is required');
                    }
                    else
                    {
                  
                    $flag = 0;
                    $user_dtl['name']       = $dataset['first_name'];
                    $user_dtl['last_name']  = $dataset['last_name'];
                    $user_dtl['usergroup']  = $dataset['usergroup'];
                    $user_dtl['iss']        = $dataset['iss'];
                    if(isset($dataset['user_meta']['email']) && !empty($dataset['user_meta']['email']))
                    {
                    $is_exits1 = User::where('email', $dataset['user_meta']['email'])->where('unique_id','!=', $dataset['unique_id'])->exists();
                        if(!$is_exits1)
                        {
                            $user_dtl['email']      = $dataset['user_meta']['email'];     
                        }
                        else
                        {
                            return $this->helper->error(202,'Email Already Exits');
                        }
                    }
                    
                        $result = DB::table('users')
                        ->where('unique_id', $dataset['unique_id'])
                        ->update($user_dtl);
                        if(!empty($result))
                        {
                            $flag = 1;
                        }
                            if(!empty($dataset['organizations'])){
                            foreach ($dataset['organizations'] as $key => $value) {
                                if(isset($value['organization_unique_id_old']))
                                {
                                    $client_dtl_old = $this->helper->check_client($value['organization_unique_id_old']);
                                    $client_dtl     = $this->helper->check_client($value['organization_unique_id']);
                                    $user_dtl = $this->helper->get_userid($dataset['unique_id']);
                                    $user_clint_map['user_id']      = $user_dtl[0]->id;
                                    $user_clint_map['folder_primary_id']    = $client_dtl[0]->id;
                                    $result = DB::table('users_folder_access')
                                    ->where('user_id', $user_dtl[0]->id)
                                    ->where('folder_primary_id', $client_dtl_old[0]->id)
                                    ->update($user_clint_map);
                                    $flag = 1;
                                }
                                else
                                {
                                    $client_dtl = $this->helper->check_client($value['organization_unique_id']);
                                    if(!empty($client_dtl))
                                    {
                                        $user_dtl = $this->helper->get_userid($dataset['unique_id']);
                                        $user_clint_map['user_id']      = $user_dtl[0]->id;
                                        $user_clint_map['folder_primary_id']    = $client_dtl[0]->id;
                                        users_folder_access::insert($user_clint_map);
                                        $flag = 1;
                                    }
                                    else
                                    {
                                        $orgn_dtl['folder_name']            = $value['organization_name'];
                                        $orgn_dtl['organization_unique_id'] = $value['organization_unique_id'];
                                        $orgn_dtl['iss']                    = $dataset['iss'];
                                        $orgn_ids[] = $value['organization_unique_id'];
                                        Client_folder_mapping::insert($orgn_dtl);
                                        $client_dtl = $this->helper->check_client($value['organization_unique_id']);
                                        $user_dtl = $this->helper->get_userid($dataset['unique_id']);
                                        $user_clint_map['user_id']      = $user_dtl[0]->id;
                                        $user_clint_map['folder_primary_id']    = $client_dtl[0]->id;
                                        users_folder_access::insert($user_clint_map);
                                        $this->helper->new_client_req_mail(1);
                                        $flag = 1;
                                    }
                                }
                                
                                }
                            }
                            if($flag == 1)
                            {
                                $res = $this->show($dataset['unique_id']);
                                $res[0]->organization = $this->show_orgn($orgn_ids);
                                return $this->helper->success($res);
                            }
                        
                    }
                }
                else
                {
                    return $this->helper->error(202,'User Already Exits');
                }
            }
            else
            {
                return $this->helper->error(201,'Invalid Subject');
            }
        }
        elseif($userAuthentication == 440)
        {
            return $this->helper->error(440,'Token expired');
        }
        else
        {
            return $this->helper->error(401,'Unauthorized');
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $token = $request->bearerToken();
        $userAuthentication = $this->helper->AuthenticateUser($token);
        if($userAuthentication['status'] == 200)
        {
            $result = DB::table('users')->where('unique_id', $id)->delete();
            if(!empty($result))
            {
                return $this->helper->success(true);
            }
            else
            {
                return $this->helper->error(201,'Something went wrong');
            }
        }
        elseif($userAuthentication == 440)
        {
            return $this->helper->error(440,'Token expired');
        }
        else
        {
            return $this->helper->error(401,'Unauthorized');
        }
    }
    public function List(Request $request)
    {
        $token = $request->bearerToken();
        $userAuthentication = $this->helper->AuthenticatedUserDtl($token);
        if($userAuthentication['status'] == 200)
        {
            if($userAuthentication['data']->sub == "list_user")
            {
                $dataset = json_decode(json_encode($userAuthentication['data']), true);
                $users = DB::table('users')
                ->select('unique_id','name as first_name','last_name','usergroup','email')
                ->where('iss', $dataset['iss'])
                ->get();
                if(!empty($users))
                {
                    return $this->helper->success($users);
                }
                else
                {
                    return $this->helper->error(201,'No Data Found');
                }
            }
        }
        elseif($userAuthentication == 440)
        {
            return $this->helper->error(440,'Token expired');
        }
        else
        {
            return $this->helper->error(401,'Unauthorized');
        }
    }
}
