<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PayrollComponentController extends Controller
{
    /**
     * Hiển thị danh sách thành phần lương
     */
    public function index(Request $request)
    {
        $query = PayrollComponent::query();

        if ($request->filled('search_id')) {
            $query->where('componentid', $request->search_id);
        }

        if ($request->filled('search_name')) {
            $query->where('componentname', 'like', '%' . $request->search_name . '%');
        }

        if ($request->filled('search_group')) {
            $query->where('componentgroup', $request->search_group);
        }

        if ($request->filled('search_method')) {
            $query->where('calculationmethod', $request->search_method);
        }

        $components = $query->orderBy('componentid', 'asc')->get();

        // Lấy danh sách nhóm và phương pháp tính từ database (tiếng Việt)
        $groups = PayrollComponent::distinct()->pluck('componentgroup')->filter()->sort()->values()->toArray();
        $methods = PayrollComponent::distinct()->pluck('calculationmethod')->filter()->sort()->values()->toArray();

        // Tạo mảng cho dropdown (key = value = tiếng Việt)
        $groups = array_combine($groups, $groups);
        $methods = array_combine($methods, $methods);

        return view('admin.payrollcomponents.index', compact('components', 'groups', 'methods'));
    }

    /**
     * Validation rules
     */
    private function getValidationRules($ignoreId = null): array
    {
        $rules = [
            'componentname' => [
                'required',
                'string',
                'min:3',
                'max:200',
                // cho phép chữ, số, khoảng trắng, và một số ký tự: , . - ( ) %
                'regex:/^[\p{L}\p{N}\s,.\-%()]+$/u',
                function ($attribute, $value, $fail) {
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('Tên thành phần lương không được có nhiều khoảng trắng liên tiếp');
                    }
                },
            ],
            'componentgroup' => [
                'required',
                'string',
                'max:50',
                Rule::in(['Thu nhập', 'Khoản trừ nhân viên', 'Đóng góp đơn vị']),
            ],
            'calculationmethod' => [
                'required',
                'string',
                'max:50',
                Rule::in(['Cố định', 'Hệ số', 'Phần trăm']),
            ],
            'componentdescription' => 'nullable|string|max:65535',
        ];

        if ($ignoreId) {
            $rules['componentname'][] = Rule::unique('payrollcomponent', 'componentname')->ignore($ignoreId, 'componentid');
        } else {
            $rules['componentname'][] = Rule::unique('payrollcomponent', 'componentname');
        }

        return $rules;
    }

    /**
     * Validation messages
     */
    private function getValidationMessages(): array
    {
        return [
            'componentname.required' => 'Tên thành phần lương là bắt buộc',
            'componentname.min'      => 'Tên thành phần lương phải có ít nhất 3 ký tự',
            'componentname.max'      => 'Tên thành phần lương không được vượt quá 200 ký tự',
            'componentname.regex'    => 'Tên thành phần lương chỉ được chứa chữ cái, số, khoảng trắng và các ký tự: , . - ( ) %',
            'componentname.unique'   => 'Tên thành phần lương đã tồn tại',

            'componentgroup.required' => 'Nhóm thành phần là bắt buộc',
            'componentgroup.in'       => 'Nhóm thành phần không hợp lệ',

            'calculationmethod.required' => 'Phương pháp tính là bắt buộc',
            'calculationmethod.in'       => 'Phương pháp tính không hợp lệ',
        ];
    }

    /**
     * Lưu thành phần lương mới
     */
    public function store(Request $request)
    {
        $request->merge([
            'componentname' => trim($request->componentname),
        ]);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        PayrollComponent::create($validated);

        return redirect()->route('admin.payrollcomponent.index')
            ->with('success', 'Thêm thành phần lương thành công!');
    }

    /**
     * Cập nhật thành phần lương
     */
    public function update(Request $request, $id)
    {
        $component = PayrollComponent::findOrFail($id);

        $request->merge([
            'componentname' => trim($request->componentname),
        ]);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($component->componentid),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        $component->update($validated);

        return redirect()->route('admin.payrollcomponent.index')
            ->with('success', 'Cập nhật thành phần lương thành công!');
    }

    /**
     * Xóa thành phần lương
     */
    public function destroy($id)
    {
        $component = PayrollComponent::findOrFail($id);

        // TODO: kiểm tra quan hệ với các bảng bảng lương trước khi cho phép xóa

        $component->delete();

        return redirect()->route('admin.payrollcomponent.index')
            ->with('success', 'Xóa thành phần lương thành công!');
    }
}


