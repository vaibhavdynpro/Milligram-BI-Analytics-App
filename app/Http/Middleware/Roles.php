<?php

namespace App\Http\Middleware;
use App\User;
use Closure;

class Roles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_with_role = User::where('id', $request->user()->id)
        ->leftJoin('grp_role_usr_mapping', 'users.id', '=', 'grp_role_usr_mapping.user_id')
        ->select('grp_role_usr_mapping.roles')->first();
  //       echo "<pre>";
		// print_r($user_with_role->roles);
  //       exit();
		if($user_with_role->roles =='0'){
			return redirect('home');
		}        
       
        return $next($request);
    }
}
