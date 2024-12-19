<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Helpers;
use App\Client_folder_mapping;
use DB;
use PHPMailer\PHPMailer;
class ClientController extends Controller
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
        $userAuthentication = $this->helper->AuthenticatedUserDtl($token);
        if($userAuthentication['status'] == 200)
        {
            $orgn_ids = [];
            if($userAuthentication['data']->sub == "create_org")
            {
                $clientset = [];
                $dataset = json_decode(json_encode($userAuthentication['data']->organizations), true);   
                foreach ($dataset as $key => $value) {
                    $client_dtl = $this->helper->check_client($value['organization_unique_id']);
                    if(!empty($client_dtl))
                    {
                        unset($dataset[$key]);
                    }
                }           
                foreach ($dataset as $key => $value) {              
                    $orgn_ids[] = $value['organization_unique_id'];               
                    foreach ($value as $key1 => $value1) {
                        
                        if($key1 == "organization_name")
                        {
                        $clientset[$key]['org_id']      = $value['organization_unique_id'];
                        $clientset[$key]['org']         = $value1;
                        $dataset[$key]['folder_name']   = $value1;
                        $dataset[$key]['type']          = 'Client';
                        $dataset[$key]['iss']           = $userAuthentication['data']->iss;                        
                        unset($dataset[$key][$key1]);                     
                        }
                        if($key1 == "org_meta")
                        {
                        $dataset[$key]['contact_email']     = $value1['contact_email'];
                        $dataset[$key]['owner_unique_id']   = $value1['owner_unique_id'];
                        unset($dataset[$key][$key1]);
                        }
                    }                    
                }
                $count = 0;  
                if(!empty($dataset)){           
                    $result = Client_folder_mapping::insert($dataset);
                    if (!empty($result)) {
                        $lastInsertedId = DB::getPdo()->lastInsertId();
                        $datas = $this->show($orgn_ids);
                        $this->new_client_req_mail($clientset);
                        return $this->helper->success($datas);
                    }
                    else
                    {
                        return $this->helper->error(203,'Something went wrong');
                    }
                }
                else
                {
                    return $this->helper->error(202,'Client Already Exits');
                }
            }
            else{
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
    public function show($ids)
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
            if($userAuthentication['data']->sub == "update_org")
            {
                $dataset = json_decode(json_encode($userAuthentication['data']->organizations), true);
                // foreach ($dataset as $key => $value) {
                    // $client_dtl = $this->helper->check_orgn($value['organization_unique_id'],$value['organization_name']);
                //     if(!empty($client_dtl))
                //     {
                //         unset($dataset[$key]);
                //     }
                // }
                foreach ($dataset as $key => $value) {
                    $orgn_ids[] = $value['organization_unique_id'];
                    foreach ($value as $key1 => $value1) {
                        if($key1 == "organization_name")
                        {
                        $dataset[$key]['folder_name'] = $value1;
                        unset($dataset[$key][$key1]);           
                        $dataset[$key]['iss'] = $userAuthentication['data']->iss; 
                        $dataset[$key]['updated_at'] = date("Y-m-d H:i:s");         
                        }
                        if($key1 == "org_meta")
                        {
                        if(isset($value1['contact_email'])){$dataset[$key]['contact_email'] = $value1['contact_email'];}     
                        if(isset($value1['owner_unique_id'])){$dataset[$key]['owner_unique_id']   = $value1['owner_unique_id'];}
                        
                        unset($dataset[$key][$key1]);
                        }
                    } 
                }
               $result = [];
               if(!empty($dataset))
               {
                    foreach ($dataset as $key => $value) {
                        $result = DB::table('client_folder_mapping')
                        ->where('organization_unique_id', $dataset[$key]['organization_unique_id'])
                        ->update($dataset[$key]);
                    }
                    if(!empty($result))
                    {
                        $datas = $this->show($orgn_ids);
                        return $this->helper->success($datas);
                    }
                    else
                    {
                        return $this->helper->error(202,'Nothing to Update');
                    }
               }
               else
               {
                    return $this->helper->error(202,'Nothing to Update');
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
            $result = DB::table('client_folder_mapping')->where('id', $id)->delete();
            if(!empty($result))
            {
                return $this->helper->success(true);
            }
            else
            {
                return $this->helper->error(201,'Something Went Wrong');
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
            if($userAuthentication['data']->sub == "organization")
            {
                $dataset = json_decode(json_encode($userAuthentication['data']), true);
                $clients = DB::table('client_folder_mapping')
                ->select('organization_unique_id','folder_name as organization_name','contact_email','owner_unique_id','is_approved')    
                ->where('iss', $dataset['iss'])
                // ->where('is_approved', 1)
                ->get();
                if(!empty($clients))
                {
                    return $this->helper->success($clients);
                }
                else
                {
                    return $this->helper->error(202,'No Data Found');
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



    public function new_client_req_mail($clients){

        $text             = 'Hello Team,'."<br/>"."<br/>";
        $text             = $text.' '."New client request generated by 909 healthcare.<br/><br/><br/>";
        $text             = $text.' '."<b>Client List:</b><br/><br/>";
        foreach ($clients as $key => $value) {
            $cnt = $key+1;
            $text = $text.' '.$cnt." - ".$value['org_id']."->".$value['org']."<br/>";
        }
        $text             = $text.' '."<br/><br/><br/><br/><br/>";
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
        $mail->Subject = "New Client Request";
        $mail->Body    = $text;
        $mail->AddAddress('punit.bopche@dynproindia.com', "system admin");
        $mail->AddCC('himanshu.s@dynproindia.com', "tester");
        $mail->AddCC('mahesh.bhalchandra@dynproindia.com', "Developer");
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
