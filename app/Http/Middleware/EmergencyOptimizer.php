<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmergencyOptimizer
{
    public function handle(Request $request, Closure $next)
    {
        // تعطيل جميع العمليات غير الضرورية
        DB::disableQueryLog();
        config(['app.debug' => false]);
        
        // تحسين الذاكرة
        ini_set('memory_limit', '512M');
        
        // تحسين اتصال قاعدة البيانات
        DB::statement('SET SESSION query_cache_type = ON');
        DB::statement('SET SESSION query_cache_size = 67108864');
        
        return $next($request);
    }
}