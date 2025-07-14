<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OptimizeResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // ضغط JSON للـ API responses
        if ($response instanceof JsonResponse) {
            $response->header('Content-Encoding', 'gzip');
            $response->header('Vary', 'Accept-Encoding');
            $response->header('Cache-Control', 'public, max-age=300'); // 5 دقائق
            
            // ضغط المحتوى
            $content = $response->getContent();
            if (strlen($content) > 1024) { // ضغط فقط إذا كان المحتوى أكبر من 1KB
                $compressed = gzencode($content, 6); // مستوى ضغط متوسط
                $response->setContent($compressed);
            }
        }
        
        return $response;
    }
}