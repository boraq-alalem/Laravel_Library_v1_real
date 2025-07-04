<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HighPerformance
{
    public function handle(Request $request, Closure $next)
    {
        // كاش طويل المدى للـ APIs الثقيلة
        if ($request->is('api/theses/latest')) {
            $cached = Cache::get('hp_latest');
            if ($cached) {
                return response()->json($cached)->header('X-Cache', 'HP-HIT');
            }
        }
        
        $response = $next($request);
        
        // حفظ لمدة 15 دقيقة
        if ($request->is('api/theses/latest') && $response->isSuccessful()) {
            Cache::put('hp_latest', $response->getData(), 900);
        }
        
        return $response;
    }
}