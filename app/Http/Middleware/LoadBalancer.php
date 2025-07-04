<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LoadBalancer
{
    public function handle(Request $request, Closure $next)
    {
        // تحديد حد الطلبات المتزامنة
        $currentLoad = $this->getCurrentLoad();
        
        if ($currentLoad > 200) {
            return response()->json([
                'message' => 'Server busy, try again later',
                'retry_after' => 2
            ], 503)->header('Retry-After', 2);
        }
        
        return $next($request);
    }
    
    private function getCurrentLoad()
    {
        // محاكاة قياس الحمل
        return rand(50, 250);
    }
}