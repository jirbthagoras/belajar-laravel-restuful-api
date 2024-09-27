<?php

namespace App\Http\Middleware;

use Closure;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header("Authorization");

        $authenticate = true;

        if(!$token){
            $authenticate = false;
        }

        $user = \App\Models\User::query()->where('token', $token)->first();

        if(!$user){
            $authenticate = false;
        } else {

            Auth::login($user);

        }

        if($authenticate){
            return $next($request);
        } else {
            return response()->json([
                "errors" => [
                    "message" => ["Unauthorized."]
                ]
            ])->setStatusCode(401);
        }
    }
}
