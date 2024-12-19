<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
class EventLogRoute
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
        $response = $next($request);

        if (app()->environment('local')) {
            $details = [
                'URI' => $request->fullUrl(),
                'METHOD' => $request->getMethod(),
                'Token' => $request->bearerToken(),
                'IP' => $request->ip(),
                'User_id' => (isset(auth()->user()->id)?auth()->user()->id : ""),
                'email' => (isset(auth()->user()->email)?auth()->user()->email : ""),
                'first_name' => (isset(auth()->user()->name)?auth()->user()->name : ""),
                'last_name' => (isset(auth()->user()->last_name)?auth()->user()->last_name : ""),
                'REQUEST_BODY' => $request->all(),
                'Cookies' => $request->cookie(),
            ];
                // 'RESPONSE' => $response->getContent(),

            // $log = stripslashes(json_encode($details));
            Log::channel('custom')->info(json_encode($details));
            
        }

        return $response;
    }
}
