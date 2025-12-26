<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollComponent;
use App\Models\PayrollComponentConfig;
use App\Models\PayrollComponentUnitConfig;
use App\Models\TeacherPayrollComponent;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TeacherPayrollComponentController extends Controller
{
    public function index(Request $request)
    {
        $query = TeacherPayrollComponent::with(['component', 'teacher']);

        if ($request->filled('search_id')) {
            $query->where('teachercomponentid', $request->search_id);
        }

        if ($request->filled('search_teacherid')) {
            $query->where('teacherid', $request->search_teacherid);
        }

        if ($request->filled('search_componentid')) {
            $query->where('componentid', $request->search_componentid);
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

        $configs = $query->orderBy('teachercomponentid', 'asc')->get();

        $allTeachers = Teacher::orderBy('fullname', 'asc')->get();
        $allComponents = PayrollComponent::orderBy('componentname', 'asc')->get();

        return view('admin.teacherpayrollcomponents.index', compact('configs', 'allTeachers', 'allComponents'));
    }

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

    public function getUsedComponents($teacherId)
    {
        $usedComponentIds = TeacherPayrollComponent::where('teacherid', $teacherId)
            ->whereNull('expirationdate')
            ->pluck('componentid')
            ->unique()
            ->toArray();

        return response()->json([
            'success' => true,
            'used_component_ids' => $usedComponentIds,
        ]);
    }

    public function getBaseValues(Request $request)
    {
        $componentId = $request->input('componentid');
        $teacherId = $request->input('teacherid');
        $effectiveDate = $request->input('effectivedate', now()->format('Y-m-d'));

        if (!$componentId) {
            return response()->json(['error' => 'Component ID là bắt buộc'], 400);
        }

        $component = PayrollComponent::find($componentId);
        if (!$component) {
            return response()->json(['error' => 'Thành phần lương không tồn tại'], 404);
        }

        $baseCoefficient = null;
        $basePercentage = null;
        $baseFixedAmount = null;

        $baseConfig = PayrollComponentConfig::where('componentid', $componentId)
            ->where('effectivedate', '<=', $effectiveDate)
            ->where(function($query) use ($effectiveDate) {
                $query->whereNull('expirationdate')
                      ->orWhere('expirationdate', '>=', $effectiveDate);
            })
            ->orderBy('effectivedate', 'desc')
            ->first();

        if ($baseConfig) {
            $baseCoefficient = $baseConfig->defaultcoefficient;
            $basePercentage = $baseConfig->percentagevalue;
            $baseFixedAmount = $baseConfig->fixedamount;
        }

        $unitAdjustCoefficient = null;
        $unitAdjustPercentage = null;
        if ($teacherId) {
            $teacher = Teacher::find($teacherId);
            if ($teacher && $teacher->unitid) {
                $unitConfig = PayrollComponentUnitConfig::where('unitid', $teacher->unitid)
                    ->where('componentid', $componentId)
                    ->where('effectivedate', '<=', $effectiveDate)
                    ->where(function($query) use ($effectiveDate) {
                        $query->whereNull('expirationdate')
                              ->orWhere('expirationdate', '>=', $effectiveDate);
                    })
                    ->orderBy('effectivedate', 'desc')
                    ->first();

                if ($unitConfig) {
                    $unitAdjustCoefficient = $unitConfig->adjustcoefficient;
                    $unitAdjustPercentage = $unitConfig->adjustpercentage;
                }
            }

            if ($component->componentname === 'Lương ngạch bậc' || 
                (stripos($component->componentname, 'lương ngạch bậc') !== false && $teacher->currentcoefficient)) {
                $baseCoefficient = $teacher->currentcoefficient;
            }
        }

        return response()->json([
            'success' => true,
            'base_coefficient' => $baseCoefficient,
            'base_percentage' => $basePercentage,
            'base_fixed_amount' => $baseFixedAmount,
            'unit_adjust_coefficient' => $unitAdjustCoefficient,
            'unit_adjust_percentage' => $unitAdjustPercentage,
            'calculation_method' => $component->calculationmethod,
        ]);
    }

    private function getValidationRulesForStore(): array
    {
        return [
            'teacherid' => [
                'required',
                'integer',
                'exists:teacher,teacherid',
            ],
            'componentids' => [
                'required',
                'array',
                'min:1',
            ],
            'componentids.*' => [
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
            'adjustcustomcoefficient' => [
                'nullable',
                'numeric',
                'between:-9999.9999,9999.9999',
            ],
            'adjustcustompercentage' => [
                'nullable',
                'numeric',
                'between:-9999.9999,9999.9999',
            ],
            'note' => 'nullable|string|max:65535',
        ];
    }

    private function getValidationRules($ignoreId = null): array
    {
        $rules = [
            'teacherid' => [
                'required',
                'integer',
                'exists:teacher,teacherid',
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
            'adjustcustomcoefficient' => [
                'nullable',
                'numeric',
                'between:-9999.9999,9999.9999',
            ],
            'adjustcustompercentage' => [
                'nullable',
                'numeric',
                'between:-9999.9999,9999.9999',
            ],
            'note' => 'nullable|string|max:65535',
        ];

        if ($ignoreId) {
            $rules['effectivedate'][] = Rule::unique('teacherpayrollcomponent', 'effectivedate')
                ->where('teacherid', request('teacherid'))
                ->where('componentid', request('componentid'))
                ->ignore($ignoreId, 'teachercomponentid');
        } else {
            $rules['effectivedate'][] = Rule::unique('teacherpayrollcomponent', 'effectivedate')
                ->where('teacherid', request('teacherid'))
                ->where('componentid', request('componentid'));
        }

        return $rules;
    }

    private function getValidationMessages(): array
    {
        return [
            'teacherid.required' => 'Giáo viên là bắt buộc',
            'teacherid.exists' => 'Giáo viên không tồn tại',
            'componentid.required' => 'Thành phần lương là bắt buộc',
            'componentid.exists' => 'Thành phần lương không tồn tại',
            'componentids.required' => 'Vui lòng chọn ít nhất một thành phần lương',
            'componentids.array' => 'Thành phần lương không hợp lệ',
            'componentids.min' => 'Vui lòng chọn ít nhất một thành phần lương',
            'componentids.*.required' => 'Thành phần lương là bắt buộc',
            'componentids.*.exists' => 'Thành phần lương không tồn tại',
            'effectivedate.required' => 'Ngày hiệu lực là bắt buộc',
            'effectivedate.date' => 'Ngày hiệu lực không hợp lệ',
            'effectivedate.unique' => 'Đã tồn tại cấu hình cho giáo viên và thành phần lương này tại ngày hiệu lực này',
            'expirationdate.date' => 'Ngày hết hạn không hợp lệ',
            'expirationdate.after' => 'Ngày hết hạn phải sau ngày hiệu lực',
            'adjustcustomcoefficient.numeric' => 'Hệ số điều chỉnh phải là số',
            'adjustcustomcoefficient.between' => 'Hệ số điều chỉnh phải nằm trong khoảng -9999.9999 đến 9999.9999',
            'adjustcustompercentage.numeric' => 'Phần trăm điều chỉnh phải là số',
            'adjustcustompercentage.between' => 'Phần trăm điều chỉnh phải nằm trong khoảng -9999.9999 đến 9999.9999',
        ];
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->getValidationRulesForStore(),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        $componentIds = $request->input('componentids', []);
        $createdCount = 0;
        $errors = [];

        foreach ($componentIds as $componentId) {
            $existing = TeacherPayrollComponent::where('teacherid', $request->teacherid)
                ->where('componentid', $componentId)
                ->where('effectivedate', $request->effectivedate)
                ->first();

            if ($existing) {
                $component = PayrollComponent::find($componentId);
                $errors[] = 'Đã tồn tại cấu hình cho thành phần "' . ($component->componentname ?? 'N/A') . '" tại ngày hiệu lực này';
                continue;
            }

            TeacherPayrollComponent::where('teacherid', $request->teacherid)
                ->where('componentid', $componentId)
                ->whereNull('expirationdate')
                ->where('effectivedate', '<', $request->effectivedate)
                ->update([
                    'expirationdate' => date('Y-m-d', strtotime($request->effectivedate . ' -1 day'))
                ]);

            $data = [
                'teacherid' => $request->teacherid,
                'componentid' => $componentId,
                'effectivedate' => $request->effectivedate,
                'expirationdate' => $request->expirationdate,
                'note' => $request->note,
            ];

            $teacher = Teacher::find($request->teacherid);
            $unitId = $teacher ? $teacher->unitid : null;

            $adjustCoefficient = null;
            if ($request->has('adjustcustomcoefficient') && $request->adjustcustomcoefficient !== null && $request->adjustcustomcoefficient !== '' && $request->adjustcustomcoefficient !== '0') {
                $adjustCoefficient = $request->adjustcustomcoefficient;
            } elseif ($unitId) {
                $unitConfig = PayrollComponentUnitConfig::where('unitid', $unitId)
                    ->where('componentid', $componentId)
                    ->where('effectivedate', '<=', $request->effectivedate)
                    ->where(function($query) use ($request) {
                        $query->whereNull('expirationdate')
                              ->orWhere('expirationdate', '>=', $request->effectivedate);
                    })
                    ->orderBy('effectivedate', 'desc')
                    ->first();

                if ($unitConfig && $unitConfig->adjustcoefficient !== null) {
                    $adjustCoefficient = $unitConfig->adjustcoefficient;
                }
            }

            $adjustPercentage = null;
            if ($request->has('adjustcustompercentage') && $request->adjustcustompercentage !== null && $request->adjustcustompercentage !== '' && $request->adjustcustompercentage !== '0') {
                $adjustPercentage = $request->adjustcustompercentage;
            } elseif ($unitId) {
                $unitConfig = PayrollComponentUnitConfig::where('unitid', $unitId)
                    ->where('componentid', $componentId)
                    ->where('effectivedate', '<=', $request->effectivedate)
                    ->where(function($query) use ($request) {
                        $query->whereNull('expirationdate')
                              ->orWhere('expirationdate', '>=', $request->effectivedate);
                    })
                    ->orderBy('effectivedate', 'desc')
                    ->first();

                if ($unitConfig && $unitConfig->adjustpercentage !== null) {
                    $adjustPercentage = $unitConfig->adjustpercentage;
                }
            }

            if ($adjustCoefficient !== null) {
                $data['adjustcustomcoefficient'] = $adjustCoefficient;
            }
            if ($adjustPercentage !== null) {
                $data['adjustcustompercentage'] = $adjustPercentage;
            }

            TeacherPayrollComponent::create($data);

            $createdCount++;
        }

        if (!empty($errors)) {
            return redirect()->route('admin.teacherpayrollcomponent.index')
                ->withErrors(['componentids' => implode(', ', $errors)])
                ->withInput();
        }

        $message = $createdCount > 1 
            ? "Thêm thành công {$createdCount} cấu hình thành phần lương theo giáo viên!" 
            : 'Thêm cấu hình thành phần lương theo giáo viên thành công!';

        return redirect()->route('admin.teacherpayrollcomponent.index')
            ->with('success', $message);
    }

    public function update(Request $request, $id)
    {
        $config = TeacherPayrollComponent::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($config->teachercomponentid),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        $updateData = [
            'teacherid' => $validated['teacherid'],
            'componentid' => $validated['componentid'],
            'effectivedate' => $validated['effectivedate'],
            'expirationdate' => $validated['expirationdate'] ?? null,
            'note' => $validated['note'] ?? null,
        ];

        if (isset($validated['adjustcustomcoefficient']) && $validated['adjustcustomcoefficient'] !== null && $validated['adjustcustomcoefficient'] !== '' && $validated['adjustcustomcoefficient'] !== '0') {
            $updateData['adjustcustomcoefficient'] = $validated['adjustcustomcoefficient'];
        } else {
            $updateData['adjustcustomcoefficient'] = null;
        }

        if (isset($validated['adjustcustompercentage']) && $validated['adjustcustompercentage'] !== null && $validated['adjustcustompercentage'] !== '' && $validated['adjustcustompercentage'] !== '0') {
            $updateData['adjustcustompercentage'] = $validated['adjustcustompercentage'];
        } else {
            $updateData['adjustcustompercentage'] = null;
        }

        $config->update($updateData);

        return redirect()->route('admin.teacherpayrollcomponent.index')
            ->with('success', 'Cập nhật cấu hình thành phần lương theo giáo viên thành công!');
    }

    public function destroy($id)
    {
        $config = TeacherPayrollComponent::findOrFail($id);

        $config->delete();

        return redirect()->route('admin.teacherpayrollcomponent.index')
            ->with('success', 'Xóa cấu hình thành phần lương theo giáo viên thành công!');
    }
}

