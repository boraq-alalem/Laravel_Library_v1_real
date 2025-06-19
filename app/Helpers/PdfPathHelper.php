<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

class PdfPathHelper
{
    // تشفير المسار الحقيقي لمسار PDF
    public static function encryptPath($realPath)
    {
        return urlencode(base64_encode(Crypt::encryptString($realPath)));
    }

    // فك تشفير المسار المشفر
    public static function decryptPath($encrypted)
    {
        try {
            $realPath = Crypt::decryptString(base64_decode(urldecode($encrypted)));
            return $realPath;
        } catch (\Exception $e) {
            return null;
        }
    }
}
