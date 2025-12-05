<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\PayrollComponent;
use App\Models\PayrollComponentConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PayrollComponentConfigController extends Controller
{
    /**
     * Hiển thị danh sách cấu hình thành phần lương
     */
    public function index(Request $request)
    {
        $query = PayrollComponentConfig::with('component');

        // Tìm kiếm theo mã cấu hình
        if ($request->filled('search_id')) {
            $query->where('configid', $request->search_id);
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

        $configs = $query->orderBy('configid', 'asc')->get();

        // Lấy danh sách thành phần lương cho dropdown
        $allComponents = PayrollComponent::orderBy('componentname', 'asc')->get();

        // Lấy danh sách component đã có cấu hình đang hiệu lực (expirationdate IS NULL)
        $activeConfigComponentIds = PayrollComponentConfig::whereNull('expirationdate')
            ->pluck('componentid')
            ->unique()
            ->toArray();

        // Loại bỏ các component đã có cấu hình đang hiệu lực (dùng cho form create và edit)
        $componentsForCreate = $allComponents->reject(function ($component) use ($activeConfigComponentIds) {
            return in_array($component->componentid, $activeConfigComponentIds);
        });

        // Cho form edit: loại bỏ các component đã có cấu hình đang hiệu lực
        // (component hiện tại của cấu hình đang sửa sẽ được thêm vào trong JavaScript nếu cần)
        $componentsForEdit = $componentsForCreate;

        return view('accounting.payrollcomponentconfigs.index', compact('configs', 'allComponents', 'componentsForCreate', 'componentsForEdit'));
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
     * Validation rules
     */
    private function getValidationRules($ignoreId = null): array
    {
        $rules = [
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
            'defaultcoefficient' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999.9999',
            ],
            'percentagevalue' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'fixedamount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'note' => 'nullable|string|max:65535',
        ];

        // Unique constraint: componentid + effectivedate
        if ($ignoreId) {
            $rules['effectivedate'][] = Rule::unique('payrollcomponentconfig', 'effectivedate')
                ->where('componentid', request('componentid'))
                ->ignore($ignoreId, 'configid');
        } else {
            $rules['effectivedate'][] = Rule::unique('payrollcomponentconfig', 'effectivedate')
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
            'componentid.required' => 'Thành phần lương là bắt buộc',
            'componentid.exists' => 'Thành phần lương không tồn tại',
            'effectivedate.required' => 'Ngày hiệu lực là bắt buộc',
            'effectivedate.date' => 'Ngày hiệu lực không hợp lệ',
            'effectivedate.unique' => 'Đã tồn tại cấu hình cho thành phần lương này tại ngày hiệu lực này',
            'expirationdate.date' => 'Ngày hết hạn không hợp lệ',
            'expirationdate.after' => 'Ngày hết hạn phải sau ngày hiệu lực',
            'defaultcoefficient.numeric' => 'Hệ số mặc định phải là số',
            'defaultcoefficient.min' => 'Hệ số mặc định không được nhỏ hơn 0',
            'defaultcoefficient.max' => 'Hệ số mặc định không được lớn hơn 9999.9999',
            'percentagevalue.numeric' => 'Tỷ lệ phần trăm phải là số',
            'percentagevalue.min' => 'Tỷ lệ phần trăm không được nhỏ hơn 0',
            'percentagevalue.max' => 'Tỷ lệ phần trăm không được lớn hơn 100',
            'fixedamount.numeric' => 'Số tiền cố định phải là số',
            'fixedamount.min' => 'Số tiền cố định không được nhỏ hơn 0',
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

        // Kiểm tra logic: phải có ít nhất 1 trong 3 giá trị (coefficient, percentage, fixed)
        $validator->after(function ($validator) use ($request) {
            $component = PayrollComponent::find($request->componentid);
            if (!$component) {
                return;
            }

            $hasValue = false;
            $method = $component->calculationmethod;

            if ($method === 'Hệ số' && $request->filled('defaultcoefficient')) {
                $hasValue = true;
            } elseif ($method === 'Phần trăm' && $request->filled('percentagevalue')) {
                $hasValue = true;
            } elseif ($method === 'Cố định' && $request->filled('fixedamount')) {
                $hasValue = true;
            }

            if (!$hasValue) {
                if ($method === 'Hệ số') {
                    $validator->errors()->add('defaultcoefficient', 'Vui lòng nhập hệ số mặc định');
                } elseif ($method === 'Phần trăm') {
                    $validator->errors()->add('percentagevalue', 'Vui lòng nhập tỷ lệ phần trăm');
                } elseif ($method === 'Cố định') {
                    $validator->errors()->add('fixedamount', 'Vui lòng nhập số tiền cố định');
                }
            }
        });

        $validated = $validator->validate();

        // Tự động đóng cấu hình cũ nếu có (set expirationdate = effectivedate - 1 ngày)
        if ($request->filled('componentid') && $request->filled('effectivedate')) {
            PayrollComponentConfig::where('componentid', $request->componentid)
                ->whereNull('expirationdate')
                ->where('effectivedate', '<', $request->effectivedate)
                ->update([
                    'expirationdate' => date('Y-m-d', strtotime($request->effectivedate . ' -1 day'))
                ]);
        }

        PayrollComponentConfig::create($validated);

        return redirect()->route('accounting.payrollcomponentconfig.index')
            ->with('success', 'Thêm cấu hình thành phần lương thành công!');
    }

    /**
     * Cập nhật cấu hình
     */
    public function update(Request $request, $id)
    {
        $config = PayrollComponentConfig::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($config->configid),
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

            if ($method === 'Hệ số' && $request->filled('defaultcoefficient')) {
                $hasValue = true;
            } elseif ($method === 'Phần trăm' && $request->filled('percentagevalue')) {
                $hasValue = true;
            } elseif ($method === 'Cố định' && $request->filled('fixedamount')) {
                $hasValue = true;
            }

            if (!$hasValue) {
                if ($method === 'Hệ số') {
                    $validator->errors()->add('defaultcoefficient', 'Vui lòng nhập hệ số mặc định');
                } elseif ($method === 'Phần trăm') {
                    $validator->errors()->add('percentagevalue', 'Vui lòng nhập tỷ lệ phần trăm');
                } elseif ($method === 'Cố định') {
                    $validator->errors()->add('fixedamount', 'Vui lòng nhập số tiền cố định');
                }
            }
        });

        $validated = $validator->validate();

        $config->update($validated);

        return redirect()->route('accounting.payrollcomponentconfig.index')
            ->with('success', 'Cập nhật cấu hình thành phần lương thành công!');
    }

    /**
     * Xóa cấu hình
     */
    public function destroy($id)
    {
        $config = PayrollComponentConfig::findOrFail($id);

        // TODO: Kiểm tra quan hệ với bảng payroll trước khi cho phép xóa

        $config->delete();

        return redirect()->route('accounting.payrollcomponentconfig.index')
            ->with('success', 'Xóa cấu hình thành phần lương thành công!');
    }
}

