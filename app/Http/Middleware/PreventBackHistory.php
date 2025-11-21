<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventBackHistory
{
    /**
     * Đặt header để trình duyệt không cache, tránh quay lại trang sau khi logout.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        return $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
}


