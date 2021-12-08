<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
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
        $origin = $request->header('origin');
        if($origin){
            $allowed_origins = config('sdk-domain-except', []);

            if (in_array($origin, $allowed_origins, true) === true) {
                return $next($request)->header('Access-Control-Allow-Origin',$origin)
                    ->header('Access-Control-Allow-Headers', 'Origin, Set-Cookie, x-xsrf-token, X-CSRF-TOKEN, X-Requested-With, Content-Type, Accept')
                    ->header('Access-Control-Allow-Methods', 'GET, HEAD, POST, PUT, DELETE, OPTIONS')
                    ->header('Access-Control-Allow-Credentials', 'true');
            }else{
                return response(['success' => false, 'message' => 'Origin not allow', 'origin' => $origin], 429);
            }
        }
        return $next($request);
    }
}

