<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UltraFast
{
    public function handle(Request $request, Closure $next)
    {
        // كاش فوري للـ API الرئيسي
        if ($request->is('api/theses/latest')) {
            $cached = Cache::get('ultra_fast_latest');
            if ($cached) {
                return response()->json($cached)->header('X-Cache', 'HIT');
            }
        }
        
        $response = $next($request);
        
        // حفظ في الكاش
        if ($request->is('api/theses/latest') && $response->isSuccessful()) {
            Cache::put('ultra_fast_latest', $response->getData(), 120);
        }
        
        return $response;
    }
}