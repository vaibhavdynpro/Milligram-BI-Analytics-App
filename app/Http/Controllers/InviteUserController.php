<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PHPMailer\PHPMailer;
use App\Libraries\Helpers;
use App\InviteUser;
use App\User;

use App\Mail\sendEmail;


class InviteUserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
		// $this->middleware('admin');
        $this->helper = new Helpers;
	
    }
  
  public function store(Request $request)
   { 
    
    $userlist = explode(",", $request->useremail);
    
    $userData = \DB::table('users')
            ->select('name','last_name')
            ->where('id',auth()->user()->id)
            ->get();      
    //print_r($userData); exit
    foreach($userlist as $key => $row){ 
        InviteUser::create([
            'user_id' => auth()->user()->id, 
            'user_email' => $row,
            'group_code'=> $request['groupcode'],
            'created_by' => auth()->user()->id,            
        ]); 
        //print_r($userlist);exit();
         $this->SendInvite($row,$userData[0]->name,$userData[0]->last_name,$request->groupcode);  
          
     } 

     return redirect('home')->with('success','User invition send successfully');
  }

public function SendInvite($useremail,$name,$last_name,$groupcode)
 {
    
    $text             = 'Hello,'."<br/>"."<br/>";
    $text             = $text.'You'."'".'re invited to Join Kairos Analytics Platform by '.$name.' '.$last_name.".<br/>";
    $text             = $text.$groupcode. " ".'is your group code please enter at the time of registration.'."<br/>";
    $text             = $text.'Please click on below link and use Signup to register your account.'."<br/>";
    $text             = $text.'https://hca.kairosrp.com/register_page'."<br/><br/>";
    $text             = $text. 'Thanks & Regards,'."<br/>";
    $text             = $text.'Kairos Admin';

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
    $mail->Subject = "Invite User";
    $mail->Body    = $text;
   // $mail->AddBCC('himanshu.s@us.dynpro.com');
    $mail->AddBCC('vilas.shinde@us.dynpro.com');
    $mail->AddAddress($useremail);
    
    $mail->Send();
 }

}
