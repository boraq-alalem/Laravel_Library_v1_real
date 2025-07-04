<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceBooster
{
    public function handle(Request $request, Closure $next)
    {
        // تعطيل query log في الإنتاج
        DB::disableQueryLog();
        
        // تحسين memory limit
        ini_set('memory_limit', '256M');
        
        // تحسين execution time
        set_time_limit(30);
        
        $response = $next($request);
        
        // إضافة headers للأداء
        $response->headers->set('X-Powered-By', 'Laravel-Optimized');
        $response->headers->set('Server-Timing', 'total;dur=' . (microtime(true) - LARAVEL_START) * 1000);
        
        return $response;
    }
}