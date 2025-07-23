<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PerformanceMonitor
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsage = ($endMemory - $startMemory) / 1024;
        
        // إضافة headers للمراقبة
        $response->headers->set('X-Response-Time', round($executionTime, 2) . 'ms');
        $response->headers->set('X-Memory-Usage', round($memoryUsage, 2) . 'KB');
        $response->headers->set('X-Peak-Memory', round(memory_get_peak_usage() / 1024, 2) . 'KB');
        
        // تسجيل الطلبات البطيئة
        if ($executionTime > 500) {
            \Log::warning('Slow request', [
                'url' => $request->fullUrl(),
                'time' => $executionTime,
                'memory' => $memoryUsage
            ]);
        }
        
        return $response;
    }
}