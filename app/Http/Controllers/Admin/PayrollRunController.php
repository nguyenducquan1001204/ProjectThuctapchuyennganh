<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollRun;
use App\Models\PayrollRunDetail;
use App\Models\PayrollRunDetailComponent;
use App\Models\BudgetSpendingUnit;
use App\Models\BaseSalary;
use App\Models\Teacher;
use App\Models\TeacherPayrollComponent;
use App\Models\PayrollComponent;
use App\Models\PayrollComponentConfig;
use App\Models\PayrollComponentUnitConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PayrollRunController extends Controller
{
    /**
     * Hiển thị danh sách bảng lương theo kỳ
     */
    public function index(Request $request)
    {
        $query = PayrollRun::with(['unit', 'baseSalary']);

        // Tìm kiếm theo mã bảng lương
        if ($request->filled('search_id')) {
            $query->where('payrollrunid', $request->search_id);
        }

        // Tìm kiếm theo đơn vị
        if ($request->filled('search_unitid')) {
            $query->where('unitid', $request->search_unitid);
        }

        // Tìm kiếm theo kỳ lương
        if ($request->filled('search_payrollperiod')) {
            $query->where('payrollperiod', $request->search_payrollperiod);
        }

        // Tìm kiếm theo trạng thái
        if ($request->filled('search_status')) {
            $query->where('status', $request->search_status);
        }

        // Tìm kiếm theo ngày tạo
        if ($request->filled('search_createdat')) {
            $query->whereDate('createdat', $request->search_createdat);
        }

        $payrollRuns = $query->orderBy('payrollrunid', 'desc')->get();

        // Lấy danh sách đơn vị cho dropdown
        $allUnits = BudgetSpendingUnit::orderBy('unitname', 'asc')->get();

        return view('admin.payrollruns.index', compact('payrollRuns', 'allUnits'));
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
            'basesalaryid' => [
                'required',
                'integer',
                'exists:basesalary,basesalaryid',
            ],
            'payrollperiod' => [
                'required',
                'string',
                'regex:/^\d{4}-\d{2}$/',
            ],
            'status' => [
                'required',
                'in:draft,calculating,approved,paid',
            ],
            'note' => 'nullable|string|max:65535',
        ];

        // Unique constraint: unitid + payrollperiod
        if ($ignoreId) {
            $rules['payrollperiod'][] = Rule::unique('payrollrun', 'payrollperiod')
                ->where('unitid', request('unitid'))
                ->ignore($ignoreId, 'payrollrunid');
        } else {
            $rules['payrollperiod'][] = Rule::unique('payrollrun', 'payrollperiod')
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
            'unitid.required' => 'Đơn vị là bắt buộc',
            'unitid.integer' => 'Đơn vị không hợp lệ',
            'unitid.exists' => 'Đơn vị không tồn tại',
            'basesalaryid.required' => 'Mức lương cơ bản là bắt buộc',
            'basesalaryid.integer' => 'Mức lương cơ bản không hợp lệ',
            'basesalaryid.exists' => 'Mức lương cơ bản không tồn tại',
            'payrollperiod.required' => 'Kỳ lương là bắt buộc',
            'payrollperiod.regex' => 'Kỳ lương phải có định dạng YYYY-MM (ví dụ: 2025-11)',
            'payrollperiod.unique' => 'Đã tồn tại bảng lương cho đơn vị này trong kỳ lương này',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
        ];
    }

    /**
     * Lấy danh sách mức lương cơ bản theo đơn vị
     */
    public function getBaseSalariesByUnit($unitId)
    {
        $baseSalaries = BaseSalary::where('unitid', $unitId)
            ->where(function($query) {
                $query->whereNull('expirationdate')
                      ->orWhere('expirationdate', '>=', now());
            })
            ->orderBy('effectivedate', 'desc')
            ->get();

        return response()->json($baseSalaries);
    }

    /**
     * Lưu bảng lương mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        // Chuyển đổi unitid và basesalaryid
        $validated['unitid'] = (int)$validated['unitid'];
        $validated['basesalaryid'] = (int)$validated['basesalaryid'];

        // Set createdat nếu chưa có
        if (!isset($validated['createdat'])) {
            $validated['createdat'] = Carbon::now();
        }

        // Nếu status là 'approved', set approvedat
        if ($validated['status'] === 'approved' && !isset($validated['approvedat'])) {
            $validated['approvedat'] = Carbon::now();
        }

        PayrollRun::create($validated);

        return redirect()->route('admin.payrollrun.index')
            ->with('success', 'Thêm bảng lương theo kỳ thành công!');
    }

    /**
     * Cập nhật bảng lương
     */
    public function update(Request $request, $id)
    {
        $payrollRun = PayrollRun::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($payrollRun->payrollrunid),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        // Chuyển đổi unitid và basesalaryid
        $validated['unitid'] = (int)$validated['unitid'];
        $validated['basesalaryid'] = (int)$validated['basesalaryid'];

        // Xử lý approvedat: nếu chuyển từ trạng thái khác sang 'approved', set approvedat
        if ($validated['status'] === 'approved' && $payrollRun->status !== 'approved') {
            $validated['approvedat'] = Carbon::now();
        } elseif ($validated['status'] !== 'approved') {
            // Nếu chuyển từ 'approved' sang trạng thái khác, giữ nguyên approvedat
            $validated['approvedat'] = $payrollRun->approvedat;
        }

        $payrollRun->update($validated);

        return redirect()->route('admin.payrollrun.index')
            ->with('success', 'Cập nhật bảng lương theo kỳ thành công!');
    }

    /**
     * Xóa bảng lương
     */
    public function destroy($id)
    {
        $payrollRun = PayrollRun::findOrFail($id);

        // Kiểm tra trạng thái: chỉ cho phép xóa nếu ở trạng thái 'draft'
        if ($payrollRun->status !== 'draft') {
            return redirect()->route('admin.payrollrun.index')
                ->with('error', 'Chỉ có thể xóa bảng lương ở trạng thái "Khởi tạo"!');
        }

        // TODO: Kiểm tra quan hệ với bảng payroll detail trước khi cho phép xóa

        $payrollRun->delete();

        return redirect()->route('admin.payrollrun.index')
            ->with('success', 'Xóa bảng lương theo kỳ thành công!');
    }

    /**
     * Preview dữ liệu trước khi tính lương
     */
    public function preview($id)
    {
        try {
            $payrollRun = PayrollRun::with(['unit', 'baseSalary'])->findOrFail($id);

            // Lấy ngày hiệu lực
            $payrollPeriod = $payrollRun->payrollperiod;
            $periodStart = $payrollPeriod . '-01';
            $periodEnd = Carbon::parse($periodStart)->endOfMonth()->format('Y-m-d');

            $baseSalary = $payrollRun->baseSalary;
            $baseSalaryAmount = $baseSalary ? $baseSalary->basesalaryamount : 0;

            // Lấy danh sách giáo viên
            $teacherIds = TeacherPayrollComponent::where('effectivedate', '<=', $periodEnd)
                ->where(function($query) use ($periodStart) {
                    $query->whereNull('expirationdate')
                          ->orWhere('expirationdate', '>=', $periodStart);
                })
                ->whereHas('teacher', function($query) use ($payrollRun) {
                    $query->where('unitid', $payrollRun->unitid);
                })
                ->distinct()
                ->pluck('teacherid')
                ->toArray();

            if (empty($teacherIds)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Không có giáo viên nào có cấu hình thành phần lương trong đơn vị này!',
                ], 400);
            }

            $teachers = Teacher::whereIn('teacherid', $teacherIds)->get();

            $previewData = [];
            foreach ($teachers as $teacher) {
                try {
                    $teacherCoefficient = $teacher->currentcoefficient ?? 0;
                    
                    // Lấy các thành phần lương
                    $teacherComponents = TeacherPayrollComponent::where('teacherid', $teacher->teacherid)
                        ->where('effectivedate', '<=', $periodEnd)
                        ->where(function($query) use ($periodStart) {
                            $query->whereNull('expirationdate')
                                  ->orWhere('expirationdate', '>=', $periodStart);
                        })
                        ->with('component')
                        ->get();

                    $components = [];
                    foreach ($teacherComponents as $tc) {
                        try {
                            $component = $tc->component;
                            if (!$component) continue;

                            $value = $this->getComponentValue($teacher, $tc->componentid, $periodEnd, $tc);
                            
                            $components[] = [
                                'component_id' => $tc->componentid,
                                'component_name' => $component->componentname,
                                'calculation_method' => $component->calculationmethod,
                                'coefficient' => $value['coefficient'] ?? 0,
                                'percentage' => $value['percentage'] ?? 0,
                                'fixed' => $value['fixed'] ?? 0,
                                'adjustcustomcoefficient' => $tc->adjustcustomcoefficient,
                                'adjustcustompercentage' => $tc->adjustcustompercentage,
                            ];
                        } catch (\Exception $e) {
                            // Bỏ qua component lỗi, tiếp tục với component khác
                            continue;
                        }
                    }

                    $previewData[] = [
                        'teacher_id' => $teacher->teacherid,
                        'teacher_name' => $teacher->fullname,
                        'teacher_coefficient' => $teacherCoefficient,
                        'components' => $components,
                    ];
                } catch (\Exception $e) {
                    // Bỏ qua giáo viên lỗi, tiếp tục với giáo viên khác
                    continue;
                }
            }

            return response()->json([
                'success' => true,
                'payroll_run' => [
                    'id' => $payrollRun->payrollrunid,
                    'unit_name' => $payrollRun->unit ? $payrollRun->unit->unitname : '',
                    'payroll_period' => $payrollRun->payrollperiod,
                    'base_salary' => $baseSalaryAmount,
                ],
                'teachers' => $previewData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Tính lương tự động cho bảng lương
     */
    public function calculate($id)
    {
        $payrollRun = PayrollRun::with(['unit', 'baseSalary'])->findOrFail($id);

        // Kiểm tra trạng thái: chỉ cho phép tính nếu ở trạng thái 'draft' hoặc 'calculating'
        if (!in_array($payrollRun->status, ['draft', 'calculating'])) {
            return redirect()->route('admin.payrollrun.index')
                ->with('error', 'Chỉ có thể tính lương cho bảng lương ở trạng thái "Khởi tạo" hoặc "Đang tính toán"!');
        }

        // Cập nhật trạng thái thành 'calculating'
        $payrollRun->update(['status' => 'calculating']);

        try {
            DB::beginTransaction();

            // Lấy ngày hiệu lực (ngày đầu và cuối tháng của kỳ lương)
            $payrollPeriod = $payrollRun->payrollperiod; // Format: YYYY-MM
            $periodStart = $payrollPeriod . '-01'; // Ngày đầu tháng
            $periodEnd = Carbon::parse($periodStart)->endOfMonth()->format('Y-m-d'); // Ngày cuối tháng

            // Lấy mức lương cơ bản
            $baseSalary = $payrollRun->baseSalary;
            $baseSalaryAmount = $baseSalary ? $baseSalary->basesalaryamount : 0;

            if ($baseSalaryAmount <= 0) {
                throw new \Exception('Mức lương cơ bản không hợp lệ!');
            }

            // Lấy danh sách giáo viên có trong teacherpayrollcomponent hiệu lực trong tháng
            // Component hiệu lực nếu: effectivedate <= ngày cuối tháng VÀ (expirationdate null HOẶC expirationdate >= ngày đầu tháng)
            $teacherIds = TeacherPayrollComponent::where('effectivedate', '<=', $periodEnd)
                ->where(function($query) use ($periodStart) {
                    $query->whereNull('expirationdate')
                          ->orWhere('expirationdate', '>=', $periodStart);
                })
                ->whereHas('teacher', function($query) use ($payrollRun) {
                    $query->where('unitid', $payrollRun->unitid);
                })
                ->distinct()
                ->pluck('teacherid')
                ->toArray();

            if (empty($teacherIds)) {
                throw new \Exception('Không có giáo viên nào có cấu hình thành phần lương trong đơn vị này!');
            }

            // Lấy thông tin giáo viên
            $teachers = Teacher::whereIn('teacherid', $teacherIds)->get();

            if ($teachers->isEmpty()) {
                throw new \Exception('Không tìm thấy thông tin giáo viên!');
            }

            $calculatedCount = 0;
            $errors = [];

            foreach ($teachers as $teacher) {
                try {
                    // Tính lương cho từng giáo viên (sử dụng ngày cuối tháng để lấy component hiệu lực)
                    $result = $this->calculateTeacherPayroll($teacher, $payrollRun, $periodEnd, $baseSalaryAmount);
                    
                    if ($result['success']) {
                        $calculatedCount++;
                    } else {
                        $errors[] = "Giáo viên {$teacher->fullname}: {$result['error']}";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Giáo viên {$teacher->fullname}: {$e->getMessage()}";
                }
            }

            // Cập nhật trạng thái về 'draft' sau khi tính xong
            $payrollRun->update(['status' => 'draft']);

            DB::commit();

            $message = "Tính lương tự động thành công cho {$calculatedCount} giáo viên!";
            if (!empty($errors)) {
                $message .= "\nLỗi: " . implode('; ', $errors);
            }

            return redirect()->route('admin.payrollrun.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            // Cập nhật trạng thái về 'draft' nếu có lỗi
            $payrollRun->update(['status' => 'draft']);

            return redirect()->route('admin.payrollrun.index')
                ->with('error', 'Lỗi khi tính lương: ' . $e->getMessage());
        }
    }

    /**
     * Tính lương cho một giáo viên
     * @param bool $returnDetails Nếu true, trả về chi tiết tính toán để xuất file
     */
    private function calculateTeacherPayroll($teacher, $payrollRun, $effectiveDate, $baseSalaryAmount, $returnDetails = false)
    {
        // Lấy hệ số lương từ teacher
        $teacherCoefficient = $teacher->currentcoefficient ?? 0;

        if ($teacherCoefficient <= 0) {
            return ['success' => false, 'error' => 'Hệ số lương không hợp lệ'];
        }

        // Lấy các thành phần lương của giáo viên hiệu lực trong tháng
        // Sử dụng ngày cuối tháng để lấy tất cả component có hiệu lực trong tháng
        $periodStart = Carbon::parse($effectiveDate)->startOfMonth()->format('Y-m-d');
        $periodEnd = Carbon::parse($effectiveDate)->endOfMonth()->format('Y-m-d');
        
        $teacherComponents = TeacherPayrollComponent::where('teacherid', $teacher->teacherid)
            ->where('effectivedate', '<=', $periodEnd)
            ->where(function($query) use ($periodStart) {
                $query->whereNull('expirationdate')
                      ->orWhere('expirationdate', '>=', $periodStart);
            })
            ->with('component')
            ->get();

        // Tạo map componentid => giá trị
        $componentValues = [];
        foreach ($teacherComponents as $tc) {
            $componentId = $tc->componentid;
            $component = $tc->component;
            
            if (!$component) continue;

            // Lấy giá trị từ config và unit config
            $value = $this->getComponentValue($teacher, $componentId, $effectiveDate, $tc);
            $componentValues[$componentId] = [
                'component' => $component,
                'value' => $value,
                'teacherComponent' => $tc
            ];
        }

        // Tìm các thành phần cần thiết theo tên
        $phuCapChucVu = $this->findComponentByName($componentValues, ['Phụ cấp chức vụ', 'phụ cấp chức vụ']);
        $phuCapVuotKhung = $this->findComponentByName($componentValues, ['Phụ cấp vượt khung', 'phụ cấp vượt khung']);
        $phuCapUuDai = $this->findComponentByName($componentValues, ['Phụ cấp ưu đãi', 'phụ cấp ưu đãi']);
        $phuCapThamNien = $this->findComponentByName($componentValues, ['Phụ cấp thâm niên', 'phụ cấp thâm niên']);
        $phuCapTrachNhiem = $this->findComponentByName($componentValues, ['Phụ cấp trách nhiệm', 'phụ cấp trách nhiệm']);
        $phuCapDocHai = $this->findComponentByName($componentValues, ['Phụ cấp độc hại', 'phụ cấp độc hại']);

        // Lấy giá trị HỆ SỐ từ teacherpayrollcomponent cho các phụ cấp hệ số
        $phuCapChucVuHeSo = $phuCapChucVu ? $phuCapChucVu['value']['coefficient'] : 0;
        $phuCapVuotKhungHeSo = $phuCapVuotKhung ? $phuCapVuotKhung['value']['coefficient'] : 0;
        $phuCapTrachNhiemHeSo = $phuCapTrachNhiem ? $phuCapTrachNhiem['value']['coefficient'] : 0;
        $phuCapDocHaiHeSo = $phuCapDocHai ? $phuCapDocHai['value']['coefficient'] : 0;

        // Lấy giá trị TỶ LỆ từ teacherpayrollcomponent cho các phụ cấp tỷ lệ
        $phuCapUuDaiTyLe = $phuCapUuDai ? ($phuCapUuDai['value']['percentage'] ?? 0) : 0;
        $phuCapThamNienTyLe = $phuCapThamNien ? ($phuCapThamNien['value']['percentage'] ?? 0) : 0;

        // Tính Hệ số phụ cấp
        // Công thức: {Hệ số lương + Phụ cấp chức vụ(Hệ số) + Phụ cấp vượt khung(Hệ số)} * Phụ cấp ưu đãi(Tỷ lệ)
        $heSoPhuCap = 0;
        if ($phuCapUuDai) {
            $tongHeSoChoPhuCap = $teacherCoefficient + $phuCapChucVuHeSo + $phuCapVuotKhungHeSo;
            // Tỷ lệ phụ cấp ưu đãi: nếu >= 1 thì chia 100 (ví dụ 30 = 30% = 0.3), nếu < 1 thì chia 10 (ví dụ 0.5 = 0.5% = 0.05)
            $tyLeValue = $phuCapUuDaiTyLe >= 1 ? $phuCapUuDaiTyLe / 100 : $phuCapUuDaiTyLe / 10;
            $heSoPhuCap = $tongHeSoChoPhuCap * $tyLeValue;
        }

        // Tính Hệ số phụ cấp thâm niên
        // Công thức: {Hệ số lương + Phụ cấp chức vụ(Hệ số) + Phụ cấp vượt khung(Hệ số)} * Phụ cấp thâm niên(Tỷ lệ)
        $heSoPhuCapThamNien = 0;
        if ($phuCapThamNien) {
            $tongHeSoChoPhuCap = $teacherCoefficient + $phuCapChucVuHeSo + $phuCapVuotKhungHeSo;
            // Tỷ lệ phụ cấp thâm niên: nếu >= 1 thì chia 100 (ví dụ 29 = 29% = 0.29), nếu < 1 thì chia 10 (ví dụ 0.5 = 0.5% = 0.05)
            $tyLeValue = $phuCapThamNienTyLe >= 1 ? $phuCapThamNienTyLe / 100 : $phuCapThamNienTyLe / 10;
            $heSoPhuCapThamNien = $tongHeSoChoPhuCap * $tyLeValue;
        }

        // Tính Tổng hệ số
        // Công thức: Hệ số lương + Phụ cấp chức vụ(Hệ số) + Phụ cấp vượt khung(Hệ số) + Phụ cấp trách nhiệm(Hệ số) + Phụ cấp độc hại(Hệ số) + Hệ số phụ cấp + Hệ số phụ cấp thâm niên
        $tongHeSo = $teacherCoefficient
            + $phuCapChucVuHeSo
            + $phuCapVuotKhungHeSo
            + $phuCapTrachNhiemHeSo
            + $phuCapDocHaiHeSo
            + $heSoPhuCap
            + $heSoPhuCapThamNien;

        // Tính Quỹ lương phụ cấp 01 tháng
        // Công thức: Tổng hệ số * Mức lương cơ bản
        $quyLuongPhuCap = $tongHeSo * $baseSalaryAmount;

        // Tính các khoản trừ (10.5%)
        // Tổng hệ số để trừ = Hệ số lương + Phụ cấp chức vụ(Hệ số) + Phụ cấp vượt khung(Hệ số) + Hệ số phụ cấp thâm niên
        $tongHeSoChoTru = $teacherCoefficient
            + $phuCapChucVuHeSo
            + $phuCapVuotKhungHeSo
            + $heSoPhuCapThamNien;
        $quyLuongChoTru = $tongHeSoChoTru * $baseSalaryAmount;

        $bhxhNhanVien = $this->findComponentByName($componentValues, ['BHXH nhân viên', 'bhxh nhân viên']);
        $bhytNhanVien = $this->findComponentByName($componentValues, ['BHYT nhân viên', 'bhyt nhân viên']);
        $bhtnNhanVien = $this->findComponentByName($componentValues, ['BHTN nhân viên', 'bhtn nhân viên']);

        // Tính các khoản trừ: nếu >= 1 thì chia 100 (ví dụ 8 = 8% = 0.08), nếu < 1 thì chia 10 (ví dụ 0.5 = 0.5% = 0.05)
        $bhxhPercentageRaw = $bhxhNhanVien ? ($bhxhNhanVien['value']['percentage'] ?? 0) : 0;
        $bhxhPercentage = $bhxhPercentageRaw >= 1 ? $bhxhPercentageRaw / 100 : $bhxhPercentageRaw / 10;
        $tienTruBHXH = $quyLuongChoTru * $bhxhPercentage;

        $bhytPercentageRaw = $bhytNhanVien ? ($bhytNhanVien['value']['percentage'] ?? 0) : 0;
        $bhytPercentage = $bhytPercentageRaw >= 1 ? $bhytPercentageRaw / 100 : $bhytPercentageRaw / 10;
        $tienTruBHYT = $quyLuongChoTru * $bhytPercentage;

        $bhtnPercentageRaw = $bhtnNhanVien ? ($bhtnNhanVien['value']['percentage'] ?? 0) : 0;
        $bhtnPercentage = $bhtnPercentageRaw >= 1 ? $bhtnPercentageRaw / 100 : $bhtnPercentageRaw / 10;
        $tienTruBHTN = $quyLuongChoTru * $bhtnPercentage;

        $tongTruNhanVien = $tienTruBHXH + $tienTruBHYT + $tienTruBHTN;

        $thucLinh = $quyLuongPhuCap - $tongTruNhanVien;

        $bhxhDonVi = $this->findComponentByName($componentValues, ['BHXH đơn vị', 'bhxh đơn vị']);
        $bhytDonVi = $this->findComponentByName($componentValues, ['BHYT đơn vị', 'bhyt đơn vị']);
        $bhtnDonVi = $this->findComponentByName($componentValues, ['BHTN đơn vị', 'bhtn đơn vị']);
        $bhtnTuNganSach = $this->findComponentByName($componentValues, ['BHTN từ ngân sách', 'bhtn từ ngân sách']);

        // Tính các khoản ngân sách: nếu >= 1 thì chia 100 (ví dụ 17 = 17% = 0.17), nếu < 1 thì chia 10 (ví dụ 0.5 = 0.5% = 0.05)
        $bhxhDonViPercentageRaw = $bhxhDonVi ? ($bhxhDonVi['value']['percentage'] ?? 0) : 0;
        $bhxhDonViPercentage = $bhxhDonViPercentageRaw >= 1 ? $bhxhDonViPercentageRaw / 100 : $bhxhDonViPercentageRaw / 10;
        $nganSachBHXH = $quyLuongChoTru * $bhxhDonViPercentage;

        $bhytDonViPercentageRaw = $bhytDonVi ? ($bhytDonVi['value']['percentage'] ?? 0) : 0;
        $bhytDonViPercentage = $bhytDonViPercentageRaw >= 1 ? $bhytDonViPercentageRaw / 100 : $bhytDonViPercentageRaw / 10;
        $nganSachBHYT = $quyLuongChoTru * $bhytDonViPercentage;

        $bhtnDonViPercentageRaw = $bhtnDonVi ? ($bhtnDonVi['value']['percentage'] ?? 0) : 0;
        $bhtnDonViPercentage = $bhtnDonViPercentageRaw >= 1 ? $bhtnDonViPercentageRaw / 100 : $bhtnDonViPercentageRaw / 10;
        $nganSachBHTN = $quyLuongChoTru * $bhtnDonViPercentage;

        $bhtnTuNSPercentageRaw = $bhtnTuNganSach ? ($bhtnTuNganSach['value']['percentage'] ?? 0) : 0;
        $bhtnTuNSPercentage = $bhtnTuNSPercentageRaw >= 1 ? $bhtnTuNSPercentageRaw / 100 : $bhtnTuNSPercentageRaw / 10;
        $nganSachBHTNTuNS = $quyLuongChoTru * $bhtnTuNSPercentage;

        $tongNganSach = $nganSachBHXH + $nganSachBHYT + $nganSachBHTN + $nganSachBHTNTuNS;

        // Tính tổng chi phí
        $tongChiPhi = $quyLuongPhuCap + $tongNganSach;

        // Kiểm tra xem đã có bản ghi payrollrundetail chưa
        $existingDetail = PayrollRunDetail::where('payrollrunid', $payrollRun->payrollrunid)
            ->where('teacherid', $teacher->teacherid)
            ->first();

        if ($existingDetail) {
            // Cập nhật bản ghi hiện có
            $existingDetail->update([
                'totalincome' => $quyLuongPhuCap,
                'totalemployeedeductions' => $tongTruNhanVien,
                'totalemployercontributions' => $tongNganSach,
                'netpay' => $thucLinh,
                'totalcost' => $tongChiPhi,
            ]);
            $detail = $existingDetail;
        } else {
            // Tạo bản ghi mới
            $detail = PayrollRunDetail::create([
                'payrollrunid' => $payrollRun->payrollrunid,
                'teacherid' => $teacher->teacherid,
                'totalincome' => $quyLuongPhuCap,
                'totalemployeedeductions' => $tongTruNhanVien,
                'totalemployercontributions' => $tongNganSach,
                'netpay' => $thucLinh,
                'totalcost' => $tongChiPhi,
            ]);
        }

        // Xóa các bản ghi component cũ
        PayrollRunDetailComponent::where('detailid', $detail->detailid)->delete();

        // Lưu tất cả các thành phần lương của giáo viên vào payrollrundetailcomponent
        foreach ($componentValues as $componentId => $data) {
            $component = $data['component'];
            $value = $data['value'];
            
            // Xác định loại tính toán
            $calculationMethod = $component->calculationmethod ?? '';
            $calculatedAmount = 0;
            $appliedCoefficient = null;
            $appliedPercentage = null;

            if ($calculationMethod === 'Hệ số' || stripos($calculationMethod, 'hệ số') !== false) {
                // Tính theo hệ số
                $appliedCoefficient = $value['coefficient'];
                $calculatedAmount = $appliedCoefficient * $baseSalaryAmount;
            } elseif ($calculationMethod === 'Phần trăm' || stripos($calculationMethod, 'phần trăm') !== false) {
                // Tính theo phần trăm: nếu >= 1 thì chia 100 (ví dụ 8 = 8% = 0.08), nếu < 1 thì chia 10 (ví dụ 0.5 = 0.5% = 0.05)
                $appliedPercentage = $value['percentage'];
                $percentageValue = $appliedPercentage >= 1 ? $appliedPercentage / 100 : $appliedPercentage / 10;
                
                // Tính số tiền dựa trên quỹ lương để trừ hoặc ngân sách
                // Nếu là khoản trừ hoặc ngân sách, tính dựa trên quyLuongChoTru
                $componentName = strtolower($component->componentname ?? '');
                if (stripos($componentName, 'bhxh') !== false || 
                    stripos($componentName, 'bhyt') !== false || 
                    stripos($componentName, 'bhtn') !== false) {
                    $calculatedAmount = $quyLuongChoTru * $percentageValue;
                } else {
                    // Các thành phần khác tính dựa trên quỹ lương phụ cấp
                    $calculatedAmount = $quyLuongPhuCap * $percentageValue;
                }
            } elseif ($calculationMethod === 'Số tiền cố định' || stripos($calculationMethod, 'số tiền') !== false) {
                // Tính theo số tiền cố định
                $calculatedAmount = $value['fixed'];
            }

            // Lưu vào database
            PayrollRunDetailComponent::create([
                'detailid' => $detail->detailid,
                'componentid' => $componentId,
                'appliedcoefficient' => $appliedCoefficient,
                'appliedpercentage' => $appliedPercentage,
                'calculatedamount' => $calculatedAmount,
            ]);
        }

        // Lưu Quỹ lương phụ cấp như một component đặc biệt (nếu cần)
        // Hoặc có thể tính từ tổng các component

        if ($returnDetails) {
            return [
                'success' => true,
                'details' => [
                    'teacher_name' => $teacher->fullname,
                    'teacher_id' => $teacher->teacherid,
                    'teacher_coefficient' => $teacherCoefficient,
                    'base_salary' => $baseSalaryAmount,
                    'phu_cap_chuc_vu' => $phuCapChucVu ? $phuCapChucVu['value']['coefficient'] : 0,
                    'phu_cap_vuot_khung' => $phuCapVuotKhung ? $phuCapVuotKhung['value']['coefficient'] : 0,
                    'phu_cap_trach_nhiem' => $phuCapTrachNhiem ? $phuCapTrachNhiem['value']['coefficient'] : 0,
                    'phu_cap_doc_hai' => $phuCapDocHai ? $phuCapDocHai['value']['coefficient'] : 0,
                    'phu_cap_uu_dai_percentage' => $phuCapUuDai ? ($phuCapUuDai['value']['percentage'] ?? 0) : 0,
                    'phu_cap_tham_nien_percentage' => $phuCapThamNien ? ($phuCapThamNien['value']['percentage'] ?? 0) : 0,
                    'he_so_phu_cap' => $heSoPhuCap,
                    'he_so_phu_cap_tham_nien' => $heSoPhuCapThamNien,
                    'tong_he_so' => $tongHeSo,
                    'quy_luong_phu_cap' => $quyLuongPhuCap,
                    'tong_he_so_cho_tru' => $tongHeSoChoTru,
                    'quy_luong_cho_tru' => $quyLuongChoTru,
                    'bhxh_nhan_vien_percentage' => $bhxhNhanVien ? ($bhxhNhanVien['value']['percentage'] ?? 0) : 0,
                    'bhyt_nhan_vien_percentage' => $bhytNhanVien ? ($bhytNhanVien['value']['percentage'] ?? 0) : 0,
                    'bhtn_nhan_vien_percentage' => $bhtnNhanVien ? ($bhtnNhanVien['value']['percentage'] ?? 0) : 0,
                    'tien_tru_bhxh' => $tienTruBHXH,
                    'tien_tru_bhyt' => $tienTruBHYT,
                    'tien_tru_bhtn' => $tienTruBHTN,
                    'tong_tru_nhan_vien' => $tongTruNhanVien,
                    'thuc_linh' => $thucLinh,
                    'bhxh_don_vi_percentage' => $bhxhDonVi ? ($bhxhDonVi['value']['percentage'] ?? 0) : 0,
                    'bhyt_don_vi_percentage' => $bhytDonVi ? ($bhytDonVi['value']['percentage'] ?? 0) : 0,
                    'bhtn_don_vi_percentage' => $bhtnDonVi ? ($bhtnDonVi['value']['percentage'] ?? 0) : 0,
                    'bhtn_tu_ngan_sach_percentage' => $bhtnTuNganSach ? ($bhtnTuNganSach['value']['percentage'] ?? 0) : 0,
                    'ngan_sach_bhxh' => $nganSachBHXH,
                    'ngan_sach_bhyt' => $nganSachBHYT,
                    'ngan_sach_bhtn' => $nganSachBHTN,
                    'ngan_sach_bhtn_tu_ns' => $nganSachBHTNTuNS,
                    'tong_ngan_sach' => $tongNganSach,
                    'tong_chi_phi' => $tongChiPhi,
                    'components' => $componentValues
                ]
            ];
        }

        return ['success' => true];
    }

    /**
     * Xuất file chi tiết tính toán lương
     */
    public function exportCalculationDetails($id)
    {
        $payrollRun = PayrollRun::with(['unit', 'baseSalary'])->findOrFail($id);

        // Lấy ngày hiệu lực
        $payrollPeriod = $payrollRun->payrollperiod;
        $periodEnd = $payrollPeriod . '-01';
        $periodEnd = Carbon::parse($periodEnd)->endOfMonth()->format('Y-m-d');

        $baseSalary = $payrollRun->baseSalary;
        $baseSalaryAmount = $baseSalary ? $baseSalary->basesalaryamount : 0;

        // Lấy danh sách giáo viên
        $teacherIds = TeacherPayrollComponent::where('effectivedate', '<=', $periodEnd)
            ->where(function($query) use ($payrollPeriod) {
                $periodStart = $payrollPeriod . '-01';
                $query->whereNull('expirationdate')
                      ->orWhere('expirationdate', '>=', $periodStart);
            })
            ->whereHas('teacher', function($query) use ($payrollRun) {
                $query->where('unitid', $payrollRun->unitid);
            })
            ->distinct()
            ->pluck('teacherid')
            ->toArray();

        $teachers = Teacher::whereIn('teacherid', $teacherIds)->get();

        // Tính toán và lấy chi tiết
        $calculationDetails = [];
        foreach ($teachers as $teacher) {
            $result = $this->calculateTeacherPayroll($teacher, $payrollRun, $periodEnd, $baseSalaryAmount, true);
            if ($result['success'] && isset($result['details'])) {
                $calculationDetails[] = $result['details'];
            }
        }

        // Tạo file CSV
        $filename = 'Chi_tiet_tinh_luong_' . $payrollRun->payrollperiod . '_' . date('YmdHis') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        // Thêm BOM UTF-8 để Excel hiển thị tiếng Việt đúng
        $output = "\xEF\xBB\xBF";

        // Header
        $output .= "CHI TIẾT TÍNH TOÁN LƯƠNG - KỲ: {$payrollRun->payrollperiod}\n";
        $output .= "Đơn vị: " . ($payrollRun->unit ? $payrollRun->unit->unitname : '') . "\n";
        $output .= "Mức lương cơ bản: " . number_format($baseSalaryAmount, 0, ',', '.') . " đ\n";
        $output .= "\n";

        foreach ($calculationDetails as $detail) {
            $output .= "═══════════════════════════════════════════════════════════════\n";
            $output .= "GIÁO VIÊN: {$detail['teacher_name']} (Mã: {$detail['teacher_id']})\n";
            $output .= "═══════════════════════════════════════════════════════════════\n\n";

            // Thông tin cơ bản
            $output .= "1. THÔNG TIN CƠ BẢN:\n";
            $output .= "   - Hệ số lương: " . number_format($detail['teacher_coefficient'], 4, ',', '.') . "\n";
            $output .= "   - Mức lương cơ bản: " . number_format($detail['base_salary'], 0, ',', '.') . " đ\n\n";

            // Các phụ cấp hệ số
            $output .= "2. CÁC PHỤ CẤP (HỆ SỐ):\n";
            $output .= "   - Phụ cấp chức vụ: " . number_format($detail['phu_cap_chuc_vu'], 4, ',', '.') . "\n";
            $output .= "   - Phụ cấp vượt khung: " . number_format($detail['phu_cap_vuot_khung'], 4, ',', '.') . "\n";
            $output .= "   - Phụ cấp trách nhiệm: " . number_format($detail['phu_cap_trach_nhiem'], 4, ',', '.') . "\n";
            $output .= "   - Phụ cấp độc hại: " . number_format($detail['phu_cap_doc_hai'], 4, ',', '.') . "\n\n";

            // Tính hệ số phụ cấp
            $output .= "3. TÍNH HỆ SỐ PHỤ CẤP:\n";
            $tongHeSoChoPhuCap = $detail['teacher_coefficient'] + $detail['phu_cap_chuc_vu'] + $detail['phu_cap_vuot_khung'];
            $output .= "   - Tổng hệ số cho phụ cấp = {$detail['teacher_coefficient']} + {$detail['phu_cap_chuc_vu']} + {$detail['phu_cap_vuot_khung']} = " . number_format($tongHeSoChoPhuCap, 4, ',', '.') . "\n";
            $phuCapUuDaiPct = $detail['phu_cap_uu_dai_percentage'] >= 1 ? $detail['phu_cap_uu_dai_percentage'] / 100 : $detail['phu_cap_uu_dai_percentage'] / 10;
            $output .= "   - Phụ cấp ưu đãi (%): " . number_format($detail['phu_cap_uu_dai_percentage'], 4, ',', '.') . " (" . number_format($phuCapUuDaiPct * 100, 2, ',', '.') . "%)\n";
            $output .= "   - Hệ số phụ cấp = " . number_format($tongHeSoChoPhuCap, 4, ',', '.') . " × " . number_format($phuCapUuDaiPct, 4, ',', '.') . " = " . number_format($detail['he_so_phu_cap'], 4, ',', '.') . "\n\n";

            // Tính hệ số phụ cấp thâm niên
            $output .= "4. TÍNH HỆ SỐ PHỤ CẤP THÂM NIÊN:\n";
            $phuCapThamNienPct = $detail['phu_cap_tham_nien_percentage'] >= 1 ? $detail['phu_cap_tham_nien_percentage'] / 100 : $detail['phu_cap_tham_nien_percentage'] / 10;
            $output .= "   - Phụ cấp thâm niên (%): " . number_format($detail['phu_cap_tham_nien_percentage'], 4, ',', '.') . " (" . number_format($phuCapThamNienPct * 100, 2, ',', '.') . "%)\n";
            $output .= "   - Hệ số phụ cấp thâm niên = " . number_format($tongHeSoChoPhuCap, 4, ',', '.') . " × " . number_format($phuCapThamNienPct, 4, ',', '.') . " = " . number_format($detail['he_so_phu_cap_tham_nien'], 4, ',', '.') . "\n\n";

            // Tổng hệ số
            $output .= "5. TỔNG HỆ SỐ:\n";
            $output .= "   = {$detail['teacher_coefficient']} + {$detail['phu_cap_chuc_vu']} + {$detail['phu_cap_vuot_khung']} + {$detail['phu_cap_trach_nhiem']} + {$detail['phu_cap_doc_hai']} + " . number_format($detail['he_so_phu_cap'], 4, ',', '.') . " + " . number_format($detail['he_so_phu_cap_tham_nien'], 4, ',', '.') . "\n";
            $output .= "   = " . number_format($detail['tong_he_so'], 4, ',', '.') . "\n\n";

            // Quỹ lương phụ cấp
            $output .= "6. QUỸ LƯƠNG PHỤ CẤP 01 THÁNG:\n";
            $output .= "   = " . number_format($detail['tong_he_so'], 4, ',', '.') . " × " . number_format($detail['base_salary'], 0, ',', '.') . "\n";
            $output .= "   = " . number_format($detail['quy_luong_phu_cap'], 0, ',', '.') . " đ\n\n";

            // Các khoản trừ
            $output .= "7. CÁC KHOẢN TRỪ (10.5%):\n";
            $output .= "   - Tổng hệ số để trừ = {$detail['teacher_coefficient']} + {$detail['phu_cap_chuc_vu']} + {$detail['phu_cap_vuot_khung']} + " . number_format($detail['he_so_phu_cap_tham_nien'], 4, ',', '.') . " = " . number_format($detail['tong_he_so_cho_tru'], 4, ',', '.') . "\n";
            $output .= "   - Quỹ lương để trừ = " . number_format($detail['tong_he_so_cho_tru'], 4, ',', '.') . " × " . number_format($detail['base_salary'], 0, ',', '.') . " = " . number_format($detail['quy_luong_cho_tru'], 0, ',', '.') . " đ\n";
            
            $bhxhPct = $detail['bhxh_nhan_vien_percentage'] >= 1 ? $detail['bhxh_nhan_vien_percentage'] / 100 : $detail['bhxh_nhan_vien_percentage'] / 10;
            $bhytPct = $detail['bhyt_nhan_vien_percentage'] >= 1 ? $detail['bhyt_nhan_vien_percentage'] / 100 : $detail['bhyt_nhan_vien_percentage'] / 10;
            $bhtnPct = $detail['bhtn_nhan_vien_percentage'] >= 1 ? $detail['bhtn_nhan_vien_percentage'] / 100 : $detail['bhtn_nhan_vien_percentage'] / 10;
            
            $output .= "   - BHXH nhân viên (%): " . number_format($detail['bhxh_nhan_vien_percentage'], 4, ',', '.') . " (" . number_format($bhxhPct * 100, 2, ',', '.') . "%)\n";
            $output .= "     → Số tiền trừ BHXH = " . number_format($detail['quy_luong_cho_tru'], 0, ',', '.') . " × " . number_format($bhxhPct, 4, ',', '.') . " = " . number_format($detail['tien_tru_bhxh'], 0, ',', '.') . " đ\n";
            
            $output .= "   - BHYT nhân viên (%): " . number_format($detail['bhyt_nhan_vien_percentage'], 4, ',', '.') . " (" . number_format($bhytPct * 100, 2, ',', '.') . "%)\n";
            $output .= "     → Số tiền trừ BHYT = " . number_format($detail['quy_luong_cho_tru'], 0, ',', '.') . " × " . number_format($bhytPct, 4, ',', '.') . " = " . number_format($detail['tien_tru_bhyt'], 0, ',', '.') . " đ\n";
            
            $output .= "   - BHTN nhân viên (%): " . number_format($detail['bhtn_nhan_vien_percentage'], 4, ',', '.') . " (" . number_format($bhtnPct * 100, 2, ',', '.') . "%)\n";
            $output .= "     → Số tiền trừ BHTN = " . number_format($detail['quy_luong_cho_tru'], 0, ',', '.') . " × " . number_format($bhtnPct, 4, ',', '.') . " = " . number_format($detail['tien_tru_bhtn'], 0, ',', '.') . " đ\n";
            
            $output .= "   - Tổng trừ nhân viên = " . number_format($detail['tien_tru_bhxh'], 0, ',', '.') . " + " . number_format($detail['tien_tru_bhyt'], 0, ',', '.') . " + " . number_format($detail['tien_tru_bhtn'], 0, ',', '.') . " = " . number_format($detail['tong_tru_nhan_vien'], 0, ',', '.') . " đ\n\n";

            // Số tiền thực lĩnh
            $output .= "8. SỐ TIỀN THỰC LĨNH:\n";
            $output .= "   = " . number_format($detail['quy_luong_phu_cap'], 0, ',', '.') . " - " . number_format($detail['tong_tru_nhan_vien'], 0, ',', '.') . "\n";
            $output .= "   = " . number_format($detail['thuc_linh'], 0, ',', '.') . " đ\n\n";

            // Các khoản ngân sách
            $output .= "9. CÁC KHOẢN NGÂN SÁCH (21.5%):\n";
            $bhxhDonViPct = $detail['bhxh_don_vi_percentage'] >= 1 ? $detail['bhxh_don_vi_percentage'] / 100 : $detail['bhxh_don_vi_percentage'] / 10;
            $bhytDonViPct = $detail['bhyt_don_vi_percentage'] >= 1 ? $detail['bhyt_don_vi_percentage'] / 100 : $detail['bhyt_don_vi_percentage'] / 10;
            $bhtnDonViPct = $detail['bhtn_don_vi_percentage'] >= 1 ? $detail['bhtn_don_vi_percentage'] / 100 : $detail['bhtn_don_vi_percentage'] / 10;
            $bhtnTuNSPct = $detail['bhtn_tu_ngan_sach_percentage'] >= 1 ? $detail['bhtn_tu_ngan_sach_percentage'] / 100 : $detail['bhtn_tu_ngan_sach_percentage'] / 10;
            
            $output .= "   - BHXH đơn vị (%): " . number_format($detail['bhxh_don_vi_percentage'], 4, ',', '.') . " (" . number_format($bhxhDonViPct * 100, 2, ',', '.') . "%)\n";
            $output .= "     → Ngân sách BHXH = " . number_format($detail['quy_luong_cho_tru'], 0, ',', '.') . " × " . number_format($bhxhDonViPct, 4, ',', '.') . " = " . number_format($detail['ngan_sach_bhxh'], 0, ',', '.') . " đ\n";
            
            $output .= "   - BHYT đơn vị (%): " . number_format($detail['bhyt_don_vi_percentage'], 4, ',', '.') . " (" . number_format($bhytDonViPct * 100, 2, ',', '.') . "%)\n";
            $output .= "     → Ngân sách BHYT = " . number_format($detail['quy_luong_cho_tru'], 0, ',', '.') . " × " . number_format($bhytDonViPct, 4, ',', '.') . " = " . number_format($detail['ngan_sach_bhyt'], 0, ',', '.') . " đ\n";
            
            $output .= "   - BHTN đơn vị (%): " . number_format($detail['bhtn_don_vi_percentage'], 4, ',', '.') . " (" . number_format($bhtnDonViPct * 100, 2, ',', '.') . "%)\n";
            $output .= "     → Ngân sách BHTN = " . number_format($detail['quy_luong_cho_tru'], 0, ',', '.') . " × " . number_format($bhtnDonViPct, 4, ',', '.') . " = " . number_format($detail['ngan_sach_bhtn'], 0, ',', '.') . " đ\n";
            
            $output .= "   - BHTN từ ngân sách (%): " . number_format($detail['bhtn_tu_ngan_sach_percentage'], 4, ',', '.') . " (" . number_format($bhtnTuNSPct * 100, 2, ',', '.') . "%)\n";
            $output .= "     → Ngân sách BHTN từ NS = " . number_format($detail['quy_luong_cho_tru'], 0, ',', '.') . " × " . number_format($bhtnTuNSPct, 4, ',', '.') . " = " . number_format($detail['ngan_sach_bhtn_tu_ns'], 0, ',', '.') . " đ\n";
            
            $output .= "   - Tổng ngân sách = " . number_format($detail['ngan_sach_bhxh'], 0, ',', '.') . " + " . number_format($detail['ngan_sach_bhyt'], 0, ',', '.') . " + " . number_format($detail['ngan_sach_bhtn'], 0, ',', '.') . " + " . number_format($detail['ngan_sach_bhtn_tu_ns'], 0, ',', '.') . " = " . number_format($detail['tong_ngan_sach'], 0, ',', '.') . " đ\n\n";

            // Tổng chi phí
            $output .= "10. TỔNG CHI PHÍ:\n";
            $output .= "    = " . number_format($detail['quy_luong_phu_cap'], 0, ',', '.') . " + " . number_format($detail['tong_ngan_sach'], 0, ',', '.') . "\n";
            $output .= "    = " . number_format($detail['tong_chi_phi'], 0, ',', '.') . " đ\n\n";

            $output .= "\n";
        }

        return Response::make($output, 200, $headers);
    }

    /**
     * Lấy giá trị thành phần lương CHỈ từ teacherpayrollcomponent
     * Không lấy từ các bảng config khác
     */
    private function getComponentValue($teacher, $componentId, $effectiveDate, $teacherComponent = null)
    {
        $component = PayrollComponent::find($componentId);
        if (!$component) {
            return ['coefficient' => 0, 'percentage' => 0, 'fixed' => 0];
        }

        $coefficient = 0;
        $percentage = 0;
        $fixed = 0;

        // CHỈ lấy từ teacherpayrollcomponent, không lấy từ config nào khác
        if ($teacherComponent) {
            // Lấy giá trị điều chỉnh từ teacherpayrollcomponent
            $teacherAdjustCoeff = $teacherComponent->adjustcustomcoefficient;
            $teacherAdjustPct = $teacherComponent->adjustcustompercentage;
            
            // Sử dụng giá trị từ teacherpayrollcomponent (nếu có)
            if ($teacherAdjustCoeff !== null) {
                $coefficient = $teacherAdjustCoeff;
            }
            
            if ($teacherAdjustPct !== null) {
                $percentage = $teacherAdjustPct;
            }
        }

        // Đặc biệt: Nếu là "Lương ngạch bậc", lấy từ teacher.currentcoefficient
        if (stripos($component->componentname, 'lương ngạch bậc') !== false && $teacher && $teacher->currentcoefficient) {
            $coefficient = $teacher->currentcoefficient;
        }

        return [
            'coefficient' => $coefficient,
            'percentage' => $percentage,
            'fixed' => $fixed,
        ];
    }

    /**
     * Tìm component theo tên trong danh sách
     */
    private function findComponentByName($componentValues, $names)
    {
        foreach ($componentValues as $componentId => $data) {
            $componentName = $data['component']->componentname ?? '';
            foreach ($names as $name) {
                if (stripos($componentName, $name) !== false) {
                    return $data;
                }
            }
        }
        return null;
    }
}

