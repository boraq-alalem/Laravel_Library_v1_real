<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseOptimizer
{
    public function handle(Request $request, Closure $next)
    {
        DB::enableQueryLog();
        
        // تحسين إعدادات MySQL للجلسة
        DB::statement('SET SESSION query_cache_type = ON');
        DB::statement('SET SESSION tmp_table_size = 268435456');
        DB::statement('SET SESSION max_heap_table_size = 268435456');
        
        $response = $next($request);
        
        // تسجيل الاستعلامات البطيئة فقط
        $queries = DB::getQueryLog();
        foreach ($queries as $query) {
            if ($query['time'] > 100) { // أكثر من 100ms
                \Log::warning('Slow Query: ' . $query['query'] . ' - Time: ' . $query['time'] . 'ms');
            }
        }
        
        return $response;
    }
}