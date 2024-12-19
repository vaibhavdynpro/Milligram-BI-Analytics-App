<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Helpers;
use DB;
// use JWTAuth;
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
        auth()->setDefaultDriver('api');
        $this->helper = new Helpers;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = (auth()->guard('api')->attempt($credentials))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
    	$valid = auth()->check();
    	if($valid == 1)
    	{
    		if(!empty(auth()->user()))
    		{
	        	return response()->json([
	        	'data'  => auth()->user(),
	            'error' => '',
	            'status' => 200
	        	]);
	    	}
    	}    	
    	else
    	{   
    		return response()->json([
	        	'data'  => auth()->user(),
	            'error' => '',
	            'status' => 201
	        	]);
    	}
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    public function decodejwt(Request $request)
    {
        $token = $request->bearerToken();
        $userAuthentication = $this->helper->AuthenticatedUserDtl($token);
        if($userAuthentication['status'] == 200)
        {
            //if($userAuthentication['data']->sub == "kpi")
             if($userAuthentication['data']->sub == "data_request")
            {
            $client_dtl = $this->helper->get_client_Details($userAuthentication['data']->organization_unique_id);
            if(!empty($client_dtl))
            {
            $schema = "";
                if(!is_null($client_dtl[0]->schema_name))
                {
                    $schema = $client_dtl[0]->schema_name;
                }
           
                if(!empty($schema))
                {
                // $conn = odbc_connect("testodbc1","HIMANSHU","Node2me@git");
        
                // $sql1="select SUM(TOTAL_EMPLOYER_PAID_AMT)as value FROM ".$schema.".STG_TAB_MEDICAL_DATA";

                // $sql2="select IFF(COUNT(DISTINCT UNIQUE_ID) = 0, 0,SUM(TOTAL_EMPLOYER_PAID_AMT)/COUNT(DISTINCT UNIQUE_ID)) as value from ".$schema.".STG_TAB_MEDICAL_DATA";
                // $sql3="select COUNT(DISTINCT UNIQUE_ID)as value from ".$schema.".STG_TAB_MEDICAL_DATA";
                // $sql4="select SUM(TOTAL_EMPLOYER_PAID_AMT)as value from ".$schema.".STG_TAB_PHARMACY_DATA";
                // $sql5="select IFF(COUNT(DISTINCT UNIQUE_ID) = 0, 0, SUM(TOTAL_EMPLOYER_PAID_AMT)/COUNT(DISTINCT UNIQUE_ID))as value from ".$schema.".STG_TAB_PHARMACY_DATA";
                // $sql6="select COUNT(DISTINCT UNIQUE_ID) as value from ".$schema.".STG_TAB_PHARMACY_DATA";
                // $sql7="select SUM(TOTAL_EMPLOYER_PAID_AMT) as value from ".$schema.".STG_TAB_MEDICAL_DATA
                //         WHERE substring(RECONCILED_DIAGNOSIS_CODE_ICD10, 1, 1) = 'M'";
                // $sql8="select IFF(COUNT(DISTINCT UNIQUE_ID) = 0, 0, SUM(TOTAL_EMPLOYER_PAID_AMT)/COUNT(DISTINCT UNIQUE_ID))as value from ".$schema.".STG_TAB_MEDICAL_DATA WHERE substring(RECONCILED_DIAGNOSIS_CODE_ICD10, 1, 1) = 'M'";
                // $sql9="select COUNT(DISTINCT UNIQUE_ID) as value from ".$schema.".STG_TAB_MEDICAL_DATA 
                //         WHERE substring(RECONCILED_DIAGNOSIS_CODE_ICD10, 1, 1) = 'M'";
                
                // $query1 = odbc_exec($conn, $sql1);
                // $result1 = odbc_fetch_array($query1);
                // $query2 = odbc_exec($conn, $sql2);
                // $result2 = odbc_fetch_array($query2);
                // $query3 = odbc_exec($conn, $sql3);
                // $result3 = odbc_fetch_array($query3);
                // $query4 = odbc_exec($conn, $sql4);
                // $result4 = odbc_fetch_array($query4);

                //     $arr['KPI'][0]['data_type'] = "single_value";
                //     $arr['KPI'][0]['title'] = "Total Medical Spend";
                //     $arr['KPI'][0]['value'] = round($result1['VALUE']);

                //     $arr['KPI'][1]['data_type'] = "single_value";
                //     $arr['KPI'][1]['title'] = "Mean Medical";
                //     $arr['KPI'][1]['value'] = round($result2['VALUE']);

                //     $arr['KPI'][2]['data_type'] = "single_value";
                //     $arr['KPI'][2]['title'] = "Total Medical Patients";
                //     $arr['KPI'][2]['value'] = round($result3['VALUE']);

                //     $arr['KPI'][3]['data_type'] = "single_value";
                //     $arr['KPI'][3]['title'] = "Total Pharmacy Spend";
                //     $arr['KPI'][3]['value'] = round($result4['VALUE']);  

           
                $curl = curl_init();
   
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://8gw1bd7nnd.execute-api.us-east-1.amazonaws.com/sf-deploy',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>'{
                   "query":"SELECT SUM(TOTAL_EMPLOYER_PAID_AMT) AS \\"Total Medical Spend\\",IFF(COUNT(DISTINCT UNIQUE_ID) = 0, 0,SUM(TOTAL_EMPLOYER_PAID_AMT)/COUNT(DISTINCT UNIQUE_ID)) AS \\"Mean Medical\\",COUNT(DISTINCT UNIQUE_ID) AS \\"Total Medical Patients\\",b.value3 AS \\"Total Pharmacy Spend\\" FROM STG_TAB_MEDICAL_DATA a join (select SUM(TOTAL_EMPLOYER_PAID_AMT)as value3 from VW_PHARMACY WHERE YEAR(DATE_FILLED) IN (2015,2016,2017,2018,2019)) b WHERE YEAR(a.PAID_DATE) IN (2015,2016,2017,2018,2019) group by b.value3",
                   "schema":"SCH_KAIROS_ARKANSAS_MUNICIPAL_LEAGUE"
                }',
                  CURLOPT_HTTPHEADER => array(
                    'x-api-key: nBCbDwJZYe8pLENWbFEjvaWH6tzdOklh5vLXWCVJ',
                    'Content-Type: application/json'
                  ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $resultset = json_decode($response);
          
                $arr = [];
                foreach ($resultset as $key => $value) {
                    $i = 0;
                    foreach($value as $key1 => $value1)
                    {
                       $arr['KPI'][$i]['data_type'] = "single_value";
                       $arr['KPI'][$i]['title']     = $key1;
                       $arr['KPI'][$i]['value']     = round($value1);
                       $i++;
                    }
                   
                }
                    if(!empty($arr))
                    {
                        return response()->json([
                        'data'  => $arr,
                        'error' => '',
                        'status' => 200
                        ]);
                    }
                }
                else
                {
                    return response()->json([
                    'data'  => [],
                    'message'=>'The dataset for the organization is not available yet.Please contact System Administrator for more details',
                    'error' => '',
                    'status' => 201
                    ]);
                }
                }
                else
                {
                    return $this->helper->error(201,'Orgnization Not Found');
                }
            }
            else
            {
                return $this->helper->error(201,'Invalid Subject');
            }
            
                
        }
        elseif($userAuthentication['status'] == 440)
        {
                return response()->json([
                'token'  => '',
                'error' => 'Token expired',
                'status' => 440
                ]);
        }
        else
        {
            return response()->json([
                'token'  => '',
                'error' => 'Unauthorized',
                'status' => 401
                ]);
        }
        
    }
    public function decodejwt_v1(Request $request)
    {
        $token = $request->bearerToken();
        $userAuthentication = $this->helper->AuthenticatedUserDtl($token);
        if($userAuthentication['status'] == 200)
        {
                      
                $arr['KPI'][0]['data_type'] = "single_value";
                $arr['KPI'][0]['title'] = "Total Medical Spend";
                $arr['KPI'][0]['value'] =  8762377634;

                $arr['KPI'][1]['data_type'] = "single_value";
                $arr['KPI'][1]['title'] = "Mean Medical";
                $arr['KPI'][1]['value'] = "$3456";

                $arr['KPI'][2]['data_type'] = "single_value";
                $arr['KPI'][2]['title'] = "Total Medical Patients";
                $arr['KPI'][2]['value'] = "7657656";

                $arr['KPI'][3]['data_type'] = "single_value";
                $arr['KPI'][3]['title'] = "Total Pharmacy Spend";
                $arr['KPI'][3]['value'] = "$87638748";  

//                 $arr['KPI'][4]['data_type'] = "single_value";
//                 $arr['KPI'][4]['title'] = "Mean Pharmacy";
//                 $arr['KPI'][4]['value'] = 64767676;  

//                 $arr['KPI'][5]['data_type'] = "single_value";
//                 $arr['KPI'][5]['title'] = "Total Pharmacy Patients";
//                 $arr['KPI'][5]['value'] = "$85467";

//                 $arr['KPI'][6]['data_type'] = "single_value";
//                 $arr['KPI'][6]['title'] = "Total MSK Medical";
//                 $arr['KPI'][6]['value'] = "$985746";

//                 $arr['KPI'][7]['data_type'] = "single_value";
//                 $arr['KPI'][7]['title'] = "Mean MSK Medical";
//                 $arr['KPI'][7]['value'] = "765757";

//                 $arr['KPI'][8]['data_type'] = "single_value";
//                 $arr['KPI'][8]['title'] = "MSK Total Patients";
//                 $arr['KPI'][8]['value'] = 2355667;  

                if(!empty($arr))
                {
                    return response()->json([
                    'data'  => $arr,
                    'error' => '',
                    'status' => 200
                    ]);
                }
          
            
                
        }
        elseif($userAuthentication['status'] == 440)
        {
                return response()->json([
                'token'  => '',
                'error' => 'Token expired',
                'status' => 440
                ]);
        }
        else
        {
            return response()->json([
                'token'  => '',
                'error' => 'Unauthorized',
                'status' => 401
                ]);
        }
        
    }
    public function verifyjwt()
    {
        $secret = env('JWT_SECRET');
        $token = JWTAuth::getToken();
        $tokenParts = explode(".", $token);  
        $tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($tokenHeader));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($tokenPayload));
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        if($base64UrlSignature == $tokenParts[2])
        {
            return "verified";
        }
    } 
    public function iframe(Request $request)
    {
        $token = $request->bearerToken();
        $userAuthentication = $this->helper->AuthenticatedUserDtl($token);
        if($userAuthentication['status'] == 200)
        {
            //if($userAuthentication['data']->sub == "embed_url")
            if($userAuthentication['data']->sub == "sso_request")
            {
            $UserData= \DB::table('users')
            ->select('users.unique_id','users.id')
            ->where('users.unique_id',$userAuthentication['data']->unique_id)
            ->get();                    
            if(isset($UserData[0]->unique_id))
            {
                $client_dtl = $this->helper->get_client_Details($userAuthentication['data']->organization_unique_id);
                if(!empty($client_dtl))
                {
                    $schema = "";
                    if(!is_null($client_dtl[0]->folder_id))
                    {
                        $schema = $client_dtl[0]->schema_name;
                    }
                    if(!empty($schema))
                    {
                        $id = $this->helper->encrypt_id($UserData[0]->id);
                        $app_url = env('APP_URL');
                        $url = $app_url.'/autologin/'.$id;
                        return response()->json([
                        'embed_url'  => $url,
                        'error' => '',
                        'status' => 200
                        ]);
                    }
                    else
                    {
                        return response()->json([
                        'embed_url'  => "",
                        'message'=>'The dataset for the organization is not available yet.Please contact System Administrator for more details',
                        'error' => '',
                        'status' => 202
                        ]);
                    }
                }
                else
                {
                    return response()->json([
                    'url'  => "",
                    'error' => 'Invalid Organization',
                    'status' => 201
                    ]);
                }
            }
            else
            {
                return response()->json([
                'url'  => "",
                'error' => 'User Not Found',
                'status' => 201
                ]);
            }
            }
            else{
                return $this->helper->error(201,'Invalid Subject');
            }                
        }
        elseif($userAuthentication == 440)
        {
                return response()->json([
                'token'  => '',
                'error' => 'Token expired',
                'status' => 440
                ]);
        }
        else
        {
            return response()->json([
                'token'  => '',
                'error' => 'Unauthorized',
                'status' => 401
                ]);
        }
    }   
    public function iframe_v1(Request $request)
    {
        $token = $request->bearerToken();
        $userAuthentication = $this->helper->AuthenticatedUserDtl($token);
        if($userAuthentication['status'] == 200)
        {
            $UserData= \DB::table('users')
            ->select('users.unique_id','users.id')
            ->where('users.unique_id',$userAuthentication['data']->unique_id)
            ->get();                    
            if(isset($UserData[0]->unique_id))
            {             
                    $id = $this->helper->encrypt_id($UserData[0]->id);
                    $app_url = env('APP_URL');
                    $url = $app_url.'/autologin/'.$id;
                    return response()->json([
                    'embed_url'  => $url,
                    'error' => '',
                    'status' => 200
                    ]);
            }
            else
            {
                return response()->json([
                'url'  => "",
                'error' => 'user not found',
                'status' => 201
                ]);
            }                
        }
        elseif($userAuthentication == 440)
        {
                return response()->json([
                'token'  => '',
                'error' => 'Token expired',
                'status' => 440
                ]);
        }
        else
        {
            return response()->json([
                'token'  => '',
                'error' => 'Unauthorized',
                'status' => 401
                ]);
        }
    }   
}
