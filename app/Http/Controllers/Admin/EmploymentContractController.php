<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmploymentContract;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EmploymentContractController extends Controller
{
    /**
     * Hiển thị danh sách hợp đồng
     */
    public function index(Request $request)
    {
        $query = EmploymentContract::with(['teacher']);
        
        // Tìm kiếm theo giáo viên
        if ($request->filled('search_teacher')) {
            $query->whereHas('teacher', function($q) use ($request) {
                $q->where('fullname', 'like', '%' . $request->search_teacher . '%');
            });
        }
        
        // Tìm kiếm theo loại hợp đồng
        if ($request->filled('search_contracttype')) {
            $query->where('contracttype', $request->search_contracttype);
        }
        
        // Tìm kiếm theo ngày ký
        if ($request->filled('search_signdate')) {
            $query->whereDate('signdate', $request->search_signdate);
        }
        
        // Tìm kiếm theo ngày hiệu lực
        if ($request->filled('search_startdate')) {
            $query->whereDate('startdate', $request->search_startdate);
        }
        
        $contracts = $query->orderBy('contractid', 'desc')->get();
        
        // Lấy danh sách giáo viên cho dropdown
        // Khi tạo mới: Loại bỏ giáo viên đã có hợp đồng đang hiệu lực (enddate = null hoặc enddate >= today)
        // Khi chỉnh sửa: Hiển thị tất cả giáo viên để có thể thay đổi
        $today = Carbon::today();
        $teacherIdsWithActiveContracts = EmploymentContract::where(function($q) use ($today) {
            $q->whereNull('enddate')
              ->orWhere('enddate', '>=', $today);
        })->pluck('teacherid')->unique();
        
        $allTeachers = Teacher::orderBy('fullname', 'asc')->get();
        $availableTeachers = Teacher::whereNotIn('teacherid', $teacherIdsWithActiveContracts)
            ->orderBy('fullname', 'asc')
            ->get();
        
        // Gán teachers = availableTeachers cho dropdown tạo mới
        $teachers = $availableTeachers;
        // allTeachers dùng cho dropdown chỉnh sửa
        
        return view('admin.employmentcontracts.index', compact('contracts', 'teachers', 'allTeachers'));
    }

    /**
     * Validation rules cho hợp đồng
     */
    private function getValidationRules($ignoreId = null): array
    {
        return [
            'teacherid' => [
                'required',
                'integer',
                'exists:teacher,teacherid',
            ],
            'contracttype' => [
                'required',
                'string',
                'max:150',
            ],
            'signdate' => [
                'required',
                'date',
            ],
            'startdate' => [
                'required',
                'date',
                'after_or_equal:signdate',
            ],
            'enddate' => [
                'nullable',
                'date',
                'after:startdate',
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
            'contracttype.required' => 'Loại hợp đồng là bắt buộc',
            'contracttype.string' => 'Loại hợp đồng phải là chuỗi',
            'contracttype.max' => 'Loại hợp đồng không được vượt quá 150 ký tự',
            'signdate.required' => 'Ngày ký hợp đồng là bắt buộc',
            'signdate.date' => 'Ngày ký hợp đồng không hợp lệ',
            'startdate.required' => 'Ngày hiệu lực là bắt buộc',
            'startdate.date' => 'Ngày hiệu lực không hợp lệ',
            'startdate.after_or_equal' => 'Ngày hiệu lực phải sau hoặc bằng ngày ký',
            'enddate.date' => 'Ngày hết hạn không hợp lệ',
            'enddate.after' => 'Ngày hết hạn phải sau ngày hiệu lực',
        ];
    }

    /**
     * Lưu hợp đồng mới
     */
    public function store(Request $request)
    {
        $request->merge([
            'teacherid' => $request->teacherid ? (int)$request->teacherid : null,
            'contracttype' => trim($request->contracttype),
            'signdate' => $request->signdate ? $request->signdate : null,
            'startdate' => $request->startdate ? $request->startdate : null,
            'enddate' => $request->enddate ? $request->enddate : null,
            'note' => $request->note ? trim($request->note) : null,
        ]);

        $validator = Validator::make($request->all(), $this->getValidationRules(), $this->getValidationMessages());

        $validated = $validator->validate();

        EmploymentContract::create($validated);

        return redirect()->route('admin.employmentcontract.index')
            ->with('success', 'Thêm hợp đồng thành công!');
    }

    /**
     * Cập nhật hợp đồng
     */
    public function update(Request $request, $id)
    {
        $contract = EmploymentContract::findOrFail($id);

        $request->merge([
            'teacherid' => $request->teacherid ? (int)$request->teacherid : null,
            'contracttype' => trim($request->contracttype),
            'signdate' => $request->signdate ? $request->signdate : null,
            'startdate' => $request->startdate ? $request->startdate : null,
            'enddate' => $request->enddate ? $request->enddate : null,
            'note' => $request->note ? trim($request->note) : null,
        ]);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($contract->contractid),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        $contract->update($validated);

        return redirect()->route('admin.employmentcontract.index')
            ->with('success', 'Cập nhật hợp đồng thành công!');
    }

    /**
     * Xóa hợp đồng
     */
    public function destroy($id)
    {
        $contract = EmploymentContract::findOrFail($id);
        
        // TODO: Kiểm tra quan hệ với các bảng khác trước khi xóa
        
        $contract->delete();

        return redirect()->route('admin.employmentcontract.index')
            ->with('success', 'Xóa hợp đồng thành công!');
    }
}
