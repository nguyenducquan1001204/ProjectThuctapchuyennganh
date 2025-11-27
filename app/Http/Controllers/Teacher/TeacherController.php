<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;

class TeacherController extends Controller
{
    /**
     * Trang chủ khu vực Giáo viên
     */
    public function index()
    {
        return view('teacher.dashboard');
    }
}


