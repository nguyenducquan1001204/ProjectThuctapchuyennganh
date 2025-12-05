<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\TeacherPayrollComponent;
use App\Models\PayrollComponent;

class TeacherPayrollComponentController extends Controller
{
    /**
     * Hiển thị danh sách thành phần lương của giáo viên hiện tại
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

        $query = TeacherPayrollComponent::with('component')
            ->where('teacherid', $teacher->teacherid);

        // Tìm kiếm theo mã cấu hình
        if ($request->filled('search_id')) {
            $query->where('teachercomponentid', $request->search_id);
        }

        // Tìm kiếm theo thành phần lương
        if ($request->filled('search_componentid')) {
            $query->where('componentid', $request->search_componentid);
        }

        // Tìm kiếm theo ngày hiệu lực
        if ($request->filled('search_effectivedate')) {
            $query->whereDate('effectivedate', $request->search_effectivedate);
        }

        // Tìm kiếm theo trạng thái (đang hiệu lực / đã hết hạn)
        if ($request->filled('search_status')) {
            if ($request->search_status === 'active') {
                $query->whereNull('expirationdate');
            } elseif ($request->search_status === 'expired') {
                $query->whereNotNull('expirationdate')
                      ->where('expirationdate', '<', now());
            }
        }

        $configs = $query->orderBy('effectivedate', 'desc')->get();

        // Lấy danh sách thành phần lương cho dropdown search
        $allComponents = PayrollComponent::orderBy('componentname', 'asc')->get();

        return view('teacher.teacherpayrollcomponents.index', compact('configs', 'teacher', 'allComponents'));
    }
}

