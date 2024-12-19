<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Helpers;
use App\users_folder_access;
use DB;
class UserClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->helper = new Helpers;
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $userAuthentication = $this->helper->AuthenticateUser($token);
        if($userAuthentication['status'] == 200)
        {
            $data = $request->all();
            foreach ($data['payload'] as $key => $value) {
                $user_dtl = $this->helper->get_userid($value['unique_id']);
                unset($data['payload'][$key]['unique_id']);
                $data['payload'][$key]['user_id'] = $user_dtl[0]->id;
            }
            $result = users_folder_access::insert($data['payload']);
            if (!empty($result)) {
                // $datas = $this->show($result->id);
                return $this->helper->success($result);
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
        $user_dtl = $this->helper->get_userid($id);
        return DB::table('users_folder_access')
            ->select('users_folder_access.*')            
            ->where('user_id', $user_dtl[0]->id)
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
    public function update(Request $request, $id)
    {
        $token = $request->bearerToken();
        $userAuthentication = $this->helper->AuthenticateUser($token);
        if($userAuthentication['status'] == 200)
        {
            $data = $request->all();
            $user_dtl = $this->helper->get_userid($id);
            $is_mapped = $this->helper->check_user_client_mapping($user_dtl[0]->id,$data['old_folder_id']);
            if(isset($is_mapped) && !empty($is_mapped))
            {
                $dataset['folder_id']=$data['new_folder_id'];
                $result = DB::table('users_folder_access')
                ->where('id', $is_mapped[0]->id)
                ->update($dataset);
                if (!empty($result)) {
                    return $this->helper->success($result);
                }
            }
            else
            {
                return $this->helper->error(201,'User cant mapped with folder id');
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
            $data = $request->all();
            $user_dtl = $this->helper->get_userid($id);
            $is_mapped = $this->helper->check_user_client_mapping($user_dtl[0]->id,$data['folder_id']);
            if(isset($is_mapped) && !empty($is_mapped))
            {
                $result = DB::table('users_folder_access')->where('id', $is_mapped[0]->id)->delete();
                if(!empty($result))
                {
                    return $this->helper->success(true);
                }
                else
                {
                    return $this->helper->error(202,'Something went wrong');
                }
            }
            else
            {
                return $this->helper->error(201,'User cant mapped with folder id');
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
        $userAuthentication = $this->helper->AuthenticateUser($token);
        if($userAuthentication['status'] == 200)
        {
            $data = $request->all();
            $user_dtl = $this->helper->get_userid($data['unique_id']);
            $clients = DB::table('users_folder_access as a')
            ->select('a.folder_id as client_id','c.folder_name as client_name','b.unique_id','b.name as first_name','b.last_name')
            ->leftjoin('users as b', 'a.user_id', '=', 'b.id')
            ->leftjoin('client_folder_mapping as c', 'a.folder_id', '=', 'c.folder_id')
            ->where('user_id', $user_dtl[0]->id)
            ->get();
            if(!empty($clients))
            {
                return $this->helper->success($clients);
            }
            else
            {
                return $this->helper->error(201,'No Data Found');
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
    public function getdata(Request $request)
    {       
        $sql1="select SUM(TOTAL_EMPLOYER_PAID_AMT)as value FROM SCH_IMA_ALL_GROUPS.STG_TAB_MEDICAL_DATA";
        // $sql2="select IFF(COUNT(DISTINCT UNIQUE_ID) = 0, 0,SUM(TOTAL_EMPLOYER_PAID_AMT)/COUNT(DISTINCT UNIQUE_ID)) as value from SCH_IMA_ALL_GROUPS.STG_TAB_MEDICAL_DATA";
        // $sql3="select COUNT(DISTINCT UNIQUE_ID)as value from SCH_IMA_ALL_GROUPS.STG_TAB_MEDICAL_DATA";
        // $sql4="select SUM(TOTAL_EMPLOYER_PAID_AMT)as value from SCH_IMA_ALL_GROUPS.STG_TAB_PHARMACY_DATA";
        // $sql5="select IFF(COUNT(DISTINCT UNIQUE_ID) = 0, 0, SUM(TOTAL_EMPLOYER_PAID_AMT)/COUNT(DISTINCT UNIQUE_ID))as value from SCH_IMA_ALL_GROUPS.STG_TAB_PHARMACY_DATA";
        // $sql6="select COUNT(DISTINCT UNIQUE_ID) as value from SCH_IMA_ALL_GROUPS.STG_TAB_PHARMACY_DATA";
        // $sql7="select SUM(TOTAL_EMPLOYER_PAID_AMT) as value from SCH_IMA_ALL_GROUPS.STG_TAB_MEDICAL_DATA
        //         WHERE substring(RECONCILED_DIAGNOSIS_CODE_ICD10, 1, 1) = 'M'";
        // $sql8="select IFF(COUNT(DISTINCT UNIQUE_ID) = 0, 0, SUM(TOTAL_EMPLOYER_PAID_AMT)/COUNT(DISTINCT UNIQUE_ID))as value from SCH_IMA_ALL_GROUPS.STG_TAB_MEDICAL_DATA WHERE substring(RECONCILED_DIAGNOSIS_CODE_ICD10, 1, 1) = 'M'";
        // $sql9="select COUNT(DISTINCT UNIQUE_ID) as value from SCH_IMA_ALL_GROUPS.STG_TAB_MEDICAL_DATA 
        //         WHERE substring(RECONCILED_DIAGNOSIS_CODE_ICD10, 1, 1) = 'M'";
        // return $result =  DB::connection('odbc')->select($sql9);
        // print_r($result[0]->VALUE);
         $conn = odbc_connect("Driver=SnowflakeDSIIDriver;Server=cya15100.us-east-1.snowflakecomputing.com;Account=cya15100.us-east-1;Database=DB_KAIROS_PROD;Warehouse=WH_SNOWFLAKE;DefaultSchema=SCH_IMA_ALL_GROUPS;Role=SYSADMIN;","HIMANSHU","Node2me@git");
        $query = odbc_exec($conn, $sql1);
        $row = odbc_fetch_array($query);
        print_r($row['VALUE']);
        exit();
        while($row = odbc_fetch_array($query)){
            echo $row["value"] . "\n";
        }
        exit();
       // return $books = DB::connection('odbc')->table('STG_TAB_MEDICAL_DATA')->limit(5)->get();
    }
}
 // $conn = odbc_connect("Driver=SnowflakeDSIIDriver;Server=cya15100.us-east-1.snowflakecomputing.com;Account=cya15100.us-east-1;Database=DB_KAIROS_PROD;Warehouse=WH_SNOWFLAKE;DefaultSchema=SCH_IMA_ALL_GROUPS;Role=SYSADMIN;","HIMANSHU","Node2me@git");
        // $query = odbc_exec($conn, "SELECT * FROM SCH_IMA_ALL_GROUPS.STG_TAB_MEDICAL_DATA limit 30");
        // // $row = odbc_fetch_array($query);
        // while($row = odbc_fetch_array($query)){
        //     echo $row["EMPLOYEE_F_NAME"] . "\n";
        // }
        // exit();
       // return $books = DB::connection('odbc')->table('STG_TAB_MEDICAL_DATA')->limit(5)->get();