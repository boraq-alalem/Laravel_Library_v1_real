<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter as RateLimiterFacade;
use Symfony\Component\HttpFoundation\Response;

class RateLimiter
{
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $request->ip();
        
        if (RateLimiterFacade::tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'message' => 'Too many requests'
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }
        
        RateLimiterFacade::hit($key, $decayMinutes * 60);
        
        return $next($request);
    }
}