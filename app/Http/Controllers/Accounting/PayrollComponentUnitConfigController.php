<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\PayrollComponent;
use App\Models\PayrollComponentUnitConfig;
use App\Models\BudgetSpendingUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PayrollComponentUnitConfigController extends Controller
{
    /**
     * Hiển thị danh sách cấu hình thành phần lương theo đơn vị
     */
    public function index(Request $request)
    {
        $query = PayrollComponentUnitConfig::with(['component', 'unit']);

        // Tìm kiếm theo mã cấu hình
        if ($request->filled('search_id')) {
            $query->where('unitconfigid', $request->search_id);
        }

        // Tìm kiếm theo đơn vị
        if ($request->filled('search_unitid')) {
            $query->where('unitid', $request->search_unitid);
        }

        // Tìm kiếm theo thành phần lương
        if ($request->filled('search_componentid')) {
            $query->where('componentid', $request->search_componentid);
        }

        // Tìm kiếm theo ngày hiệu lực
        if ($request->filled('search_effectivedate')) {
            $query->whereDate('effectivedate', $request->search_effectivedate);
        }

        // Tìm kiếm theo trạng thái (đang hiệu lực / đã hết hạn)
        if ($request->filled('search_status')) {
            if ($request->search_status === 'active') {
                $query->whereNull('expirationdate');
            } elseif ($request->search_status === 'expired') {
                $query->whereNotNull('expirationdate')
                      ->where('expirationdate', '<', now());
            }
        }

        $configs = $query->orderBy('unitconfigid', 'asc')->get();

        // Lấy danh sách đơn vị và thành phần lương cho dropdown
        $allUnits = BudgetSpendingUnit::orderBy('unitname', 'asc')->get();
        $allComponents = PayrollComponent::orderBy('componentname', 'asc')->get();

        return view('accounting.payrollcomponentunitconfigs.index', compact('configs', 'allUnits', 'allComponents'));
    }

    /**
     * Lấy thông tin component theo ID (API)
     */
    public function getComponent($id)
    {
        $component = PayrollComponent::find($id);
        
        if (!$component) {
            return response()->json(['error' => 'Thành phần lương không tồn tại'], 404);
        }

        return response()->json([
            'componentid' => $component->componentid,
            'componentname' => $component->componentname,
            'calculationmethod' => $component->calculationmethod,
        ]);
    }

    /**
     * Lấy danh sách component đã được cấu hình cho đơn vị (đang hiệu lực) - API
     */
    public function getUsedComponents($unitId)
    {
        $usedComponentIds = PayrollComponentUnitConfig::where('unitid', $unitId)
            ->whereNull('expirationdate')
            ->pluck('componentid')
            ->unique()
            ->toArray();

        return response()->json([
            'success' => true,
            'used_component_ids' => $usedComponentIds,
        ]);
    }

    /**
     * Validation rules
     */
    private function getValidationRules($ignoreId = null): array
    {
        $rules = [
            'unitid' => [
                'required',
                'integer',
                'exists:budgetspendingunit,unitid',
            ],
            'componentid' => [
                'required',
                'integer',
                'exists:payrollcomponent,componentid',
            ],
            'effectivedate' => [
                'required',
                'date',
            ],
            'expirationdate' => [
                'nullable',
                'date',
                'after:effectivedate',
            ],
            'adjustcoefficient' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999.9999',
            ],
            'adjustpercentage' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'adjustfixedamount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'note' => 'nullable|string|max:65535',
        ];

        // Unique constraint: unitid + componentid + effectivedate
        if ($ignoreId) {
            $rules['effectivedate'][] = Rule::unique('payrollcomponentunitconfig', 'effectivedate')
                ->where('unitid', request('unitid'))
                ->where('componentid', request('componentid'))
                ->ignore($ignoreId, 'unitconfigid');
        } else {
            $rules['effectivedate'][] = Rule::unique('payrollcomponentunitconfig', 'effectivedate')
                ->where('unitid', request('unitid'))
                ->where('componentid', request('componentid'));
        }

        return $rules;
    }

    /**
     * Validation messages
     */
    private function getValidationMessages(): array
    {
        return [
            'unitid.required' => 'Đơn vị là bắt buộc',
            'unitid.exists' => 'Đơn vị không tồn tại',
            'componentid.required' => 'Thành phần lương là bắt buộc',
            'componentid.exists' => 'Thành phần lương không tồn tại',
            'effectivedate.required' => 'Ngày hiệu lực là bắt buộc',
            'effectivedate.date' => 'Ngày hiệu lực không hợp lệ',
            'effectivedate.unique' => 'Đã tồn tại cấu hình cho đơn vị và thành phần lương này tại ngày hiệu lực này',
            'expirationdate.date' => 'Ngày hết hạn không hợp lệ',
            'expirationdate.after' => 'Ngày hết hạn phải sau ngày hiệu lực',
            'adjustcoefficient.numeric' => 'Hệ số điều chỉnh phải là số',
            'adjustcoefficient.min' => 'Hệ số điều chỉnh không được nhỏ hơn 0',
            'adjustcoefficient.max' => 'Hệ số điều chỉnh không được lớn hơn 9999.9999',
            'adjustpercentage.numeric' => 'Tỷ lệ phần trăm điều chỉnh phải là số',
            'adjustpercentage.min' => 'Tỷ lệ phần trăm điều chỉnh không được nhỏ hơn 0',
            'adjustpercentage.max' => 'Tỷ lệ phần trăm điều chỉnh không được lớn hơn 100',
            'adjustfixedamount.numeric' => 'Giá trị tiền điều chỉnh phải là số',
            'adjustfixedamount.min' => 'Giá trị tiền điều chỉnh không được nhỏ hơn 0',
        ];
    }

    /**
     * Lưu cấu hình mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        // Kiểm tra logic: phải có ít nhất 1 trong 3 giá trị điều chỉnh (coefficient, percentage, fixed)
        $validator->after(function ($validator) use ($request) {
            $component = PayrollComponent::find($request->componentid);
            if (!$component) {
                return;
            }

            $hasValue = false;
            $method = $component->calculationmethod;

            if ($method === 'Hệ số' && $request->filled('adjustcoefficient')) {
                $hasValue = true;
            } elseif ($method === 'Phần trăm' && $request->filled('adjustpercentage')) {
                $hasValue = true;
            } elseif ($method === 'Cố định' && $request->filled('adjustfixedamount')) {
                $hasValue = true;
            }

            if (!$hasValue) {
                if ($method === 'Hệ số') {
                    $validator->errors()->add('adjustcoefficient', 'Vui lòng nhập hệ số điều chỉnh');
                } elseif ($method === 'Phần trăm') {
                    $validator->errors()->add('adjustpercentage', 'Vui lòng nhập tỷ lệ phần trăm điều chỉnh');
                } elseif ($method === 'Cố định') {
                    $validator->errors()->add('adjustfixedamount', 'Vui lòng nhập giá trị tiền điều chỉnh');
                }
            }
        });

        $validated = $validator->validate();

        // Tự động đóng cấu hình cũ nếu có (set expirationdate = effectivedate - 1 ngày)
        if ($request->filled('unitid') && $request->filled('componentid') && $request->filled('effectivedate')) {
            PayrollComponentUnitConfig::where('unitid', $request->unitid)
                ->where('componentid', $request->componentid)
                ->whereNull('expirationdate')
                ->where('effectivedate', '<', $request->effectivedate)
                ->update([
                    'expirationdate' => date('Y-m-d', strtotime($request->effectivedate . ' -1 day'))
                ]);
        }

        PayrollComponentUnitConfig::create($validated);

        return redirect()->route('accounting.payrollcomponentunitconfig.index')
            ->with('success', 'Thêm cấu hình thành phần lương theo đơn vị thành công!');
    }

    /**
     * Cập nhật cấu hình
     */
    public function update(Request $request, $id)
    {
        $config = PayrollComponentUnitConfig::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($config->unitconfigid),
            $this->getValidationMessages()
        );

        // Kiểm tra logic tương tự store
        $validator->after(function ($validator) use ($request) {
            $component = PayrollComponent::find($request->componentid);
            if (!$component) {
                return;
            }

            $hasValue = false;
            $method = $component->calculationmethod;

            if ($method === 'Hệ số' && $request->filled('adjustcoefficient')) {
                $hasValue = true;
            } elseif ($method === 'Phần trăm' && $request->filled('adjustpercentage')) {
                $hasValue = true;
            } elseif ($method === 'Cố định' && $request->filled('adjustfixedamount')) {
                $hasValue = true;
            }

            if (!$hasValue) {
                if ($method === 'Hệ số') {
                    $validator->errors()->add('adjustcoefficient', 'Vui lòng nhập hệ số điều chỉnh');
                } elseif ($method === 'Phần trăm') {
                    $validator->errors()->add('adjustpercentage', 'Vui lòng nhập tỷ lệ phần trăm điều chỉnh');
                } elseif ($method === 'Cố định') {
                    $validator->errors()->add('adjustfixedamount', 'Vui lòng nhập giá trị tiền điều chỉnh');
                }
            }
        });

        $validated = $validator->validate();

        $config->update($validated);

        return redirect()->route('accounting.payrollcomponentunitconfig.index')
            ->with('success', 'Cập nhật cấu hình thành phần lương theo đơn vị thành công!');
    }

    /**
     * Xóa cấu hình
     */
    public function destroy($id)
    {
        $config = PayrollComponentUnitConfig::findOrFail($id);

        // TODO: Kiểm tra quan hệ với bảng payroll trước khi cho phép xóa

        $config->delete();

        return redirect()->route('accounting.payrollcomponentunitconfig.index')
            ->with('success', 'Xóa cấu hình thành phần lương theo đơn vị thành công!');
    }
}

