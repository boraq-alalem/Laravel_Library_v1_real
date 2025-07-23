<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class PdfPathHelper
{
    // تشفير سريع باستخدام hash قصير
    public static function encryptPath($realPath)
    {
        // إنشاء hash قصير ثابت من المسار
        $hash = substr(md5($realPath), 0, 6); // 6 خانات فقط
        $shortId = strtolower(base_convert($hash, 16, 36)); // تحويل لـ base36
        
        // حفظ في الكاش لمدة يوم كامل
        Cache::put('pdf_' . $shortId, $realPath, 86400);
        
        return $shortId;
    }

    // فك التشفير السريع
    public static function decryptPath($encrypted)
    {
        $path = Cache::get('pdf_' . $encrypted);
        
        // إذا لم يوجد في الكاش، حاول إعادة إنشاءه من قاعدة البيانات
        if (!$path) {
            $thesis = \App\Models\Thesis::whereRaw('SUBSTRING(MD5(pdf_path), 1, 6) = ?', [strtoupper($encrypted)])->first();
            if ($thesis && $thesis->pdf_path) {
                Cache::put('pdf_' . $encrypted, $thesis->pdf_path, 86400);
                return $thesis->pdf_path;
            }
        }
        
        return $path;
    }
    
    // تنظيف الكاش القديم (اختياري)
    public static function clearOldCache()
    {
        // يمكن استدعاؤها دورياً لتنظيف الكاش
    }
}
