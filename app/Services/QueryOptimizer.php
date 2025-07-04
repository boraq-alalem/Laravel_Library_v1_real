<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class QueryOptimizer
{
    public static function optimizedPaginate($query, $perPage = 15, $cacheKey = null)
    {
        if ($cacheKey) {
            return Cache::remember($cacheKey, 180, fn() => $query->paginate($perPage));
        }
        return $query->paginate($perPage);
    }
    
    public static function batchSelect($model, $ids, $columns = ['*'])
    {
        return $model::whereIn('id', $ids)->select($columns)->get()->keyBy('id');
    }
    
    public static function countWithCache($query, $cacheKey, $ttl = 300)
    {
        return Cache::remember($cacheKey, $ttl, fn() => $query->count());
    }
}