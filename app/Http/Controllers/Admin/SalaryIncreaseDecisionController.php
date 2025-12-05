<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryIncreaseDecision;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SalaryIncreaseDecisionController extends Controller
{
    /**
     * Hiển thị danh sách quyết định nâng lương
     */
    public function index(Request $request)
    {
        $query = SalaryIncreaseDecision::with('teacher');

        // Tìm kiếm theo mã quyết định
        if ($request->filled('search_id')) {
            $query->where('decisionid', $request->search_id);
        }

        // Tìm kiếm theo giáo viên
        if ($request->filled('search_teacherid')) {
            $query->where('teacherid', $request->search_teacherid);
        }

        // Tìm kiếm theo ngày ký quyết định
        if ($request->filled('search_decisiondate')) {
            $query->whereDate('decisiondate', $request->search_decisiondate);
        }

        // Tìm kiếm theo ngày áp dụng
        if ($request->filled('search_applydate')) {
            $query->whereDate('applydate', $request->search_applydate);
        }

        $decisions = $query->orderBy('decisionid', 'desc')->get();

        // Lấy danh sách giáo viên cho dropdown
        $allTeachers = Teacher::orderBy('fullname', 'asc')->get();

        return view('admin.salaryincreasedecisions.index', compact('decisions', 'allTeachers'));
    }

    /**
     * Lưu quyết định nâng lương mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacherid' => 'required|integer|exists:teacher,teacherid',
            'decisiondate' => 'required|date',
            'newcoefficient' => [
                'required',
                'numeric',
                'min:0',
                'max:9999.9999',
                function ($attribute, $value, $fail) use ($request) {
                    // Lấy hệ số hiện tại của giáo viên
                    $teacher = Teacher::find($request->teacherid);
                    if ($teacher && $teacher->currentcoefficient) {
                        if ($value <= $teacher->currentcoefficient) {
                            $fail('Hệ số mới phải lớn hơn hệ số hiện tại (' . number_format($teacher->currentcoefficient, 4) . ')');
                        }
                    }
                },
            ],
            'applydate' => [
                'required',
                'date',
                'after_or_equal:decisiondate',
            ],
            'note' => 'nullable|string|max:65535',
        ], [
            'teacherid.required' => 'Vui lòng chọn giáo viên.',
            'teacherid.exists' => 'Giáo viên không tồn tại.',
            'decisiondate.required' => 'Vui lòng nhập ngày ký quyết định.',
            'decisiondate.date' => 'Ngày ký quyết định không hợp lệ.',
            'newcoefficient.required' => 'Vui lòng nhập hệ số mới.',
            'newcoefficient.numeric' => 'Hệ số mới phải là số.',
            'newcoefficient.min' => 'Hệ số mới phải lớn hơn 0.',
            'newcoefficient.max' => 'Hệ số mới không được vượt quá 9999.9999.',
            'applydate.required' => 'Vui lòng nhập ngày áp dụng.',
            'applydate.date' => 'Ngày áp dụng không hợp lệ.',
            'applydate.after_or_equal' => 'Ngày áp dụng phải sau hoặc bằng ngày ký quyết định.',
            'note.max' => 'Ghi chú không được vượt quá 65535 ký tự.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Lấy giáo viên và hệ số hiện tại
            $teacher = Teacher::findOrFail($request->teacherid);
            $oldCoefficient = $teacher->currentcoefficient ?? 0;

            // Tạo quyết định nâng lương
            $decision = SalaryIncreaseDecision::create([
                'teacherid' => $request->teacherid,
                'decisiondate' => $request->decisiondate,
                'oldcoefficient' => $oldCoefficient,
                'newcoefficient' => $request->newcoefficient,
                'applydate' => $request->applydate,
                'note' => $request->note,
            ]);

            // Cập nhật hệ số hiện tại của giáo viên
            $teacher->currentcoefficient = $request->newcoefficient;
            $teacher->save();

            // Cập nhật lịch sử hệ số (nếu có)
            if (method_exists($teacher, 'addCoefficientHistory')) {
                $teacher->addCoefficientHistory(
                    $request->newcoefficient,
                    $request->applydate,
                    null,
                    $request->note
                );
            }

            DB::commit();

            return redirect()->route('admin.salaryincreasedecision.index')
                ->with('success', 'Tạo quyết định nâng lương thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra khi tạo quyết định: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Cập nhật quyết định nâng lương
     */
    public function update(Request $request, SalaryIncreaseDecision $salaryincreasedecision)
    {
        $validator = Validator::make($request->all(), [
            'teacherid' => 'required|integer|exists:teacher,teacherid',
            'decisiondate' => 'required|date',
            'oldcoefficient' => 'required|numeric|min:0|max:9999.9999',
            'newcoefficient' => [
                'required',
                'numeric',
                'min:0',
                'max:9999.9999',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value <= $request->oldcoefficient) {
                        $fail('Hệ số mới phải lớn hơn hệ số cũ.');
                    }
                },
            ],
            'applydate' => [
                'required',
                'date',
                'after_or_equal:decisiondate',
            ],
            'note' => 'nullable|string|max:65535',
        ], [
            'teacherid.required' => 'Vui lòng chọn giáo viên.',
            'teacherid.exists' => 'Giáo viên không tồn tại.',
            'decisiondate.required' => 'Vui lòng nhập ngày ký quyết định.',
            'decisiondate.date' => 'Ngày ký quyết định không hợp lệ.',
            'oldcoefficient.required' => 'Vui lòng nhập hệ số cũ.',
            'oldcoefficient.numeric' => 'Hệ số cũ phải là số.',
            'oldcoefficient.min' => 'Hệ số cũ phải lớn hơn 0.',
            'oldcoefficient.max' => 'Hệ số cũ không được vượt quá 9999.9999.',
            'newcoefficient.required' => 'Vui lòng nhập hệ số mới.',
            'newcoefficient.numeric' => 'Hệ số mới phải là số.',
            'newcoefficient.min' => 'Hệ số mới phải lớn hơn 0.',
            'newcoefficient.max' => 'Hệ số mới không được vượt quá 9999.9999.',
            'applydate.required' => 'Vui lòng nhập ngày áp dụng.',
            'applydate.date' => 'Ngày áp dụng không hợp lệ.',
            'applydate.after_or_equal' => 'Ngày áp dụng phải sau hoặc bằng ngày ký quyết định.',
            'note.max' => 'Ghi chú không được vượt quá 65535 ký tự.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Cập nhật quyết định
            $salaryincreasedecision->update([
                'teacherid' => $request->teacherid,
                'decisiondate' => $request->decisiondate,
                'oldcoefficient' => $request->oldcoefficient,
                'newcoefficient' => $request->newcoefficient,
                'applydate' => $request->applydate,
                'note' => $request->note,
            ]);

            // Cập nhật hệ số hiện tại của giáo viên nếu đây là quyết định mới nhất
            $teacher = Teacher::findOrFail($request->teacherid);
            $latestDecision = SalaryIncreaseDecision::where('teacherid', $request->teacherid)
                ->orderBy('applydate', 'desc')
                ->orderBy('decisionid', 'desc')
                ->first();

            if ($latestDecision && $latestDecision->decisionid == $salaryincreasedecision->decisionid) {
                $teacher->currentcoefficient = $request->newcoefficient;
                $teacher->save();
            }

            DB::commit();

            return redirect()->route('admin.salaryincreasedecision.index')
                ->with('success', 'Cập nhật quyết định nâng lương thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra khi cập nhật quyết định: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Xóa quyết định nâng lương
     */
    public function destroy(SalaryIncreaseDecision $salaryincreasedecision)
    {
        try {
            DB::beginTransaction();

            $teacherId = $salaryincreasedecision->teacherid;
            $decisionId = $salaryincreasedecision->decisionid;

            // Xóa quyết định
            $salaryincreasedecision->delete();

            // Cập nhật lại hệ số hiện tại của giáo viên từ quyết định mới nhất còn lại
            $latestDecision = SalaryIncreaseDecision::where('teacherid', $teacherId)
                ->orderBy('applydate', 'desc')
                ->orderBy('decisionid', 'desc')
                ->first();

            $teacher = Teacher::findOrFail($teacherId);
            if ($latestDecision) {
                $teacher->currentcoefficient = $latestDecision->newcoefficient;
            } else {
                // Nếu không còn quyết định nào, giữ nguyên hệ số hiện tại hoặc set về 0
                // (có thể cần logic khác tùy yêu cầu)
            }
            $teacher->save();

            DB::commit();

            return redirect()->route('admin.salaryincreasedecision.index')
                ->with('success', 'Xóa quyết định nâng lương thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra khi xóa quyết định: ' . $e->getMessage()]);
        }
    }

    /**
     * Lấy hệ số hiện tại của giáo viên (API)
     */
    public function getCurrentCoefficient($teacherId)
    {
        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            return response()->json(['error' => 'Giáo viên không tồn tại'], 404);
        }

        return response()->json([
            'success' => true,
            'currentcoefficient' => $teacher->currentcoefficient ?? 0,
        ]);
    }
}

