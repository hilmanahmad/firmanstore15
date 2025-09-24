<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('api-chakra-key') != env('API_CHAKRA_KEY')) {
            return response()->json([
                'status' => false,
                'message' => 'Key tidak valid!'
            ], 401);
        }
        return $next($request);
    }
}
