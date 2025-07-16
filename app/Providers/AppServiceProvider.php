<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Thesis;
use App\Models\ReservedThesisTitle;
use App\Observers\ThesisObserver;
use App\Observers\ReservedThesisTitleObserver;

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
        // تسجيل observers لمسح الكاش
        Thesis::observe(ThesisObserver::class);
        ReservedThesisTitle::observe(ReservedThesisTitleObserver::class);
    }
}
