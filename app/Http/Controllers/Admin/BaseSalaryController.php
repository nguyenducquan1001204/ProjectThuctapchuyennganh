<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BaseSalary;
use App\Models\BudgetSpendingUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class BaseSalaryController extends Controller
{
    public function index(Request $request)
    {
        $query = BaseSalary::with('unit');

        if ($request->filled('search_id')) {
            $query->where('basesalaryid', $request->search_id);
        }

        if ($request->filled('search_unitid')) {
            $query->where('unitid', $request->search_unitid);
        }

        if ($request->filled('search_effectivedate')) {
            $query->whereDate('effectivedate', $request->search_effectivedate);
        }

        if ($request->filled('search_status')) {
            if ($request->search_status === 'active') {
                $query->whereNull('expirationdate');
            } elseif ($request->search_status === 'expired') {
                $query->whereNotNull('expirationdate')
                      ->where('expirationdate', '<', now());
            }
        }

        $baseSalaries = $query->orderBy('basesalaryid', 'asc')->get();

        $allUnits = BudgetSpendingUnit::orderBy('unitname', 'asc')->get();

        return view('admin.basesalaries.index', compact('baseSalaries', 'allUnits'));
    }

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

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        if (isset($validated['basesalaryamount'])) {
            $validated['basesalaryamount'] = (float) str_replace(',', '.', $validated['basesalaryamount']);
        }

        if (isset($validated['unitid'])) {
            $validated['unitid'] = $validated['unitid'] ? (int)$validated['unitid'] : null;
        }

        if ($request->filled('unitid') && $request->filled('effectivedate')) {
            BaseSalary::where('unitid', $request->unitid)
                ->whereNull('expirationdate')
                ->where('effectivedate', '<', $request->effectivedate)
                ->update([
                    'expirationdate' => date('Y-m-d', strtotime($request->effectivedate . ' -1 day'))
                ]);
        }

        BaseSalary::create($validated);

        return redirect()->route('admin.basesalary.index')
            ->with('success', 'Thêm mức lương cơ bản thành công!');
    }

    public function update(Request $request, $id)
    {
        $baseSalary = BaseSalary::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($baseSalary->basesalaryid),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        if (isset($validated['basesalaryamount'])) {
            $validated['basesalaryamount'] = (float) str_replace(',', '.', $validated['basesalaryamount']);
        }

        if (isset($validated['unitid'])) {
            $validated['unitid'] = $validated['unitid'] ? (int)$validated['unitid'] : null;
        }

        $baseSalary->update($validated);

        return redirect()->route('admin.basesalary.index')
            ->with('success', 'Cập nhật mức lương cơ bản thành công!');
    }

    public function terminate($id)
    {
        $baseSalary = BaseSalary::findOrFail($id);

        if ($baseSalary->expirationdate !== null) {
            return redirect()->route('admin.basesalary.index')
                ->with('error', 'Mức lương cơ bản này đã được kết thúc trước đó!');
        }

        $baseSalary->update([
            'expirationdate' => Carbon::today()
        ]);

        return redirect()->route('admin.basesalary.index')
            ->with('success', 'Đã tạm kết thúc mức lương cơ bản thành công! Bạn có thể tạo mức lương cơ bản mới.');
    }

    public function destroy($id)
    {
        $baseSalary = BaseSalary::findOrFail($id);

        $baseSalary->delete();

        return redirect()->route('admin.basesalary.index')
            ->with('success', 'Xóa mức lương cơ bản thành công!');
    }
}


