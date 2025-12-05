<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\TeacherPayrollComponent;
use App\Models\SalaryIncreaseDecision;
use App\Models\PayrollRunDetail;

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
            return redirect()->route('accounting.dashboard')
                ->with('error', 'Tài khoản của bạn chưa được liên kết với giáo viên. Vui lòng liên hệ quản trị viên.');
        }

        $teacher = Teacher::with(['jobTitle', 'unit'])->find($user->teacherid);
        
        if (!$teacher) {
            return redirect()->route('accounting.dashboard')
                ->with('error', 'Không tìm thấy thông tin giáo viên.');
        }

        // Lấy thông tin từ các bảng liên quan
        $teacherPayrollComponents = TeacherPayrollComponent::with('component')
            ->where('teacherid', $teacher->teacherid)
            ->orderBy('effectivedate', 'desc')
            ->get();

        $salaryIncreaseDecisions = SalaryIncreaseDecision::where('teacherid', $teacher->teacherid)
            ->orderBy('decisiondate', 'desc')
            ->get();

        $payrollRunDetails = PayrollRunDetail::with(['payrollRun.unit', 'components.component'])
            ->where('teacherid', $teacher->teacherid)
            ->orderBy('payrollrunid', 'desc')
            ->get();

        return view('accounting.myinformation.index', compact(
            'teacher',
            'teacherPayrollComponents',
            'salaryIncreaseDecisions',
            'payrollRunDetails'
        ));
    }
}

