<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('api')->check()) {
            Auth::shouldUse('api'); // Switch default Guard so Gate uses it
            // Set the authenticated user on the request
            $request->setUserResolver(function () {
                return Auth::guard('api')->user();
            });

            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated or invalid token.',
        ], 401);
    }
}
