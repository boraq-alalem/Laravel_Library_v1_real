<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SuperFast
{
    public function handle(Request $request, Closure $next)
    {
        // كاش فوري للـ APIs الأساسية
        $cacheKey = 'superfast:' . md5($request->fullUrl());
        
        if ($request->isMethod('GET')) {
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return response($cached['content'])
                    ->header('Content-Type', 'application/json')
                    ->header('X-Cache', 'SUPERFAST-HIT');
            }
        }
        
        $response = $next($request);
        
        // حفظ في الكاش لمدة دقيقتين
        if ($request->isMethod('GET') && $response->isSuccessful()) {
            Cache::put($cacheKey, [
                'content' => $response->getContent()
            ], 120);
        }
        
        return $response;
    }
}