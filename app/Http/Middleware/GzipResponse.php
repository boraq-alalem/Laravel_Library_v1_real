<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GzipResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if ($this->shouldCompress($request, $response)) {
            $content = $response->getContent();
            $compressed = gzencode($content, 6);
            
            $response->setContent($compressed);
            $response->headers->set('Content-Encoding', 'gzip');
            $response->headers->set('Content-Length', strlen($compressed));
        }
        
        return $response;
    }
    
    private function shouldCompress($request, $response)
    {
        return strlen($response->getContent()) > 1024 && 
               strpos($request->header('Accept-Encoding', ''), 'gzip') !== false;
    }
}