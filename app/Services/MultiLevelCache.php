<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class MultiLevelCache
{
    // كاش L1: في الذاكرة (سريع جداً)
    private static $memoryCache = [];
    
    public static function remember($key, $ttl, $callback)
    {
        // L1: فحص الذاكرة
        if (isset(self::$memoryCache[$key])) {
            return self::$memoryCache[$key];
        }
        
        // L2: فحص Redis
        $result = Cache::remember($key, $ttl, $callback);
        
        // حفظ في L1
        self::$memoryCache[$key] = $result;
        
        return $result;
    }
    
    public static function forget($key)
    {
        unset(self::$memoryCache[$key]);
        Cache::forget($key);
    }
}