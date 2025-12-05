<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\SalaryIncreaseDecision;

class SalaryIncreaseDecisionController extends Controller
{
    /**
     * Hiển thị danh sách quyết định nâng lương của giáo viên hiện tại
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Kiểm tra xem user có liên kết với Teacher không
        if (!$user || !$user->teacherid) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Tài khoản của bạn chưa được liên kết với giáo viên. Vui lòng liên hệ quản trị viên.');
        }

        $teacher = Teacher::find($user->teacherid);
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Không tìm thấy thông tin giáo viên.');
        }

        $query = SalaryIncreaseDecision::where('teacherid', $teacher->teacherid);

        // Tìm kiếm theo mã quyết định
        if ($request->filled('search_id')) {
            $query->where('decisionid', $request->search_id);
        }

        // Tìm kiếm theo ngày ký quyết định
        if ($request->filled('search_decisiondate')) {
            $query->whereDate('decisiondate', $request->search_decisiondate);
        }

        // Tìm kiếm theo ngày áp dụng
        if ($request->filled('search_applydate')) {
            $query->whereDate('applydate', $request->search_applydate);
        }

        $decisions = $query->orderBy('decisiondate', 'desc')->get();

        return view('teacher.salaryincreasedecisions.index', compact('decisions', 'teacher'));
    }
}

