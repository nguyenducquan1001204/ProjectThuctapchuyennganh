<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherJobTitleHistory;
use App\Models\Teacher;
use App\Models\JobTitle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TeacherJobTitleHistoryController extends Controller
{
    /**
     * Hiển thị danh sách lịch sử chức danh
     */
    public function index(Request $request)
    {
        $query = TeacherJobTitleHistory::with(['teacher', 'jobTitle']);
        
        // Tìm kiếm theo ID
        if ($request->filled('search_id')) {
            $query->where('historyid', $request->search_id);
        }
        
        // Tìm kiếm theo giáo viên
        if ($request->filled('search_teacher')) {
            $query->whereHas('teacher', function($q) use ($request) {
                $q->where('fullname', 'like', '%' . $request->search_teacher . '%');
            });
        }
        
        // Tìm kiếm theo chức danh
        if ($request->filled('search_jobtitle')) {
            $query->whereHas('jobTitle', function($q) use ($request) {
                $q->where('jobtitlename', 'like', '%' . $request->search_jobtitle . '%');
            });
        }
        
        // Tìm kiếm theo ngày có hiệu lực
        if ($request->filled('search_effectivedate')) {
            $query->whereDate('effectivedate', $request->search_effectivedate);
        }
        
        $histories = $query->orderBy('historyid', 'desc')->get();
        
        return view('admin.teacherjobtitlehistories.index', compact('histories'));
    }

    /**
     * Validation rules cho lịch sử chức danh
     */
    private function getValidationRules($ignoreId = null): array
    {
        return [
            'teacherid' => [
                'required',
                'integer',
                'exists:teacher,teacherid',
            ],
            'jobtitleid' => [
                'required',
                'integer',
                'exists:jobtitle,jobtitleid',
            ],
            'effectivedate' => [
                'required',
                'date',
            ],
            'expiredate' => [
                'nullable',
                'date',
                'after:effectivedate', // Ngày kết thúc phải sau ngày bắt đầu
            ],
            'note' => [
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * Validation messages
     */
    private function getValidationMessages(): array
    {
        return [
            'teacherid.required' => 'Giáo viên là bắt buộc',
            'teacherid.integer' => 'Giáo viên không hợp lệ',
            'teacherid.exists' => 'Giáo viên không tồn tại',
            'jobtitleid.required' => 'Chức danh là bắt buộc',
            'jobtitleid.integer' => 'Chức danh không hợp lệ',
            'jobtitleid.exists' => 'Chức danh không tồn tại',
            'effectivedate.required' => 'Ngày có hiệu lực là bắt buộc',
            'effectivedate.date' => 'Ngày có hiệu lực không hợp lệ',
            'expiredate.date' => 'Ngày kết thúc không hợp lệ',
            'expiredate.after' => 'Ngày kết thúc phải sau ngày có hiệu lực',
            'note.string' => 'Ghi chú không hợp lệ',
        ];
    }

    /**
     * Lưu lịch sử chức danh mới
     */
    public function store(Request $request)
    {
        $request->merge([
            'teacherid' => (int)$request->teacherid,
            'jobtitleid' => (int)$request->jobtitleid,
            'effectivedate' => $request->effectivedate ? $request->effectivedate : null,
            'expiredate' => $request->expiredate ? $request->expiredate : null,
            'note' => $request->note ? trim($request->note) : null,
        ]);

        $validator = Validator::make($request->all(), $this->getValidationRules(), $this->getValidationMessages());

        $validated = $validator->validate();

        TeacherJobTitleHistory::create($validated);

        return redirect()->route('admin.teacherjobtitlehistory.index')
            ->with('success', 'Thêm lịch sử chức danh thành công!');
    }

    /**
     * Cập nhật lịch sử chức danh
     */
    public function update(Request $request, $id)
    {
        $history = TeacherJobTitleHistory::findOrFail($id);

        $request->merge([
            'teacherid' => (int)$request->teacherid,
            'jobtitleid' => (int)$request->jobtitleid,
            'effectivedate' => $request->effectivedate ? $request->effectivedate : null,
            'expiredate' => $request->expiredate ? $request->expiredate : null,
            'note' => $request->note ? trim($request->note) : null,
        ]);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($history->historyid),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        $history->update($validated);

        return redirect()->route('admin.teacherjobtitlehistory.index')
            ->with('success', 'Cập nhật lịch sử chức danh thành công!');
    }

    /**
     * Xóa lịch sử chức danh
     */
    public function destroy($id)
    {
        $history = TeacherJobTitleHistory::findOrFail($id);
        
        $history->delete();

        return redirect()->route('admin.teacherjobtitlehistory.index')
            ->with('success', 'Xóa lịch sử chức danh thành công!');
    }
}

