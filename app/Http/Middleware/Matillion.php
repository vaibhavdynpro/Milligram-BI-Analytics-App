<?php

namespace App\Http\Middleware;
use App\User;
use Closure;

class Matillion
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
        ->leftJoin('roles', 'users.role', '=', 'roles.role_id')
        ->select('users.id','users.role','roles.users','roles.looker','roles.matillion','roles.roles','roles.clients','roles.dashboards','roles.phm')->first();
  //       echo "<pre>";
		// print_r($user_with_role->phm);
  //       exit();
		if($user_with_role->matillion =='0'){
			return redirect('home');
		}        
       
        return $next($request);
    }
}
