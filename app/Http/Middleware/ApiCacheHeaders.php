<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiCacheHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // إضافة cache headers للـ API
        $response->headers->set('Cache-Control', 'public, max-age=300');
        $response->headers->set('Vary', 'Accept, Accept-Encoding');
        $response->headers->set('ETag', md5($response->getContent()));
        
        return $response;
    }
}