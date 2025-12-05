<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;

class MyInformationController extends Controller
{
    /**
     * Hiển thị thông tin cá nhân của user hiện tại
     */
    public function index()
    {
        $user = auth()->user();
        
        // Kiểm tra xem user có liên kết với Teacher không
        if (!$user || !$user->teacherid) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Tài khoản của bạn chưa được liên kết với giáo viên. Vui lòng liên hệ quản trị viên.');
        }

        $teacher = Teacher::with(['jobTitle', 'unit'])->find($user->teacherid);
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Không tìm thấy thông tin giáo viên.');
        }

        return view('teacher.myinformation.index', compact('teacher'));
    }
}

