<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class PelangganMiddleware
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
        $user = JWTAuth::parseToken()->authenticate();
        if($user->role != 'pelanggan'){
            return response()->json([
                'message' => 'Login Sebagai Pelanggan Untuk Mengakses'
            ], 401);
        }
        return $next($request);
    }
}
