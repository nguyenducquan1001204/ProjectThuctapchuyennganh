<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\PayrollRunDetail;
use App\Models\PayrollRunDetailComponent;
use App\Models\TeacherPayrollComponent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PayrollRunDetailController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->teacherid) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Tài khoản của bạn chưa được liên kết với giáo viên. Vui lòng liên hệ quản trị viên.');
        }

        $teacher = Teacher::find($user->teacherid);
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Không tìm thấy thông tin giáo viên.');
        }

        $query = PayrollRunDetail::with(['payrollRun.unit', 'components.component'])
            ->where('teacherid', $teacher->teacherid);

        if ($request->filled('search_id')) {
            $query->where('detailid', $request->search_id);
        }
        if ($request->filled('search_payrollrunid')) {
            $query->where('payrollrunid', $request->search_payrollrunid);
        }
        if ($request->filled('search_totalincome_from')) {
            $query->where('totalincome', '>=', $request->search_totalincome_from);
        }
        if ($request->filled('search_totalincome_to')) {
            $query->where('totalincome', '<=', $request->search_totalincome_to);
        }

        $payrollRunDetails = $query->orderBy('payrollrunid', 'desc')->get();

        return view('teacher.payrollrundetails.index', compact('payrollRunDetails', 'teacher'));
    }

    public function getCalculationDetails($id)
    {
        $detail = PayrollRunDetail::with(['payrollRun.baseSalary', 'teacher', 'components.component'])->findOrFail($id);
        
        $user = Auth::user();
        
        if (!$user || !$user->teacherid || $detail->teacherid != $user->teacherid) {
            return response()->json([
                'success' => false,
                'error' => 'Không có quyền truy cập'
            ], 403);
        }
        
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

        $payrollPeriod = $payrollRun->payrollperiod;
        $periodEnd = $payrollPeriod . '-01';
        $periodEnd = Carbon::parse($periodEnd)->endOfMonth()->format('Y-m-d');

        $teacherComponents = TeacherPayrollComponent::where('teacherid', $teacher->teacherid)
            ->where('effectivedate', '<=', $periodEnd)
            ->where(function($query) use ($payrollPeriod) {
                $periodStart = $payrollPeriod . '-01';
                $query->whereNull('expirationdate')
                      ->orWhere('expirationdate', '>=', $periodStart);
            })
            ->with('component')
            ->get();

        $componentValues = [];
        foreach ($teacherComponents as $teacherComponent) {
            $component = $teacherComponent->component;
            if (!$component) continue;

            $coefficient = $teacherComponent->adjustcustomcoefficient ?? 0;
            $percentage = $teacherComponent->adjustcustompercentage ?? 0;

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

        $phuCapChucVu = $this->findComponentByName($componentValues, ['Phụ cấp chức vụ', 'phụ cấp chức vụ']);
        $phuCapVuotKhung = $this->findComponentByName($componentValues, ['Phụ cấp vượt khung', 'phụ cấp vượt khung']);
        $phuCapUuDai = $this->findComponentByName($componentValues, ['Phụ cấp ưu đãi', 'phụ cấp ưu đãi']);
        $phuCapThamNien = $this->findComponentByName($componentValues, ['Phụ cấp thâm niên', 'phụ cấp thâm niên']);
        $phuCapTrachNhiem = $this->findComponentByName($componentValues, ['Phụ cấp trách nhiệm', 'phụ cấp trách nhiệm']);
        $phuCapDocHai = $this->findComponentByName($componentValues, ['Phụ cấp độc hại', 'phụ cấp độc hại']);

        $phuCapChucVuHeSo = $phuCapChucVu ? $phuCapChucVu['value']['coefficient'] : 0;
        $phuCapVuotKhungHeSo = $phuCapVuotKhung ? $phuCapVuotKhung['value']['coefficient'] : 0;
        $phuCapTrachNhiemHeSo = $phuCapTrachNhiem ? $phuCapTrachNhiem['value']['coefficient'] : 0;
        $phuCapDocHaiHeSo = $phuCapDocHai ? $phuCapDocHai['value']['coefficient'] : 0;

        $phuCapUuDaiTyLe = $phuCapUuDai ? ($phuCapUuDai['value']['percentage'] ?? 0) : 0;
        $phuCapThamNienTyLe = $phuCapThamNien ? ($phuCapThamNien['value']['percentage'] ?? 0) : 0;

        $heSoPhuCap = 0;
        if ($phuCapUuDai) {
            $tongHeSoChoPhuCap = $teacherCoefficient + $phuCapChucVuHeSo + $phuCapVuotKhungHeSo;
            $tyLeValue = $phuCapUuDaiTyLe >= 1 ? $phuCapUuDaiTyLe / 100 : $phuCapUuDaiTyLe / 10;
            $heSoPhuCap = $tongHeSoChoPhuCap * $tyLeValue;
        }

        $heSoPhuCapThamNien = 0;
        if ($phuCapThamNien) {
            $tongHeSoChoPhuCap = $teacherCoefficient + $phuCapChucVuHeSo + $phuCapVuotKhungHeSo;
            $tyLeValue = $phuCapThamNienTyLe >= 1 ? $phuCapThamNienTyLe / 100 : $phuCapThamNienTyLe / 10;
            $heSoPhuCapThamNien = $tongHeSoChoPhuCap * $tyLeValue;
        }

        $tongHeSo = $teacherCoefficient
            + $phuCapChucVuHeSo
            + $phuCapVuotKhungHeSo
            + $phuCapTrachNhiemHeSo
            + $phuCapDocHaiHeSo
            + $heSoPhuCap
            + $heSoPhuCapThamNien;

        $quyLuongPhuCap = $tongHeSo * $baseSalaryAmount;

        $tongHeSoChoTru = $teacherCoefficient
            + $phuCapChucVuHeSo
            + $phuCapVuotKhungHeSo
            + $heSoPhuCapThamNien;
        $quyLuongChoTru = $tongHeSoChoTru * $baseSalaryAmount;

        $bhxhNhanVien = $this->findComponentByName($componentValues, ['BHXH nhân viên', 'bhxh nhân viên']);
        $bhytNhanVien = $this->findComponentByName($componentValues, ['BHYT nhân viên', 'bhyt nhân viên']);
        $bhtnNhanVien = $this->findComponentByName($componentValues, ['BHTN nhân viên', 'bhtn nhân viên']);

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

        $bhxhDonVi = $this->findComponentByName($componentValues, ['BHXH đơn vị', 'bhxh đơn vị']);
        $bhytDonVi = $this->findComponentByName($componentValues, ['BHYT đơn vị', 'bhyt đơn vị']);
        $bhtnDonVi = $this->findComponentByName($componentValues, ['BHTN đơn vị', 'bhtn đơn vị']);
        $bhtnTuNganSach = $this->findComponentByName($componentValues, ['BHTN từ ngân sách', 'bhtn từ ngân sách']);

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
}

