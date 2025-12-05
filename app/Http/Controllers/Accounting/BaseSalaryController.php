<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\BaseSalary;
use App\Models\BudgetSpendingUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class BaseSalaryController extends Controller
{
    /**
     * Hiển thị danh sách mức lương cơ bản
     */
    public function index(Request $request)
    {
        $query = BaseSalary::with('unit');

        // Tìm kiếm theo mã mức lương
        if ($request->filled('search_id')) {
            $query->where('basesalaryid', $request->search_id);
        }

        // Tìm kiếm theo đơn vị
        if ($request->filled('search_unitid')) {
            $query->where('unitid', $request->search_unitid);
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

        $baseSalaries = $query->orderBy('basesalaryid', 'asc')->get();

        // Lấy danh sách đơn vị cho dropdown
        $allUnits = BudgetSpendingUnit::orderBy('unitname', 'asc')->get();

        return view('accounting.basesalaries.index', compact('baseSalaries', 'allUnits'));
    }

    /**
     * Validation rules
     */
    private function getValidationRules($ignoreId = null): array
    {
        $rules = [
            'unitid' => [
                'nullable',
                'integer',
                'exists:budgetspendingunit,unitid',
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
            'basesalaryamount' => [
                'required',
                'numeric',
                'min:100000',
            ],
            'note' => 'nullable|string|max:65535',
        ];

        // Unique constraint: unitid + effectivedate
        if ($ignoreId) {
            $rules['effectivedate'][] = Rule::unique('basesalary', 'effectivedate')
                ->where('unitid', request('unitid'))
                ->ignore($ignoreId, 'basesalaryid');
        } else {
            $rules['effectivedate'][] = Rule::unique('basesalary', 'effectivedate')
                ->where('unitid', request('unitid'));
        }

        return $rules;
    }

    /**
     * Validation messages
     */
    private function getValidationMessages(): array
    {
        return [
            'unitid.integer' => 'Đơn vị không hợp lệ',
            'unitid.exists' => 'Đơn vị không tồn tại',
            'effectivedate.required' => 'Ngày hiệu lực là bắt buộc',
            'effectivedate.date' => 'Ngày hiệu lực không hợp lệ',
            'effectivedate.unique' => 'Đã tồn tại mức lương cơ bản cho đơn vị này tại ngày hiệu lực này',
            'expirationdate.date' => 'Ngày hết hạn không hợp lệ',
            'expirationdate.after' => 'Ngày hết hạn phải sau ngày hiệu lực',
            'basesalaryamount.required' => 'Mức lương cơ bản là bắt buộc',
            'basesalaryamount.numeric' => 'Mức lương cơ bản phải là số',
            'basesalaryamount.min' => 'Mức lương cơ bản phải lớn hơn hoặc bằng 100,000 VNĐ',
        ];
    }

    /**
     * Lưu mức lương cơ bản mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        // Chuyển đổi basesalaryamount từ string sang float
        if (isset($validated['basesalaryamount'])) {
            $validated['basesalaryamount'] = (float) str_replace(',', '.', $validated['basesalaryamount']);
        }

        // Chuyển đổi unitid
        if (isset($validated['unitid'])) {
            $validated['unitid'] = $validated['unitid'] ? (int)$validated['unitid'] : null;
        }

        // Tự động đóng mức lương cơ bản cũ nếu có (set expirationdate = effectivedate - 1 ngày)
        if ($request->filled('unitid') && $request->filled('effectivedate')) {
            BaseSalary::where('unitid', $request->unitid)
                ->whereNull('expirationdate')
                ->where('effectivedate', '<', $request->effectivedate)
                ->update([
                    'expirationdate' => date('Y-m-d', strtotime($request->effectivedate . ' -1 day'))
                ]);
        }

        BaseSalary::create($validated);

        return redirect()->route('accounting.basesalary.index')
            ->with('success', 'Thêm mức lương cơ bản thành công!');
    }

    /**
     * Cập nhật mức lương cơ bản
     */
    public function update(Request $request, $id)
    {
        $baseSalary = BaseSalary::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($baseSalary->basesalaryid),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        // Chuyển đổi basesalaryamount từ string sang float
        if (isset($validated['basesalaryamount'])) {
            $validated['basesalaryamount'] = (float) str_replace(',', '.', $validated['basesalaryamount']);
        }

        // Chuyển đổi unitid
        if (isset($validated['unitid'])) {
            $validated['unitid'] = $validated['unitid'] ? (int)$validated['unitid'] : null;
        }

        $baseSalary->update($validated);

        return redirect()->route('accounting.basesalary.index')
            ->with('success', 'Cập nhật mức lương cơ bản thành công!');
    }

    /**
     * Tạm kết thúc mức lương cơ bản (set expirationdate = hôm nay)
     */
    public function terminate($id)
    {
        $baseSalary = BaseSalary::findOrFail($id);

        // Chỉ cho phép kết thúc nếu chưa có expirationdate
        if ($baseSalary->expirationdate !== null) {
            return redirect()->route('accounting.basesalary.index')
                ->with('error', 'Mức lương cơ bản này đã được kết thúc trước đó!');
        }

        // Set expirationdate = hôm nay
        $baseSalary->update([
            'expirationdate' => Carbon::today()
        ]);

        return redirect()->route('accounting.basesalary.index')
            ->with('success', 'Đã tạm kết thúc mức lương cơ bản thành công! Bạn có thể tạo mức lương cơ bản mới.');
    }

    /**
     * Xóa mức lương cơ bản
     */
    public function destroy($id)
    {
        $baseSalary = BaseSalary::findOrFail($id);

        // TODO: Kiểm tra quan hệ với bảng payroll trước khi cho phép xóa

        $baseSalary->delete();

        return redirect()->route('accounting.basesalary.index')
            ->with('success', 'Xóa mức lương cơ bản thành công!');
    }
}

