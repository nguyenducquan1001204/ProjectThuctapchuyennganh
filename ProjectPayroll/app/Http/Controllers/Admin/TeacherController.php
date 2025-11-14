<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\JobTitle;
use App\Models\BudgetSpendingUnit;
use App\Models\TeacherJobTitleHistory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TeacherController extends Controller
{
    /**
     * Hiển thị danh sách giáo viên
     */
    public function index(Request $request)
    {
        $query = Teacher::with(['jobTitle', 'unit']);
        
        // Tìm kiếm theo ID
        if ($request->filled('search_id')) {
            $query->where('teacherid', $request->search_id);
        }
        
        // Tìm kiếm theo tên
        if ($request->filled('search_name')) {
            $query->where('fullname', 'like', '%' . $request->search_name . '%');
        }
        
        // Tìm kiếm theo chức danh
        if ($request->filled('search_jobtitle')) {
            $query->whereHas('jobTitle', function($q) use ($request) {
                $q->where('jobtitlename', 'like', '%' . $request->search_jobtitle . '%');
            });
        }
        
        // Tìm kiếm theo giới tính
        if ($request->filled('search_gender')) {
            $query->where('gender', $request->search_gender);
        }
        
        // Tìm kiếm theo trạng thái
        if ($request->filled('search_status')) {
            $query->where('status', $request->search_status);
        }
        
        $teachers = $query->orderBy('teacherid', 'asc')->get();
        
        // Lấy danh sách chức danh và đơn vị cho dropdown
        $jobTitles = JobTitle::orderBy('jobtitlename', 'asc')->get();
        $units = BudgetSpendingUnit::orderBy('unitname', 'asc')->get();
        
        return view('admin.teachers.index', compact('teachers', 'jobTitles', 'units'));
    }

    /**
     * Validation rules cho giáo viên
     */
    private function getValidationRules($ignoreId = null): array
    {
        return [
            'fullname' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[\p{L}\s]+$/u', // Chỉ cho phép chữ cái và khoảng trắng
                function ($attribute, $value, $fail) {
                    // Kiểm tra phải có ít nhất một khoảng trắng (có cả họ và tên)
                    if (trim($value) && !preg_match('/\s+/', trim($value))) {
                        $fail('Họ và tên phải bao gồm cả họ và tên (có khoảng trắng).');
                    }
                    // Kiểm tra không được có nhiều khoảng trắng liên tiếp
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('Họ và tên không được có nhiều khoảng trắng liên tiếp.');
                    }
                    // Kiểm tra mỗi từ phải có ít nhất 1 ký tự
                    $words = preg_split('/\s+/', trim($value));
                    foreach ($words as $word) {
                        if (mb_strlen($word) < 1) {
                            $fail('Mỗi từ trong họ và tên phải có ít nhất 1 ký tự.');
                            break;
                        }
                    }
                },
            ],
            'birthdate' => [
                'nullable',
                'date',
                'before:' . now()->subYears(22)->format('Y-m-d'), // Phải đủ 22 tuổi
            ],
            'gender' => [
                'nullable',
                Rule::in(['male', 'female', 'other']),
            ],
            'jobtitleid' => [
                'nullable',
                'integer',
                'exists:jobtitle,jobtitleid',
            ],
            'unitid' => [
                'nullable',
                'integer',
                'exists:budgetspendingunit,unitid',
            ],
            'startdate' => [
                'nullable',
                'date',
            ],
            'status' => [
                'required',
                Rule::in(['active', 'suspended', 'onleave', 'contractended']),
            ],
        ];
    }

    /**
     * Validation messages
     */
    private function getValidationMessages(): array
    {
        return [
            'fullname.required' => 'Họ và tên là bắt buộc',
            'fullname.min' => 'Họ và tên phải có ít nhất 3 ký tự',
            'fullname.max' => 'Họ và tên không được vượt quá 255 ký tự',
            'fullname.regex' => 'Họ và tên chỉ được chứa chữ cái và khoảng trắng',
            'birthdate.date' => 'Ngày sinh không hợp lệ',
            'birthdate.before' => 'Giáo viên phải đủ 22 tuổi mới được đăng ký',
            'gender.in' => 'Giới tính không hợp lệ',
            'jobtitleid.integer' => 'Chức danh không hợp lệ',
            'jobtitleid.exists' => 'Chức danh không tồn tại',
            'unitid.integer' => 'Đơn vị không hợp lệ',
            'unitid.exists' => 'Đơn vị không tồn tại',
            'startdate.date' => 'Ngày bắt đầu công tác không hợp lệ',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
        ];
    }

    /**
     * Lưu giáo viên mới
     */
    public function store(Request $request)
    {
        $request->merge([
            'fullname' => trim($request->fullname),
            'birthdate' => $request->birthdate ? $request->birthdate : null,
            'gender' => $request->gender ? $request->gender : null,
            'jobtitleid' => $request->jobtitleid ? (int)$request->jobtitleid : null,
            'unitid' => $request->unitid ? (int)$request->unitid : null,
            'startdate' => $request->startdate ? $request->startdate : null,
            'status' => $request->status ?? 'active',
        ]);

        $validator = Validator::make($request->all(), $this->getValidationRules(), $this->getValidationMessages());

        $validated = $validator->validate();

        $teacher = Teacher::create($validated);

        // Nếu có chức danh khi tạo mới, tự động ghi lịch sử
        if ($teacher->jobtitleid !== null) {
            $jobTitle = JobTitle::find($teacher->jobtitleid);
            $jobTitleName = $jobTitle ? $jobTitle->jobtitlename : '';
            
            TeacherJobTitleHistory::create([
                'teacherid' => $teacher->teacherid,
                'jobtitleid' => $teacher->jobtitleid,
                'effectivedate' => $teacher->startdate ?? Carbon::today(),
                'expiredate' => null, // Chưa kết thúc
                'note' => $jobTitleName ? "Chức danh đầu tiên: {$jobTitleName}" : 'Tự động ghi khi thêm giáo viên mới',
            ]);
        }

        return redirect()->route('admin.teacher.index')
            ->with('success', 'Thêm giáo viên thành công!');
    }

    /**
     * Cập nhật giáo viên
     */
    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);
        
        // Lưu jobtitleid cũ để so sánh
        $oldJobTitleId = $teacher->jobtitleid;

        $request->merge([
            'fullname' => trim($request->fullname),
            'birthdate' => $request->birthdate ? $request->birthdate : null,
            'gender' => $request->gender ? $request->gender : null,
            'jobtitleid' => $request->jobtitleid ? (int)$request->jobtitleid : null,
            'unitid' => $request->unitid ? (int)$request->unitid : null,
            'startdate' => $request->startdate ? $request->startdate : null,
            'status' => $request->status ?? 'active',
        ]);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($teacher->teacherid),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();
        
        $newJobTitleId = $validated['jobtitleid'] ?? null;

        $teacher->update($validated);

        // Nếu chức danh thay đổi, tự động ghi lịch sử
        if ($oldJobTitleId != $newJobTitleId && $newJobTitleId !== null) {
            // Lấy tên chức danh cũ và mới
            $oldJobTitle = $oldJobTitleId ? JobTitle::find($oldJobTitleId) : null;
            $newJobTitle = JobTitle::find($newJobTitleId);
            
            $oldJobTitleName = $oldJobTitle ? $oldJobTitle->jobtitlename : '';
            $newJobTitleName = $newJobTitle ? $newJobTitle->jobtitlename : '';
            
            // Đóng bản ghi lịch sử cũ (nếu có) bằng cách set expiredate = hôm nay
            TeacherJobTitleHistory::where('teacherid', $teacher->teacherid)
                ->whereNull('expiredate')
                ->update(['expiredate' => Carbon::today()]);
            
            // Tạo ghi chú mô tả thay đổi
            $note = '';
            if ($oldJobTitleId && $oldJobTitleName && $newJobTitleName) {
                $note = "Đổi từ {$oldJobTitleName} sang {$newJobTitleName}";
            } elseif ($newJobTitleName) {
                $note = "Chức danh đầu tiên: {$newJobTitleName}";
            } else {
                $note = 'Tự động ghi khi cập nhật chức danh trong quản lý giáo viên';
            }
            
            // Tạo bản ghi lịch sử mới
            TeacherJobTitleHistory::create([
                'teacherid' => $teacher->teacherid,
                'jobtitleid' => $newJobTitleId,
                'effectivedate' => Carbon::today(),
                'expiredate' => null, // Chưa kết thúc
                'note' => $note,
            ]);
        }

        return redirect()->route('admin.teacher.index')
            ->with('success', 'Cập nhật giáo viên thành công!');
    }

    /**
     * Xóa giáo viên
     */
    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        
        // TODO: Kiểm tra quan hệ với các bảng khác trước khi xóa
        
        $teacher->delete();

        return redirect()->route('admin.teacher.index')
            ->with('success', 'Xóa giáo viên thành công!');
    }
}

