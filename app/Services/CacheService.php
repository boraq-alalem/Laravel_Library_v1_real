<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public static function remember($key, $ttl, $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }
    
    public static function rememberForever($key, $callback)
    {
        return Cache::rememberForever($key, $callback);
    }
    
    public static function tags($tags)
    {
        return Cache::tags($tags);
    }
    
    public static function flush($tags = null)
    {
        return $tags ? Cache::tags($tags)->flush() : Cache::flush();
    }
}