<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\PerformanceMonitor;

class PerformanceMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        $response = $next($request);
        
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        $memoryUsage = memory_get_usage() - $startMemory;
        
        // إضافة headers للأداء
        $response->headers->set('X-Response-Time', $executionTime . 'ms');
        $response->headers->set('X-Memory-Usage', round($memoryUsage / 1024, 2) . 'KB');
        
        // تسجيل الطلبات البطيئة
        if ($executionTime > 1000) {
            \Log::warning('Slow Request', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'time' => $executionTime,
                'memory' => $memoryUsage
            ]);
        }
        
        return $response;
    }
}