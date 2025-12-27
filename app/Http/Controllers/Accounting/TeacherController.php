<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\JobTitle;
use App\Models\BudgetSpendingUnit;
use App\Models\TeacherJobTitleHistory;
use App\Models\PayrollRunDetail;
use App\Models\EmploymentContract;
use App\Models\TeacherPayrollComponent;
use App\Models\SalaryIncreaseDecision;
use App\Models\SystemUser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
        
        return view('accounting.teachers.index', compact('teachers', 'jobTitles', 'units'));
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
            'currentcoefficient' => [
                'nullable',
                'numeric',
                'min:1',
                'max:10',
                'regex:/^\d{1,2}(\.\d{1,2})?$/', // Định dạng: 1-10, có thể có 1-2 chữ số thập phân
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
            'currentcoefficient.numeric' => 'Hệ số lương ngạch bậc phải là số',
            'currentcoefficient.min' => 'Hệ số lương ngạch bậc không được nhỏ hơn 1',
            'currentcoefficient.max' => 'Hệ số lương ngạch bậc không được lớn hơn 10',
            'currentcoefficient.regex' => 'Hệ số lương ngạch bậc không hợp lệ (ví dụ: 5.70, 5.02, 4.68)',
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
            'currentcoefficient' => $request->currentcoefficient ? (float)$request->currentcoefficient : null,
            'status' => $request->status ?? 'active',
        ]);

        $validator = Validator::make($request->all(), $this->getValidationRules(), $this->getValidationMessages());

        $validated = $validator->validate();

        $newCoefficient = $validated['currentcoefficient'] ?? null;

        $teacher = Teacher::create($validated);

        // Nếu có hệ số lương khi tạo mới, tự động ghi lịch sử
        if ($newCoefficient !== null) {
            $history = [[
                'coefficient' => (float)$newCoefficient,
                'effectivedate' => $teacher->startdate ? Carbon::parse($teacher->startdate)->format('Y-m-d') : Carbon::today()->format('Y-m-d'),
                'expiredate' => null,
                'note' => 'Hệ số lương ban đầu',
            ]];
            
            $teacher->coefficient_history = $history;
            $teacher->save();
        }

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

        return redirect()->route('accounting.teacher.index')
            ->with('success', 'Thêm giáo viên thành công!');
    }

    /**
     * Cập nhật giáo viên
     */
    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);
        
        // Lưu các giá trị cũ để so sánh
        $oldJobTitleId = $teacher->jobtitleid;
        $oldCoefficient = $teacher->currentcoefficient;

        $request->merge([
            'fullname' => trim($request->fullname),
            'birthdate' => $request->birthdate ? $request->birthdate : null,
            'gender' => $request->gender ? $request->gender : null,
            'jobtitleid' => $request->jobtitleid ? (int)$request->jobtitleid : null,
            'unitid' => $request->unitid ? (int)$request->unitid : null,
            'startdate' => $request->startdate ? $request->startdate : null,
            'currentcoefficient' => $request->currentcoefficient ? (float)$request->currentcoefficient : null,
            'status' => $request->status ?? 'active',
        ]);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($teacher->teacherid),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();
        
        $newJobTitleId = $validated['jobtitleid'] ?? null;
        $newCoefficient = $validated['currentcoefficient'] ?? null;

        // Kiểm tra hệ số lương có thay đổi không
        $coefficientChanged = false;
        if ($oldCoefficient != $newCoefficient && $newCoefficient !== null) {
            $coefficientChanged = true;
        }

        $teacher->update($validated);

        // Nếu hệ số lương thay đổi, tự động ghi lịch sử
        if ($coefficientChanged) {
            $note = '';
            if ($oldCoefficient !== null) {
                $note = "Thay đổi từ " . (string)$oldCoefficient . " sang " . (string)$newCoefficient;
            } else {
                $note = "Hệ số lương ban đầu: " . (string)$newCoefficient;
            }

            // Thêm vào lịch sử
            $history = $teacher->coefficient_history ?? [];
            
            // Đóng bản ghi cũ (nếu có)
            if (!empty($history)) {
                foreach ($history as &$record) {
                    if (!isset($record['expiredate']) || $record['expiredate'] === null) {
                        $record['expiredate'] = Carbon::today()->subDay()->format('Y-m-d');
                    }
                }
            }

            // Thêm bản ghi mới
            $history[] = [
                'coefficient' => (float)$newCoefficient,
                'effectivedate' => Carbon::today()->format('Y-m-d'),
                'expiredate' => null,
                'note' => $note,
            ];

            $teacher->coefficient_history = $history;
            $teacher->save();
        }

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

        return redirect()->route('accounting.teacher.index')
            ->with('success', 'Cập nhật giáo viên thành công!');
    }

    /**
     * Xóa giáo viên
     */
    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        
        $errors = [];
        
        $payrollRunDetailCount = PayrollRunDetail::where('teacherid', $id)->count();
        if ($payrollRunDetailCount > 0) {
            $errors[] = "Có {$payrollRunDetailCount} chi tiết kỳ lương đang tham chiếu đến giáo viên này";
        }
        
        $employmentContractCount = EmploymentContract::where('teacherid', $id)->count();
        if ($employmentContractCount > 0) {
            $errors[] = "Có {$employmentContractCount} hợp đồng lao động đang thuộc giáo viên này";
        }
        
        $teacherPayrollComponentCount = TeacherPayrollComponent::where('teacherid', $id)->count();
        if ($teacherPayrollComponentCount > 0) {
            $errors[] = "Có {$teacherPayrollComponentCount} thành phần lương đang thuộc giáo viên này";
        }
        
        $teacherJobTitleHistoryCount = TeacherJobTitleHistory::where('teacherid', $id)->count();
        if ($teacherJobTitleHistoryCount > 0) {
            $errors[] = "Có {$teacherJobTitleHistoryCount} lịch sử chức danh đang thuộc giáo viên này";
        }
        
        $salaryIncreaseDecisionCount = SalaryIncreaseDecision::where('teacherid', $id)->count();
        if ($salaryIncreaseDecisionCount > 0) {
            $errors[] = "Có {$salaryIncreaseDecisionCount} quyết định tăng lương đang thuộc giáo viên này";
        }
        
        $systemUserCount = SystemUser::where('teacherid', $id)->count();
        if ($systemUserCount > 0) {
            $errors[] = "Có {$systemUserCount} tài khoản hệ thống đang liên kết với giáo viên này";
        }
        
        if (!empty($errors)) {
            $errorMessage = 'Không thể xóa giáo viên vì: ' . implode(', ', $errors);
            return redirect()->route('accounting.teacher.index')
                ->with('error', $errorMessage);
        }
        
        $teacher->delete();

        return redirect()->route('accounting.teacher.index')
            ->with('success', 'Xóa giáo viên thành công!');
    }

    /**
     * Lấy lịch sử hệ số lương của giáo viên (API)
     */
    public function getCoefficientHistory($id)
    {
        $teacher = Teacher::findOrFail($id);
        $history = $teacher->getCoefficientHistory();
        
        return response()->json([
            'success' => true,
            'teacher' => [
                'id' => $teacher->teacherid,
                'fullname' => $teacher->fullname,
                'currentcoefficient' => $teacher->currentcoefficient,
            ],
            'history' => $history,
        ]);
    }
}

