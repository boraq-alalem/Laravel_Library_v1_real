<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HighLoadOptimizer
{
    public function handle(Request $request, Closure $next)
    {
        // تحسين headers للحمولة العالية
        $response = $next($request);
        
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('Keep-Alive', 'timeout=30, max=1000');
        $response->headers->set('Cache-Control', 'public, max-age=3600');
        
        return $response;
    }
}