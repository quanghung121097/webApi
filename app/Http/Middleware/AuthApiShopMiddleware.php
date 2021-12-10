<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthApiShopMiddleware
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
        return $next($request);
        // if ($request->header('username') == env('USER_NAME_API','quanghung') && $request->header('password') == env('PASSWORD_API','jubcsvd734934556@!JJhka')) {
        //     return $next($request);
        // }
        // return response(['success' =>false , 'message' => 'Ứng dụng không có quyền truy cập hệ thống'],403);
       
    }
}
