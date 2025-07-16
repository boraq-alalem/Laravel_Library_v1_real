<?php

namespace App\Events;

use App\Models\Thesis;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ThesisCreated
{
    use Dispatchable, SerializesModels;

    public $thesis;

    /**
     * Create a new event instance.
     */
    public function __construct(Thesis $thesis)
    {
        $this->thesis = $thesis;
        
        // مسح الكاش المتعلق بالرسائل
        Cache::forget('latest_theses_1_14');
        Cache::forget('stats_index');
        
        // مسح كاش البحث
        $keys = Cache::get('thesis_search_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}