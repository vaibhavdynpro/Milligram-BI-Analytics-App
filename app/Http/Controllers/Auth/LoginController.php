<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Auth;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }
    public function authenticate(Request $request)
    {
        
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'g-recaptcha-response' => 'required|captcha'
        ]);
        $credentials = array(
            'email'     => $request->get('email'),
            'password'  => $request->get('password'),
            'entity_id' => env('env_entity_id'),
            'is_active' => 1
        );

        if (Auth::attempt($credentials)) 
        {

            if(auth()->user()->role == 1)
            {
                return redirect()->route('home');
            }
            elseif(auth()->user()->entity_id == env('env_entity_id'))
            {                
                return redirect()->route('home');                
            }
            else
            {
            Auth::logout();
            return redirect()->route('login');
            }
        }
        else
        {
            return redirect()->route('login');
        }
    }
    
    public function autologin($id)
    {
        $user_id = $this->decrypt_id($id);
        if(isset($user_id) && $user_id != "#201")
        {

            $userData = Auth::loginUsingId($user_id);
            // $userData = Auth::login($arr,true);

            if(!empty($userData))
            {
            return redirect()->route('home');            
            }
            else
            {
                echo "Unauthorized user";
            }
        }
        else
        {
            echo "Invalid User";
        }
        
    }
    public function decrypt_id($id)
    {
        
        try {
            return $decrypted = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            return "#201";
        }
    }
}
