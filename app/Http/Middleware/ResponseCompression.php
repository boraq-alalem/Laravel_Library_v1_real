<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ResponseCompression
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if ($response->headers->get('Content-Type') === 'application/json') {
            $content = $response->getContent();
            
            // ضغط JSON
            if (function_exists('gzencode') && strlen($content) > 1024) {
                $compressed = gzencode($content, 6);
                if ($compressed !== false && strlen($compressed) < strlen($content)) {
                    $response->setContent($compressed);
                    $response->headers->set('Content-Encoding', 'gzip');
                    $response->headers->set('Content-Length', strlen($compressed));
                }
            }
        }
        
        return $response;
    }
}