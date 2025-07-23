<?php

namespace App\Events;

use App\Models\ReservedThesisTitle;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ReservedThesisTitleDeleted
{
    use Dispatchable, SerializesModels;

    public $title;

    /**
     * Create a new event instance.
     */
    public function __construct(ReservedThesisTitle $title)
    {
        $this->title = $title;
        
        // مسح الكاش المتعلق بالعناوين المحجوزة
        Cache::forget('latest_reserved_1_14');
        Cache::forget('latest_reserved_guests_1_14');
        
        // مسح كاش البحث
        $keys = Cache::get('reserved_search_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}