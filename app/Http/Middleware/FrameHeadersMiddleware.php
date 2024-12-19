<?php

namespace App\Http\Middleware;

use Closure;

class FrameHeadersMiddleware
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
        // $response->headers->set('X-Frame-Options', 'SAMEORIGIN', false);
        // $response->headers->set('X-Frame-Options', "*");
        // $response->headers->set('X-Frame-Options', 'ALLOWALL');
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'self' https://testmis.dynpro.com https://demo.909.care https://909healthcare.com  https://909.care https://mrs.909.care");
        $response->headers->set('Access-Control-Allow-Origin', "*");
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', "*");
        // $response->headers->set('Access-Control-Allow-Credentials', true);
        // $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');
        // $response->headers->set('X-Frame-Options', 'SAMEORIGIN', true);
        

        // $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        // $response->header('Content-Security-Policy', "frame-ancestors 'self'");
        return $response;
    }
}
