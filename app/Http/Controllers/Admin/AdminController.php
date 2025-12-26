<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminMenuItem;
use App\Models\Teacher;
use App\Models\BudgetSpendingUnit;
use App\Models\JobTitle;
use App\Models\PayrollRun;
use App\Models\PayrollRunDetail;
use App\Models\SystemUser;
use App\Models\PayrollComponent;
use App\Models\BaseSalary;
use App\Models\EmploymentContract;
use App\Models\SalaryIncreaseDecision;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_teachers' => Teacher::count(),
            'active_teachers' => Teacher::where('status', 'active')->count(),
            'total_units' => BudgetSpendingUnit::count(),
            'total_job_titles' => JobTitle::count(),
            'total_payroll_runs' => PayrollRun::count(),
            'approved_payroll_runs' => PayrollRun::where('status', 'approved')->count(),
            'draft_payroll_runs' => PayrollRun::where('status', 'draft')->count(),
            'total_users' => SystemUser::count(),
            'total_payroll_components' => PayrollComponent::count(),
            'total_contracts' => EmploymentContract::count(),
            'total_salary_decisions' => SalaryIncreaseDecision::count(),
        ];

        $payrollStats = [
            'total_net_pay' => PayrollRunDetail::sum('netpay') ?? 0,
            'total_cost' => PayrollRunDetail::sum('totalcost') ?? 0,
            'total_income' => PayrollRunDetail::sum('totalincome') ?? 0,
            'total_deductions' => PayrollRunDetail::sum('totalemployeedeductions') ?? 0,
        ];

        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $payrollRunIds = PayrollRun::whereBetween('createdat', [$monthStart, $monthEnd])
                ->pluck('payrollrunid')
                ->toArray();
            
            $totalNetPay = 0;
            if (!empty($payrollRunIds)) {
                $totalNetPay = PayrollRunDetail::whereIn('payrollrunid', $payrollRunIds)
                    ->sum('netpay') ?? 0;
            }
            
            $monthlyStats[] = [
                'month' => $month->format('M/Y'),
                'month_name' => $month->format('m/Y'),
                'payroll_runs' => count($payrollRunIds),
                'total_net_pay' => $totalNetPay,
            ];
        }

        $teachersByUnit = Teacher::select('unitid', DB::raw('count(*) as count'))
            ->where(function($query) {
                $query->where('status', 'active')
                      ->orWhereNull('status');
            })
            ->groupBy('unitid')
            ->get()
            ->map(function($item) {
                $unit = BudgetSpendingUnit::find($item->unitid);
                return [
                    'unit_name' => $unit ? $unit->unitname : 'Chưa phân loại',
                    'count' => $item->count,
                ];
            });

        $teachersByJobTitle = Teacher::select('jobtitleid', DB::raw('count(*) as count'))
            ->where(function($query) {
                $query->where('status', 'active')
                      ->orWhereNull('status');
            })
            ->groupBy('jobtitleid')
            ->get()
            ->map(function($item) {
                $jobTitle = JobTitle::find($item->jobtitleid);
                return [
                    'job_title' => $jobTitle ? $jobTitle->jobtitlename : 'Chưa phân loại',
                    'count' => $item->count,
                ];
            });

        $recentPayrollRuns = PayrollRun::with(['unit', 'baseSalary'])
            ->orderBy('createdat', 'desc')
            ->limit(5)
            ->get();

        $recentTeachers = Teacher::with(['unit', 'jobTitle'])
            ->orderBy('startdate', 'desc')
            ->limit(5)
            ->get();

        $contractStats = [
            'active_contracts' => EmploymentContract::where(function($query) {
                $query->where('enddate', '>=', Carbon::now())
                      ->orWhereNull('enddate');
            })->count(),
            'expiring_soon' => EmploymentContract::whereNotNull('enddate')
                ->whereBetween('enddate', [
                    Carbon::now(),
                    Carbon::now()->addMonths(3)
                ])->count(),
        ];

        $baseSalaryStats = BaseSalary::select('unitid', DB::raw('MAX(basesalaryamount) as max_salary'))
            ->where(function($query) {
                $query->where('expirationdate', '>=', Carbon::now())
                      ->orWhereNull('expirationdate');
            })
            ->groupBy('unitid')
            ->with('unit')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'payrollStats',
            'monthlyStats',
            'teachersByUnit',
            'teachersByJobTitle',
            'recentPayrollRuns',
            'recentTeachers',
            'contractStats',
            'baseSalaryStats'
        ));
    }
}

