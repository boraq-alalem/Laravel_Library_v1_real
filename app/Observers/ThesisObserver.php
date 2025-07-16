<?php

namespace App\Observers;

use App\Models\Thesis;
use Illuminate\Support\Facades\Cache;

class ThesisObserver
{
    /**
     * مسح الكاش عند إنشاء رسالة جديدة
     */
    public function created(Thesis $thesis)
    {
        $this->clearThesisCache();
    }

    /**
     * مسح الكاش عند تحديث رسالة
     */
    public function updated(Thesis $thesis)
    {
        $this->clearThesisCache();
    }

    /**
     * مسح الكاش عند حذف رسالة
     */
    public function deleted(Thesis $thesis)
    {
        $this->clearThesisCache();
    }

    /**
     * مسح الكاش المتعلق بالرسائل
     */
    private function clearThesisCache()
    {
        // مسح كاش الرسائل
        Cache::forget('latest_theses_1_14');
        Cache::forget('stats_index');
        
        // مسح كاش البحث
        $keys = Cache::get('thesis_search_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}