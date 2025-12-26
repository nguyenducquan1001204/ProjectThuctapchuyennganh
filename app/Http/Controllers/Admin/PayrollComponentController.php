<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PayrollComponentController extends Controller
{
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

        $groups = PayrollComponent::distinct()->pluck('componentgroup')->filter()->sort()->values()->toArray();
        $methods = PayrollComponent::distinct()->pluck('calculationmethod')->filter()->sort()->values()->toArray();

        $groups = array_combine($groups, $groups);
        $methods = array_combine($methods, $methods);

        return view('admin.payrollcomponents.index', compact('components', 'groups', 'methods'));
    }

    private function getValidationRules($ignoreId = null): array
    {
        $rules = [
            'componentname' => [
                'required',
                'string',
                'min:3',
                'max:200',
                'regex:/^[\p{L}\p{N}\s,.\-%()]+$/u',
                function ($attribute, $value, $fail) {
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('Tên thành phần lương không được có nhiều khoảng trắng liên tiếp');
                        return;
                    }

                    $trimmed = trim($value);
                    $words = preg_split('/\s+/', $trimmed);
                    
                    foreach ($words as $word) {
                        if (mb_strlen($word) < 2) {
                            $fail('Mỗi từ trong tên thành phần lương phải có ít nhất 2 ký tự');
                            return;
                        }
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

    public function destroy($id)
    {
        $component = PayrollComponent::findOrFail($id);

        $component->delete();

        return redirect()->route('admin.payrollcomponent.index')
            ->with('success', 'Xóa thành phần lương thành công!');
    }
}


