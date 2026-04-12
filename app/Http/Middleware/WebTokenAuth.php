<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WebTokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($token = $request->cookie('api_token')) {
            $request->headers->set('Authorization', 'Bearer ' . $token);
            if (Auth::guard('api')->check()) {
                Auth::shouldUse('api');
            }
        }

        return $next($request);
    }
}
