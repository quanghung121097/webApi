<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthCustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if($user == NULL || $user->role != 'customer'){
            return response(['success' => false, 'message' => 'Tài khoản không có quyền thực hiện hành động này.'],401);
        }
        return $next($request);
    }
}
