<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // تفعيل مراقبة الاستعلامات البطيئة
        \App\Services\PerformanceMonitor::trackSlowQueries();
        
        // تحميل الكاش المسبق
        if (app()->environment('production')) {
            \App\Services\AdvancedCacheService::warmCache();
        }
    }
}
