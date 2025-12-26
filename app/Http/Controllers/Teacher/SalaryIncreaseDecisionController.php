<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\SalaryIncreaseDecision;
use Illuminate\Support\Facades\Auth;

class SalaryIncreaseDecisionController extends Controller
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

        $query = SalaryIncreaseDecision::where('teacherid', $teacher->teacherid);

        if ($request->filled('search_id')) {
            $query->where('decisionid', $request->search_id);
        }

        if ($request->filled('search_decisiondate')) {
            $query->whereDate('decisiondate', $request->search_decisiondate);
        }

        if ($request->filled('search_applydate')) {
            $query->whereDate('applydate', $request->search_applydate);
        }

        $decisions = $query->orderBy('decisiondate', 'desc')->get();

        return view('teacher.salaryincreasedecisions.index', compact('decisions', 'teacher'));
    }
}

