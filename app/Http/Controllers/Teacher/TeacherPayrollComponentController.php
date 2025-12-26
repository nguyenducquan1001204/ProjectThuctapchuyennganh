<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\TeacherPayrollComponent;
use App\Models\PayrollComponent;
use Illuminate\Support\Facades\Auth;

class TeacherPayrollComponentController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        
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

        if ($request->filled('search_id')) {
            $query->where('teachercomponentid', $request->search_id);
        }

        if ($request->filled('search_componentid')) {
            $query->where('componentid', $request->search_componentid);
        }

        if ($request->filled('search_effectivedate')) {
            $query->whereDate('effectivedate', $request->search_effectivedate);
        }

        if ($request->filled('search_status')) {
            if ($request->search_status === 'active') {
                $query->whereNull('expirationdate');
            } elseif ($request->search_status === 'expired') {
                $query->whereNotNull('expirationdate')
                      ->where('expirationdate', '<', now());
            }
        }

        $configs = $query->orderBy('effectivedate', 'desc')->get();

        $allComponents = PayrollComponent::orderBy('componentname', 'asc')->get();

        return view('teacher.teacherpayrollcomponents.index', compact('configs', 'teacher', 'allComponents'));
    }
}

