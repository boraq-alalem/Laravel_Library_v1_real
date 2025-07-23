<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class AdvancedCacheService
{
    // Cache Warming
    public static function warmCache()
    {
        $popularQueries = [
            'universities' => fn() => \App\Models\University::all(),
            'specializations' => fn() => \App\Models\Specialization::all(),
            'recent_theses' => fn() => \App\Models\Thesis::latest()->take(50)->get(),
        ];
        
        foreach ($popularQueries as $key => $callback) {
            Cache::remember($key, 3600, $callback);
        }
    }
    
    // Distributed Caching
    public static function distributedCache($key, $data, $ttl = 3600)
    {
        Redis::setex("dist:$key", $ttl, serialize($data));
    }
    
    // Cache Invalidation
    public static function smartInvalidate($model, $id)
    {
        $tags = [
            strtolower(class_basename($model)),
            strtolower(class_basename($model)) . ":$id"
        ];
        Cache::tags($tags)->flush();
    }
}