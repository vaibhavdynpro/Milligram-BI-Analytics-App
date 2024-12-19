<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer;
use App\User;
use App\password_resets;
use Illuminate\Support\Facades\Hash;
class ResetPasswordController extends Controller
{
    public function reset_mail(Request $request)
    {

        //check if email exists
        $userArray= \DB::table('users')
        ->select('*')
        ->where(['email' => $request->email_id])
        ->where(['entity_id' => env('env_entity_id')])
        ->where(['is_active' => 1])
        ->get()->toArray();

        $six_digit_random_number = mt_rand(100000, 999999);
        if(count($userArray)==1){
            
            //create new record
            password_resets::updateOrCreate(
                ['email' => $request->email_id,'user_id' => $userArray[0]->id],
                [
                'token' => $six_digit_random_number  
            ]);
            // die();
        

            $text             = 'Your One Time Password For Resetting Password is : '."\r\n".$six_digit_random_number;
            $mail             = new PHPMailer\PHPMailer(); // create a n
            $mail->IsSMTP();
            // $mail->SMTPDebug  = 1; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth   = true; // authentication enabled
            $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host       = "email-smtp.us-east-1.amazonaws.com";
            $mail->Port       = 587; // or 587
            $mail->IsHTML(true);
            $mail->Username = "AKIAR2DNWFHADOVB3X75";
            $mail->Password = "BE3hsje11JymCh+nRogY4SxHoVGIEoloN4fK3xb0YQak";
            $mail->SetFrom("hca@kairosrp.com", 'Kairos App');
            $mail->Subject = "OTP For Password Change";
            $mail->Body    = $text;
            $mail->AddAddress($request->email_id, "Kairos User");
            $check=1;
            if ($mail->Send()) {
            // if ($check==1) {
                $email = $request->email_id;
                $user_id=$userArray[0]->id;
                return view('auth.passwords.password-reset',compact('email','user_id'));
            } else {
                return redirect('reset-pass-success')->with('Failed', 'Failed to Send Email!!');
            }
        }
        else
        {
            $error_email_message="Email Not Avaialble";
            return redirect()->back()->with('failed', 'Email Not Available');
        }
    }

    public function update_pass(Request $request)
    {
        $otp = $request->otp;
        $user_id = $request->user_id;
        $email = $request->email_id;
        $psw_to_update = $request->password2;
        $hashed_pass=Hash::make($psw_to_update);

        $otp_fetch= \DB::table('password_resets')
        ->select('*')
       ->where(['email' => $email,'token' => $otp])
        ->get()->toArray();

        if(count($otp_fetch)==1)
        {

            $user = User::find($user_id);
            $user->password = $hashed_pass;
            $user->save();
            return redirect('login');
        }
        else
        {
            $failed = "OTP invalid";
            return view('auth.passwords.password-reset',compact('email','user_id','failed'));
        }
    }
}
