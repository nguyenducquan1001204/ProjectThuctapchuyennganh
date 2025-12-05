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
    /**
     * Hiển thị danh sách cấu hình thành phần lương theo giáo viên
     */
    public function index(Request $request)
    {
        $query = TeacherPayrollComponent::with(['component', 'teacher']);

        // Tìm kiếm theo mã cấu hình
        if ($request->filled('search_id')) {
            $query->where('teachercomponentid', $request->search_id);
        }

        // Tìm kiếm theo giáo viên
        if ($request->filled('search_teacherid')) {
            $query->where('teacherid', $request->search_teacherid);
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

        $configs = $query->orderBy('teachercomponentid', 'asc')->get();

        // Lấy danh sách giáo viên và thành phần lương cho dropdown
        $allTeachers = Teacher::orderBy('fullname', 'asc')->get();
        $allComponents = PayrollComponent::orderBy('componentname', 'asc')->get();

        return view('admin.teacherpayrollcomponents.index', compact('configs', 'allTeachers', 'allComponents'));
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
     * Lấy danh sách component đã được cấu hình cho giáo viên (đang hiệu lực) - API
     */
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

    /**
     * Lấy giá trị cơ bản từ cấu hình chung cho component và ngày - API
     */
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

        // Lấy giá trị cơ bản từ cấu hình chung (payrollcomponentconfig)
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

        // Nếu có giáo viên, lấy điều chỉnh từ cấu hình đơn vị (payrollcomponentunitconfig)
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
                    // Lưu giá trị điều chỉnh riêng để trả về
                    $unitAdjustCoefficient = $unitConfig->adjustcoefficient;
                    $unitAdjustPercentage = $unitConfig->adjustpercentage;
                    
                    // Không cộng điều chỉnh vào giá trị cơ bản nữa, chỉ trả về riêng biệt
                }
            }

            // Đặc biệt: Nếu component là "Lương ngạch bậc", lấy từ teacher.currentcoefficient
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
            'unit_adjust_coefficient' => $unitAdjustCoefficient, // Giá trị điều chỉnh từ unit config
            'unit_adjust_percentage' => $unitAdjustPercentage, // Giá trị điều chỉnh từ unit config
            'calculation_method' => $component->calculationmethod,
        ]);
    }

    /**
     * Validation rules cho store (nhiều thành phần)
     */
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

    /**
     * Validation rules cho update (một thành phần)
     */
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

        // Unique constraint: teacherid + componentid + effectivedate
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

    /**
     * Validation messages
     */
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

    /**
     * Lưu cấu hình mới (có thể nhiều thành phần)
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->getValidationRulesForStore(),
            $this->getValidationMessages()
        );

        // Validation cho các trường giá trị riêng đã được bỏ qua - giá trị sẽ lấy từ cấu hình chung hoặc từ giáo viên

        $validated = $validator->validate();

        $componentIds = $request->input('componentids', []);
        $createdCount = 0;
        $errors = [];

        foreach ($componentIds as $componentId) {
            // Kiểm tra xem đã tồn tại cấu hình cho giáo viên + thành phần + ngày hiệu lực này chưa
            $existing = TeacherPayrollComponent::where('teacherid', $request->teacherid)
                ->where('componentid', $componentId)
                ->where('effectivedate', $request->effectivedate)
                ->first();

            if ($existing) {
                $component = PayrollComponent::find($componentId);
                $errors[] = 'Đã tồn tại cấu hình cho thành phần "' . ($component->componentname ?? 'N/A') . '" tại ngày hiệu lực này';
                continue;
            }

            // Tự động đóng cấu hình cũ nếu có (set expirationdate = effectivedate - 1 ngày)
            TeacherPayrollComponent::where('teacherid', $request->teacherid)
                ->where('componentid', $componentId)
                ->whereNull('expirationdate')
                ->where('effectivedate', '<', $request->effectivedate)
                ->update([
                    'expirationdate' => date('Y-m-d', strtotime($request->effectivedate . ' -1 day'))
                ]);

            // Tạo cấu hình mới
            // Chỉ lưu các trường điều chỉnh, không lưu giá trị cơ bản (lấy từ cấu hình chung)
            $data = [
                'teacherid' => $request->teacherid,
                'componentid' => $componentId,
                'effectivedate' => $request->effectivedate,
                'expirationdate' => $request->expirationdate,
                'note' => $request->note,
            ];

            // Lấy giáo viên để có unitid
            $teacher = Teacher::find($request->teacherid);
            $unitId = $teacher ? $teacher->unitid : null;

            // Xử lý điều chỉnh hệ số
            $adjustCoefficient = null;
            if ($request->has('adjustcustomcoefficient') && $request->adjustcustomcoefficient !== null && $request->adjustcustomcoefficient !== '' && $request->adjustcustomcoefficient !== '0') {
                // Nếu có giá trị từ form, sử dụng giá trị đó
                $adjustCoefficient = $request->adjustcustomcoefficient;
            } elseif ($unitId) {
                // Nếu rỗng, lấy từ payrollcomponentunitconfig
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

            // Xử lý điều chỉnh phần trăm
            $adjustPercentage = null;
            if ($request->has('adjustcustompercentage') && $request->adjustcustompercentage !== null && $request->adjustcustompercentage !== '' && $request->adjustcustompercentage !== '0') {
                // Nếu có giá trị từ form, sử dụng giá trị đó
                $adjustPercentage = $request->adjustcustompercentage;
            } elseif ($unitId) {
                // Nếu rỗng, lấy từ payrollcomponentunitconfig
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

            // Chỉ thêm vào data nếu có giá trị
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

    /**
     * Cập nhật cấu hình
     */
    public function update(Request $request, $id)
    {
        $config = TeacherPayrollComponent::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($config->teachercomponentid),
            $this->getValidationMessages()
        );

        // Validation cho các trường giá trị riêng đã được bỏ qua - giá trị sẽ lấy từ cấu hình chung hoặc từ giáo viên

        $validated = $validator->validate();

        // Chỉ cập nhật các trường tồn tại trong bảng
        $updateData = [
            'teacherid' => $validated['teacherid'],
            'componentid' => $validated['componentid'],
            'effectivedate' => $validated['effectivedate'],
            'expirationdate' => $validated['expirationdate'] ?? null,
            'note' => $validated['note'] ?? null,
        ];

        // Chỉ thêm điều chỉnh hệ số nếu có giá trị
        if (isset($validated['adjustcustomcoefficient']) && $validated['adjustcustomcoefficient'] !== null && $validated['adjustcustomcoefficient'] !== '' && $validated['adjustcustomcoefficient'] !== '0') {
            $updateData['adjustcustomcoefficient'] = $validated['adjustcustomcoefficient'];
        } else {
            $updateData['adjustcustomcoefficient'] = null;
        }

        // Chỉ thêm điều chỉnh phần trăm nếu có giá trị
        if (isset($validated['adjustcustompercentage']) && $validated['adjustcustompercentage'] !== null && $validated['adjustcustompercentage'] !== '' && $validated['adjustcustompercentage'] !== '0') {
            $updateData['adjustcustompercentage'] = $validated['adjustcustompercentage'];
        } else {
            $updateData['adjustcustompercentage'] = null;
        }

        $config->update($updateData);

        return redirect()->route('admin.teacherpayrollcomponent.index')
            ->with('success', 'Cập nhật cấu hình thành phần lương theo giáo viên thành công!');
    }

    /**
     * Xóa cấu hình
     */
    public function destroy($id)
    {
        $config = TeacherPayrollComponent::findOrFail($id);

        // TODO: Kiểm tra quan hệ với bảng payroll trước khi cho phép xóa

        $config->delete();

        return redirect()->route('admin.teacherpayrollcomponent.index')
            ->with('success', 'Xóa cấu hình thành phần lương theo giáo viên thành công!');
    }
}

