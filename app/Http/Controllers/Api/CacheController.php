<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheController extends Controller
{
    /**
     * مسح الكاش يدوياً
     */
    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');
        
        switch ($type) {
            case 'theses':
                $this->clearThesisCache();
                return response()->json(['message' => 'تم مسح كاش الرسائل بنجاح']);
                
            case 'reserved_titles':
                $this->clearReservedTitlesCache();
                return response()->json(['message' => 'تم مسح كاش العناوين المحجوزة بنجاح']);
                
            case 'all':
            default:
                $this->clearThesisCache();
                $this->clearReservedTitlesCache();
                return response()->json(['message' => 'تم مسح جميع الكاش بنجاح']);
        }
    }
    
    /**
     * مسح كاش الرسائل
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
    
    /**
     * مسح كاش العناوين المحجوزة
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