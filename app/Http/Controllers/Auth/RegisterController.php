<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Otp;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer;
use App\Libraries\Helpers;
use Mail;
use App\Mail\sendEmail;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->helper = new Helpers;
    }
    public function showsignup()
    {
        return view('auth.signup');
    }
    public function OptPage($email)
    {   
        
        $email_id = $this->helper->encrypt_decrypt($email,'decrypt');
        return view('auth.Signup_otp',compact('email_id'));
    }
         
    public function ResendOtpPage($email){
         
         $email_id = $this->helper->encrypt_decrypt($email,'decrypt');
         return view('auth.resend-otp',compact('email_id'));
    }

    public function register(Request $request)
    {  
       
       $this->validator($request->all())->validate();
       $this->create($request->all());
      // $this->send_welcome_mail($entity,$request->first_name,$request->last_name,$request->email,$request->group);
       
       return redirect('register_page')->with('success', 'Your Details has been successfully save & Email Sent to admin for approval!!'); 
    }
    
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    
    protected function validator(array $data)
    {   
        return Validator::make($data, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required','unique:users', 'string','email','max:255'],
            'password' => ['required', 'string', 'min:8', 'max:12',
                            'regex:/[a-z]/',      // must contain at least one lowercase letter
                            'regex:/[A-Z]/',      // must contain at least one uppercase letter
                            'regex:/[0-9]/',      // must contain at least one digit
                            'regex:/[@$!%*#?&]/'], // must contain a special character],

            'confirm_password' =>['required','string','min:8','same:password','max:12',
                                'regex:/[a-z]/',      // must contain at least one lowercase letter
                                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                                'regex:/[0-9]/',      // must contain at least one digit
                                'regex:/[@$!%*#?&]/'],

            'group_code' => ['required','exists:groups,group_id'],
        ],
        [
                'password.regex' => 'Password must contain at least one number and both uppercase and lowercase letters and one special character.'
            ]
        );
    }
    
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {   
        $userDtl = DB::table('users')         
            ->orderBy('id','DESC')
            ->limit(1)
            ->get()
            ->toArray();
        $extr_usr_id= $userDtl[0]->external_user_id + 1;
        return User::create([
            'name' => $data['firstname'],
            'last_name' => $data['lastname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_group_id' => $data['group_code'],
            'entity_id' => env('env_entity_id'),
            'permissions' => "viewer",
            'external_user_id' => $extr_usr_id,
            'user_attributes' => "UA",
            'is_active' => 1,
            'created_by' => 1,
        ]);
    }
    
    public function store(Request $request){  
        $this->validator($request->all())->validate();
        
        $otp = rand(1000,9999);
         Otp::create([
            'first_name' => $request['firstname'],
            'last_name' => $request['lastname'],
            'email' => $request['email'],
            'group_code' => $request['group_code'],
            'otp' => $otp, 
            'otp_count' => 1,
             
        ]);
         
        $email_id = $this->helper->encrypt_decrypt($request['email'],'encrypt');
        $this->SendOtp($otp,$request['email'],$request['firstname']);
        return redirect('OptPage/'.$email_id)->with('success','One time password send successfully');
         
    }

    public function updateotp(Request $request){
         
         $otp = rand(1000,9999);
         
         $ReotpData = \DB::table('signup_otp')
            ->select('*')
            ->where('email',$request->email)
            ->get();
         
          if(!empty($ReotpData)){
            $otp_count = ($ReotpData[0]->otp_count);
            
            if($ReotpData[0]->flag == 0){
            
            if($otp_count <= 4){
             
            DB::table('signup_otp')
            ->where('email', $request->email)
            ->update(['otp' => $otp , 'otp_count'=> $otp_count + 1]);
            
            $this->SendOtp($otp, $request->email,$ReotpData[0]->first_name);
            
            $email = $this->helper->encrypt_decrypt(($request->email),'encrypt');
            
            return redirect('OptPage/'.$email)->with('success','One time password send successfully');    
          }
         else{
           
            return redirect('register_page')->with('success','Number of attempts exhausted');   
          }  
         
        }
    
      else{
        
         return redirect('register_page')->with('success','Email id already registered');  
   
       }
    }
}
  
    public function send_welcome_mail($first_name,$last_name,$email,$group){
        if(env('env_entity_id') == 1){$entity="<a href='https://hca.kairosrp.com'>HCA</a>";}
        elseif(env('env_entity_id') == 2){$entity="<a href='https://mrs.kairosrp.com'>MRS</a>";}
        $text             = 'Hello Admin,'."<br/>"."<br/>";
        $text             = $text.'New user registerd on '.$entity.' Platform.'."<br/>";
        $text             = $text.'User Details:'."<br/>";
        $text             = $text.'Name: '.$first_name.' '.$last_name. "<br/>";
        $text             = $text.'Email id: '.$email."<br/>";
        $text             = $text.'Group Code: '.$group."<br/>"."<br/>";
        $text             = $text.'Thank You,'."<br/>";
        $text             = $text.'Team Kairos';
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
        $mail->Subject = "New User Registration";
        $mail->Body    = $text;
        $mail->AddAddress('vaibhav.dwivedi@us.dynpro.com');
        $mail->AddCC('himanshu.s@us.dynpro.com');
        $mail->AddCC('mahesh.bhalchandra@us.dynpro.com');
        
        $check=1;
        // $mail->Send();
        if ($mail->Send()) {
            
            return true;
        }
        else{
            return false;
        }
    }
   
    public function SendOtp($otp, $email, $first_name){ 
            
            $text             = 'Hello '.$first_name."<br/><br/>";
            $text             = $text.'We thank you for signing up on the Kairos platform.'."<br/><br/>";
            $text             = $text.'Kindly enter below provided OTP (valid up to 1 hour) on the sign up page.'."<br/>";
            $text             = $text.'OTP: '.$otp."<br/>";
            $text             = $text.'Email id: '.$email."<br/>";
            $text             = $text.'After entering OTP click on signup , your membership request is sent for approval to our Administrator.'."<br/><br/>";
            $text             = $text.'Once we approve it you will recieve another confirmation mail with instructions to login.'."<br/><br/>";
            $text             = $text.'Thank You,'."<br/><br/>";
            $text             = $text. 'With Regards,'."<br/>";
            $text             = $text.'Kairos Admin Team';

            $mail             = new PHPMailer\PHPMailer(); // create a n
            $mail->IsSMTP();
            
           // $mail->SMTPDebug  = 2; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth   = true; // authentication enabled
            $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host       = "email-smtp.us-east-1.amazonaws.com";
            $mail->Port       = 587; ; // or 587
            $mail->IsHTML(true);
            $mail->Username = "AKIAR2DNWFHADOVB3X75";
            $mail->Password = "BE3hsje11JymCh+nRogY4SxHoVGIEoloN4fK3xb0YQak";
            $mail->SetFrom("hca@kairosrp.com", 'Kairos App');
            $mail->Subject = "Registration OTP";
            $mail->Body    = $text;
            $mail->AddAddress($email);
            
            if($mail->Send()){    
            
              return response(["status" => 200, "message" => "OTP sent successfully"]);
            }
            else{
                return response(["status" => 401, 'message' => 'Invalid']);
            }
      
  }
  
   /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
     
    public function validateOtp(Request $request){
       
        $OtpData = \DB::table('signup_otp')
        ->select('*')
        ->where('email',$request->validateemail)
        ->where('otp',$request->validateotp)
        ->get();
       
        $userDtl = DB::table('users')         
        ->orderBy('id','DESC')
        ->limit(1)
        ->get()
        ->toArray();

     $extr_usr_id= $userDtl[0]->external_user_id + 1;

       if(isset($OtpData[0]) && !empty($OtpData[0])){
        if($OtpData[0]->flag == 0){
            $dateTimeObject1 = date_create($OtpData[0]->created_at); 
            $dateTimeObject2 = date_create();
            $difference = date_diff($dateTimeObject1, $dateTimeObject2); 
            $minutes = $difference->days * 24 * 60;
            $minutes += $difference->h * 60;
            $minutes += $difference->i;
            
            if($minutes <= 60){
             
             User::create([
                'name' => ($OtpData[0]->first_name),
                'last_name' =>($OtpData[0]->last_name),
                'email' => ($OtpData[0]->email),
                'user_group_id' => ($OtpData[0]->group_code),
                'created_at' => ($OtpData[0]->created_at),
                 'is_signup'=> 1,
                'created_by' => 1,
                'external_user_id' => $extr_usr_id,
                'is_signup' => 1,
                'is_active' => 1, 
                ]);
                 
                 DB::table('signup_otp')
                 ->where('email',$OtpData[0]->email)
                 ->update(['flag'=> 1]);  
                  
                  $this->send_welcome_mail($OtpData[0]->first_name,$OtpData[0]->last_name,$OtpData[0]->email,$OtpData[0]->group_code);                                  
                 return redirect('register_page')->with('success', 'Your Details has been successfully saved & Email Sent to admin for approval!!'); 
                  
               }else{

                     $email_id = $this->helper->encrypt_decrypt(($request->validateemail),'encrypt');  
                     return redirect('resend_otp/'.$email_id)->with('success','Session expired');     

               }
            }

        }else{
                
                  $email_id = $this->helper->encrypt_decrypt(($request->validateemail),'encrypt'); 
                  return redirect('OptPage/'.$email_id)->with('success','Invalid OTP. Please enter the OTP sent to your email');  
              
            }
         
    }
    
 
}


