<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckRole
{
    /**
     * Kiểm tra vai trò người dùng cho từng khu vực (admin/accounting/teacher)
     */
    public function handle(Request $request, Closure $next, string $area)
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            return redirect()->route('login')
                ->withErrors(['username' => 'Bạn cần đăng nhập để truy cập hệ thống.']);
        }

        $roleName = Str::lower($user->role->rolename);

        $allowed = match ($area) {
            'admin'      => ['quản trị viên', 'quản trị', 'admin'],
            'accounting' => ['kế toán'],
            'teacher'    => ['giáo viên'],
            default      => [],
        };

        $ok = false;
        foreach ($allowed as $keyword) {
            if (Str::contains($roleName, $keyword)) {
                $ok = true;
                break;
            }
        }

        if (!$ok) {
            return redirect()->route('login')
                ->withErrors(['username' => 'Bạn không có quyền truy cập khu vực này.']);
        }

        return $next($request);
    }
}


