<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PerformanceMonitor
{
    public static function trackSlowQueries()
    {
        DB::listen(function ($query) {
            if ($query->time > 1000) { // أكثر من ثانية
                Log::warning('Slow Query Detected', [
                    'sql' => $query->sql,
                    'time' => $query->time,
                    'bindings' => $query->bindings
                ]);
            }
        });
    }
    
    public static function memoryUsage()
    {
        return [
            'current' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'limit' => ini_get('memory_limit')
        ];
    }
    
    public static function serverLoad()
    {
        return sys_getloadavg();
    }
}