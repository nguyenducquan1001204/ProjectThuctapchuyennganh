<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollRunDetail;
use App\Models\PayrollRun;
use App\Models\Teacher;
use App\Models\PayrollRunDetailComponent;
use App\Models\TeacherPayrollComponent;
use App\Models\PayrollComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PayrollRunDetailController extends Controller
{
    /**
     * Hiển thị danh sách chi tiết bảng lương từng giáo viên
     */
    public function index(Request $request)
    {
        $query = PayrollRunDetail::with(['payrollRun.unit', 'teacher']);

        // Tìm kiếm theo mã chi tiết
        if ($request->filled('search_id')) {
            $query->where('detailid', $request->search_id);
        }

        // Tìm kiếm theo bảng lương
        if ($request->filled('search_payrollrunid')) {
            $query->where('payrollrunid', $request->search_payrollrunid);
        }

        // Tìm kiếm theo giáo viên
        if ($request->filled('search_teacherid')) {
            $query->where('teacherid', $request->search_teacherid);
        }

        // Tìm kiếm theo tổng thu nhập (từ - đến)
        if ($request->filled('search_totalincome_from')) {
            $query->where('totalincome', '>=', $request->search_totalincome_from);
        }
        if ($request->filled('search_totalincome_to')) {
            $query->where('totalincome', '<=', $request->search_totalincome_to);
        }

        $payrollRunDetails = $query->orderBy('detailid', 'desc')->get();

        // Lấy danh sách bảng lương cho dropdown
        $allPayrollRuns = PayrollRun::with('unit')
            ->orderBy('payrollrunid', 'desc')
            ->get();

        // Lấy danh sách giáo viên cho dropdown
        $allTeachers = Teacher::orderBy('fullname', 'asc')->get();

        return view('admin.payrollrundetails.index', compact('payrollRunDetails', 'allPayrollRuns', 'allTeachers'));
    }

    /**
     * Validation rules
     */
    private function getValidationRules($ignoreId = null): array
    {
        $rules = [
            'payrollrunid' => [
                'required',
                'integer',
                'exists:payrollrun,payrollrunid',
            ],
            'teacherid' => [
                'required',
                'integer',
                'exists:teacher,teacherid',
            ],
            'totalincome' => [
                'required',
                'numeric',
                'min:0',
            ],
            'totalemployeedeductions' => [
                'required',
                'numeric',
                'min:0',
            ],
            'totalemployercontributions' => [
                'required',
                'numeric',
                'min:0',
            ],
            'netpay' => [
                'required',
                'numeric',
            ],
            'totalcost' => [
                'required',
                'numeric',
                'min:0',
            ],
            'note' => 'nullable|string|max:65535',
        ];

        // Unique constraint: payrollrunid + teacherid
        if ($ignoreId) {
            $rules['teacherid'][] = Rule::unique('payrollrundetail', 'teacherid')
                ->where('payrollrunid', request('payrollrunid'))
                ->ignore($ignoreId, 'detailid');
        } else {
            $rules['teacherid'][] = Rule::unique('payrollrundetail', 'teacherid')
                ->where('payrollrunid', request('payrollrunid'));
        }

        return $rules;
    }

    /**
     * Validation messages
     */
    private function getValidationMessages(): array
    {
        return [
            'payrollrunid.required' => 'Bảng lương là bắt buộc',
            'payrollrunid.integer' => 'Bảng lương không hợp lệ',
            'payrollrunid.exists' => 'Bảng lương không tồn tại',
            'teacherid.required' => 'Giáo viên là bắt buộc',
            'teacherid.integer' => 'Giáo viên không hợp lệ',
            'teacherid.exists' => 'Giáo viên không tồn tại',
            'teacherid.unique' => 'Giáo viên này đã có trong bảng lương này',
            'totalincome.required' => 'Tổng thu nhập là bắt buộc',
            'totalincome.numeric' => 'Tổng thu nhập phải là số',
            'totalincome.min' => 'Tổng thu nhập phải lớn hơn hoặc bằng 0',
            'totalemployeedeductions.required' => 'Tổng khoản trừ nhân viên là bắt buộc',
            'totalemployeedeductions.numeric' => 'Tổng khoản trừ nhân viên phải là số',
            'totalemployeedeductions.min' => 'Tổng khoản trừ nhân viên phải lớn hơn hoặc bằng 0',
            'totalemployercontributions.required' => 'Tổng khoản đóng đơn vị là bắt buộc',
            'totalemployercontributions.numeric' => 'Tổng khoản đóng đơn vị phải là số',
            'totalemployercontributions.min' => 'Tổng khoản đóng đơn vị phải lớn hơn hoặc bằng 0',
            'netpay.required' => 'Thực lĩnh là bắt buộc',
            'netpay.numeric' => 'Thực lĩnh phải là số',
            'totalcost.required' => 'Tổng chi phí là bắt buộc',
            'totalcost.numeric' => 'Tổng chi phí phải là số',
            'totalcost.min' => 'Tổng chi phí phải lớn hơn hoặc bằng 0',
        ];
    }

    /**
     * Lưu chi tiết bảng lương mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        // Chuyển đổi các giá trị số
        $validated['payrollrunid'] = (int)$validated['payrollrunid'];
        $validated['teacherid'] = (int)$validated['teacherid'];
        $validated['totalincome'] = (float)$validated['totalincome'];
        $validated['totalemployeedeductions'] = (float)$validated['totalemployeedeductions'];
        $validated['totalemployercontributions'] = (float)$validated['totalemployercontributions'];
        $validated['netpay'] = (float)$validated['netpay'];
        $validated['totalcost'] = (float)$validated['totalcost'];

        PayrollRunDetail::create($validated);

        return redirect()->route('admin.payrollrundetail.index')
            ->with('success', 'Thêm chi tiết bảng lương thành công!');
    }

    /**
     * Cập nhật chi tiết bảng lương
     */
    public function update(Request $request, $id)
    {
        $payrollRunDetail = PayrollRunDetail::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($payrollRunDetail->detailid),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        // Chuyển đổi các giá trị số
        $validated['payrollrunid'] = (int)$validated['payrollrunid'];
        $validated['teacherid'] = (int)$validated['teacherid'];
        $validated['totalincome'] = (float)$validated['totalincome'];
        $validated['totalemployeedeductions'] = (float)$validated['totalemployeedeductions'];
        $validated['totalemployercontributions'] = (float)$validated['totalemployercontributions'];
        $validated['netpay'] = (float)$validated['netpay'];
        $validated['totalcost'] = (float)$validated['totalcost'];

        $payrollRunDetail->update($validated);

        return redirect()->route('admin.payrollrundetail.index')
            ->with('success', 'Cập nhật chi tiết bảng lương thành công!');
    }

    /**
     * Xóa chi tiết bảng lương
     */
    public function destroy($id)
    {
        $payrollRunDetail = PayrollRunDetail::findOrFail($id);

        // Xóa các chi tiết thành phần trước
        $payrollRunDetail->components()->delete();

        $payrollRunDetail->delete();

        return redirect()->route('admin.payrollrundetail.index')
            ->with('success', 'Xóa chi tiết bảng lương thành công!');
    }

    /**
     * Lấy chi tiết tính toán cho một payrollrundetail
     */
    public function getCalculationDetails($id)
    {
        $detail = PayrollRunDetail::with(['payrollRun.baseSalary', 'teacher', 'components.component'])->findOrFail($id);
        
        $payrollRun = $detail->payrollRun;
        $teacher = $detail->teacher;
        
        if (!$payrollRun || !$teacher) {
            return response()->json([
                'success' => false,
                'error' => 'Không tìm thấy thông tin bảng lương hoặc giáo viên'
            ], 404);
        }

        $baseSalary = $payrollRun->baseSalary;
        $baseSalaryAmount = $baseSalary ? $baseSalary->basesalaryamount : 0;
        $teacherCoefficient = $teacher->currentcoefficient ?? 0;

        // Lấy ngày hiệu lực
        $payrollPeriod = $payrollRun->payrollperiod;
        $periodEnd = $payrollPeriod . '-01';
        $periodEnd = Carbon::parse($periodEnd)->endOfMonth()->format('Y-m-d');

        // Lấy các thành phần lương từ teacherpayrollcomponent
        $teacherComponents = TeacherPayrollComponent::where('teacherid', $teacher->teacherid)
            ->where('effectivedate', '<=', $periodEnd)
            ->where(function($query) use ($payrollPeriod) {
                $periodStart = $payrollPeriod . '-01';
                $query->whereNull('expirationdate')
                      ->orWhere('expirationdate', '>=', $periodStart);
            })
            ->with('component')
            ->get();

        // Tạo mảng component values
        $componentValues = [];
        foreach ($teacherComponents as $teacherComponent) {
            $component = $teacherComponent->component;
            if (!$component) continue;

            $coefficient = $teacherComponent->adjustcustomcoefficient ?? 0;
            $percentage = $teacherComponent->adjustcustompercentage ?? 0;

            // Đặc biệt: Nếu là "Lương ngạch bậc", lấy từ teacher.currentcoefficient
            if (stripos($component->componentname, 'lương ngạch bậc') !== false && $teacherCoefficient) {
                $coefficient = $teacherCoefficient;
            }

            $componentValues[$component->componentid] = [
                'component' => $component,
                'value' => [
                    'coefficient' => $coefficient,
                    'percentage' => $percentage,
                    'fixed' => 0,
                ],
                'teacherComponent' => $teacherComponent
            ];
        }

        // Tìm các thành phần cần thiết
        $phuCapChucVu = $this->findComponentByName($componentValues, ['Phụ cấp chức vụ', 'phụ cấp chức vụ']);
        $phuCapVuotKhung = $this->findComponentByName($componentValues, ['Phụ cấp vượt khung', 'phụ cấp vượt khung']);
        $phuCapUuDai = $this->findComponentByName($componentValues, ['Phụ cấp ưu đãi', 'phụ cấp ưu đãi']);
        $phuCapThamNien = $this->findComponentByName($componentValues, ['Phụ cấp thâm niên', 'phụ cấp thâm niên']);
        $phuCapTrachNhiem = $this->findComponentByName($componentValues, ['Phụ cấp trách nhiệm', 'phụ cấp trách nhiệm']);
        $phuCapDocHai = $this->findComponentByName($componentValues, ['Phụ cấp độc hại', 'phụ cấp độc hại']);

        // Lấy giá trị HỆ SỐ
        $phuCapChucVuHeSo = $phuCapChucVu ? $phuCapChucVu['value']['coefficient'] : 0;
        $phuCapVuotKhungHeSo = $phuCapVuotKhung ? $phuCapVuotKhung['value']['coefficient'] : 0;
        $phuCapTrachNhiemHeSo = $phuCapTrachNhiem ? $phuCapTrachNhiem['value']['coefficient'] : 0;
        $phuCapDocHaiHeSo = $phuCapDocHai ? $phuCapDocHai['value']['coefficient'] : 0;

        // Lấy giá trị TỶ LỆ
        $phuCapUuDaiTyLe = $phuCapUuDai ? ($phuCapUuDai['value']['percentage'] ?? 0) : 0;
        $phuCapThamNienTyLe = $phuCapThamNien ? ($phuCapThamNien['value']['percentage'] ?? 0) : 0;

        // Tính Hệ số phụ cấp
        $heSoPhuCap = 0;
        if ($phuCapUuDai) {
            $tongHeSoChoPhuCap = $teacherCoefficient + $phuCapChucVuHeSo + $phuCapVuotKhungHeSo;
            $tyLeValue = $phuCapUuDaiTyLe >= 1 ? $phuCapUuDaiTyLe / 100 : $phuCapUuDaiTyLe / 10;
            $heSoPhuCap = $tongHeSoChoPhuCap * $tyLeValue;
        }

        // Tính Hệ số phụ cấp thâm niên
        $heSoPhuCapThamNien = 0;
        if ($phuCapThamNien) {
            $tongHeSoChoPhuCap = $teacherCoefficient + $phuCapChucVuHeSo + $phuCapVuotKhungHeSo;
            $tyLeValue = $phuCapThamNienTyLe >= 1 ? $phuCapThamNienTyLe / 100 : $phuCapThamNienTyLe / 10;
            $heSoPhuCapThamNien = $tongHeSoChoPhuCap * $tyLeValue;
        }

        // Tính Tổng hệ số
        $tongHeSo = $teacherCoefficient
            + $phuCapChucVuHeSo
            + $phuCapVuotKhungHeSo
            + $phuCapTrachNhiemHeSo
            + $phuCapDocHaiHeSo
            + $heSoPhuCap
            + $heSoPhuCapThamNien;

        // Tính Quỹ lương phụ cấp 01 tháng
        $quyLuongPhuCap = $tongHeSo * $baseSalaryAmount;

        // Tính các khoản trừ
        $tongHeSoChoTru = $teacherCoefficient
            + $phuCapChucVuHeSo
            + $phuCapVuotKhungHeSo
            + $heSoPhuCapThamNien;
        $quyLuongChoTru = $tongHeSoChoTru * $baseSalaryAmount;

        // Lấy các thành phần bảo hiểm
        $bhxhNhanVien = $this->findComponentByName($componentValues, ['BHXH nhân viên', 'bhxh nhân viên']);
        $bhytNhanVien = $this->findComponentByName($componentValues, ['BHYT nhân viên', 'bhyt nhân viên']);
        $bhtnNhanVien = $this->findComponentByName($componentValues, ['BHTN nhân viên', 'bhtn nhân viên']);

        // Tính các khoản trừ
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

        // Lấy các thành phần ngân sách
        $bhxhDonVi = $this->findComponentByName($componentValues, ['BHXH đơn vị', 'bhxh đơn vị']);
        $bhytDonVi = $this->findComponentByName($componentValues, ['BHYT đơn vị', 'bhyt đơn vị']);
        $bhtnDonVi = $this->findComponentByName($componentValues, ['BHTN đơn vị', 'bhtn đơn vị']);
        $bhtnTuNganSach = $this->findComponentByName($componentValues, ['BHTN từ ngân sách', 'bhtn từ ngân sách']);

        // Tính các khoản ngân sách
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

        // Lấy danh sách components từ payrollrundetailcomponent
        $components = PayrollRunDetailComponent::where('detailid', $detail->detailid)
            ->with('component')
            ->get()
            ->map(function($item) {
                return [
                    'detailcomponentid' => $item->detailcomponentid,
                    'component_id' => $item->componentid,
                    'component_name' => $item->component ? $item->component->componentname : '-',
                    'calculation_method' => $item->component ? $item->component->calculationmethod : '-',
                    'applied_coefficient' => $item->appliedcoefficient,
                    'applied_percentage' => $item->appliedpercentage,
                    'calculated_amount' => $item->calculatedamount,
                    'note' => $item->note,
                ];
            });

        return response()->json([
            'success' => true,
            'detail' => [
                'teacher_name' => $teacher->fullname,
                'teacher_id' => $teacher->teacherid,
                'teacher_coefficient' => $teacherCoefficient,
                'base_salary' => $baseSalaryAmount,
                'phu_cap_chuc_vu' => $phuCapChucVuHeSo,
                'phu_cap_vuot_khung' => $phuCapVuotKhungHeSo,
                'phu_cap_trach_nhiem' => $phuCapTrachNhiemHeSo,
                'phu_cap_doc_hai' => $phuCapDocHaiHeSo,
                'phu_cap_uu_dai_percentage' => $phuCapUuDaiTyLe,
                'phu_cap_tham_nien_percentage' => $phuCapThamNienTyLe,
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
                'thuc_linh' => $detail->netpay,
                'bhxh_don_vi_percentage' => $bhxhDonVi ? ($bhxhDonVi['value']['percentage'] ?? 0) : 0,
                'bhyt_don_vi_percentage' => $bhytDonVi ? ($bhytDonVi['value']['percentage'] ?? 0) : 0,
                'bhtn_don_vi_percentage' => $bhtnDonVi ? ($bhtnDonVi['value']['percentage'] ?? 0) : 0,
                'bhtn_tu_ngan_sach_percentage' => $bhtnTuNganSach ? ($bhtnTuNganSach['value']['percentage'] ?? 0) : 0,
                'ngan_sach_bhxh' => $nganSachBHXH,
                'ngan_sach_bhyt' => $nganSachBHYT,
                'ngan_sach_bhtn' => $nganSachBHTN,
                'ngan_sach_bhtn_tu_ns' => $nganSachBHTNTuNS,
                'tong_ngan_sach' => $tongNganSach,
                'tong_chi_phi' => $detail->totalcost,
            ],
            'components' => $components
        ]);
    }

    /**
     * Helper function để tìm component theo tên
     */
    private function findComponentByName($componentValues, $names)
    {
        foreach ($componentValues as $data) {
            $componentName = strtolower($data['component']->componentname ?? '');
            foreach ($names as $name) {
                if (stripos($componentName, strtolower($name)) !== false) {
                    return $data;
                }
            }
        }
        return null;
    }

    /**
     * Xuất file Excel bảng lương chi tiết
     */
    public function export(Request $request)
    {
        $query = PayrollRunDetail::with(['payrollRun.unit', 'teacher']);

        // Áp dụng các bộ lọc tương tự như index
        if ($request->filled('search_id')) {
            $query->where('detailid', $request->search_id);
        }

        if ($request->filled('search_payrollrunid')) {
            $query->where('payrollrunid', $request->search_payrollrunid);
        }

        if ($request->filled('search_teacherid')) {
            $query->where('teacherid', $request->search_teacherid);
        }

        if ($request->filled('search_totalincome_from')) {
            $query->where('totalincome', '>=', $request->search_totalincome_from);
        }
        if ($request->filled('search_totalincome_to')) {
            $query->where('totalincome', '<=', $request->search_totalincome_to);
        }

        $payrollRunDetails = $query->orderBy('detailid', 'desc')->get();

        // Tạo tên file
        $filename = 'Bang_luong_chi_tiet_' . date('YmdHis') . '.xls';

        // Headers cho Excel
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        // Tạo nội dung Excel (XML format)
        $output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $output .= "<?mso-application progid=\"Excel.Sheet\"?>\n";
        $output .= "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"\n";
        $output .= " xmlns:o=\"urn:schemas-microsoft-com:office:office\"\n";
        $output .= " xmlns:x=\"urn:schemas-microsoft-com:office:excel\"\n";
        $output .= " xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"\n";
        $output .= " xmlns:html=\"http://www.w3.org/TR/REC-html40\">\n";
        $output .= "<DocumentProperties xmlns=\"urn:schemas-microsoft-com:office:office\">\n";
        $output .= "<Title>Bảng lương chi tiết</Title>\n";
        $output .= "<Created>" . date('Y-m-d\TH:i:s\Z') . "</Created>\n";
        $output .= "</DocumentProperties>\n";
        $output .= "<Styles>\n";
        $output .= "<Style ss:ID=\"Header\">\n";
        $output .= "<Font ss:Bold=\"1\" ss:Size=\"11\"/>\n";
        $output .= "<Interior ss:Color=\"#CCCCCC\" ss:Pattern=\"Solid\"/>\n";
        $output .= "<Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Center\"/>\n";
        $output .= "</Style>\n";
        $output .= "<Style ss:ID=\"Number\">\n";
        $output .= "<NumberFormat ss:Format=\"#,##0\"/>\n";
        $output .= "</Style>\n";
        $output .= "</Styles>\n";
        $output .= "<Worksheet ss:Name=\"Bảng lương chi tiết\">\n";
        $output .= "<Table>\n";

        // Header row
        $output .= "<Row>\n";
        $headers_array = [
            'STT',
            'Bảng lương',
            'Giáo viên',
            'Tổng thu nhập',
            'Tổng khoản trừ nhân viên',
            'Tổng khoản đóng đơn vị',
            'Thực lĩnh',
            'Tổng chi phí',
            'Ghi chú'
        ];
        foreach ($headers_array as $header) {
            $output .= "<Cell ss:StyleID=\"Header\"><Data ss:Type=\"String\">" . htmlspecialchars($header, ENT_XML1, 'UTF-8') . "</Data></Cell>\n";
        }
        $output .= "</Row>\n";

        // Data rows
        $stt = 1;
        foreach ($payrollRunDetails as $detail) {
            $output .= "<Row>\n";
            
            // STT
            $output .= "<Cell><Data ss:Type=\"Number\">" . $stt . "</Data></Cell>\n";
            
            // Bảng lương
            $payrollRunText = $detail->payrollRun 
                ? '#'.$detail->payrollRun->payrollrunid.' - '.($detail->payrollRun->unit ? $detail->payrollRun->unit->unitname : '-').' - '.$detail->payrollRun->payrollperiod 
                : '-';
            $output .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($payrollRunText, ENT_XML1, 'UTF-8') . "</Data></Cell>\n";
            
            // Giáo viên
            $teacherName = $detail->teacher ? $detail->teacher->fullname : '-';
            $output .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($teacherName, ENT_XML1, 'UTF-8') . "</Data></Cell>\n";
            
            // Tổng thu nhập
            $output .= "<Cell ss:StyleID=\"Number\"><Data ss:Type=\"Number\">" . number_format($detail->totalincome, 2, '.', '') . "</Data></Cell>\n";
            
            // Tổng khoản trừ nhân viên
            $output .= "<Cell ss:StyleID=\"Number\"><Data ss:Type=\"Number\">" . number_format($detail->totalemployeedeductions, 2, '.', '') . "</Data></Cell>\n";
            
            // Tổng khoản đóng đơn vị
            $output .= "<Cell ss:StyleID=\"Number\"><Data ss:Type=\"Number\">" . number_format($detail->totalemployercontributions, 2, '.', '') . "</Data></Cell>\n";
            
            // Thực lĩnh
            $output .= "<Cell ss:StyleID=\"Number\"><Data ss:Type=\"Number\">" . number_format($detail->netpay, 2, '.', '') . "</Data></Cell>\n";
            
            // Tổng chi phí
            $output .= "<Cell ss:StyleID=\"Number\"><Data ss:Type=\"Number\">" . number_format($detail->totalcost, 2, '.', '') . "</Data></Cell>\n";
            
            // Ghi chú
            $note = $detail->note ? $detail->note : '-';
            $output .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($note, ENT_XML1, 'UTF-8') . "</Data></Cell>\n";
            
            $output .= "</Row>\n";
            $stt++;
        }

        $output .= "</Table>\n";
        $output .= "</Worksheet>\n";
        $output .= "</Workbook>";

        // Thêm BOM UTF-8 để Excel hiển thị tiếng Việt đúng
        $output = "\xEF\xBB\xBF" . $output;

        return response($output, 200, $headers);
    }
}

