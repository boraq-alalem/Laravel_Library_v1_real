<?php

namespace App\Observers;

use App\Models\ReservedThesisTitle;
use Illuminate\Support\Facades\Cache;

class ReservedThesisTitleObserver
{
    /**
     * مسح الكاش عند إنشاء عنوان محجوز جديد
     */
    public function created(ReservedThesisTitle $title)
    {
        $this->clearReservedTitlesCache();
    }

    /**
     * مسح الكاش عند تحديث عنوان محجوز
     */
    public function updated(ReservedThesisTitle $title)
    {
        $this->clearReservedTitlesCache();
    }

    /**
     * مسح الكاش عند حذف عنوان محجوز
     */
    public function deleted(ReservedThesisTitle $title)
    {
        $this->clearReservedTitlesCache();
    }

    /**
     * مسح الكاش المتعلق بالعناوين المحجوزة
     */
    private function clearReservedTitlesCache()
    {
        // مسح كاش العناوين المحجوزة
        Cache::forget('latest_reserved_1_14');
        Cache::forget('latest_reserved_guests_1_14');
        
        // مسح كاش البحث
        $keys = Cache::get('reserved_search_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}