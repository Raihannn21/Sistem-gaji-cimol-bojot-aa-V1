<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\PkwtPayrollPeriod;
use App\Models\PhlPayrollPeriod;
use App\Models\PkwtAttendance;
use App\Models\PhlAttendance;
use App\Models\PkwtOvertime;
use App\Models\PhlOvertime;
use App\Models\PkwtRiskAllowance;
use App\Models\PhlRiskAllowance;
use App\Models\PkwtOtherAllowance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalManpower = Employee::where('status', 'Aktif')->count();
        $pkwtCount = Employee::where('employment_type', 'PKWT')->where('status', 'Aktif')->count();
        $phlCount = Employee::where('employment_type', 'PHL')->where('status', 'Aktif')->count();
        $pkwtPeriods = PkwtPayrollPeriod::with(['attendances.employee', 'overtimes', 'riskAllowances', 'otherAllowances'])->get();
        $totalPkwtSalary = 0;
        foreach ($pkwtPeriods as $period) {
            $startDate = Carbon::parse($period->start_date);
            $endDate = Carbon::parse($period->end_date);
            $totalPeriodDays = $startDate->diffInDays($endDate) + 1;

            $groupedAttendances = $period->attendances->groupBy('employee_id');
            foreach ($groupedAttendances as $empId => $empAttendances) {
                $employee = $empAttendances->first()->employee;
                if (!$employee)
                    continue;

                $daysWorked = $empAttendances->count();
                $harian = $totalPeriodDays > 0 ? ($employee->salary_monthly / $totalPeriodDays) : 0;
                $pokok = $daysWorked * $harian;

                $lembur = $period->overtimes->where('employee_id', $empId)->sum('amount');
                $risiko = $period->riskAllowances->where('employee_id', $empId)->sum('amount');
                $other = $period->otherAllowances->where('employee_id', $empId)->sum('amount');

                $bpjsHealth = $employee->bpjs_health ?? 0;
                $bpjsTk = $employee->bpjs_tk ?? 0;
                $pph21 = $employee->pph21 ?? 0;
                $potongan = $bpjsHealth + $bpjsTk + $pph21;

                $netPay = max(0, $pokok + $lembur + $risiko + $other - $potongan);
                $totalPkwtSalary += $netPay;
            }
        }

        // PHL total salary expenditure
        $phlPeriods = PhlPayrollPeriod::with(['attendances.employee', 'overtimes', 'riskAllowances'])->get();
        $totalPhlSalary = 0;
        foreach ($phlPeriods as $period) {
            $totalPokok = 0;
            foreach ($period->attendances as $attendance) {
                if ($attendance->duration > 0) {
                    $totalPokok += ($attendance->employee?->salary_daily ?? 0);
                }
            }
            $totalOvertime = $period->overtimes->sum('amount');
            $totalRisk = $period->riskAllowances->sum('amount');
            $totalPhlSalary += ($totalPokok + $totalOvertime + $totalRisk);
        }

        $totalSalaryCost = $totalPkwtSalary + $totalPhlSalary;

        $pkwtOvertimeCost = PkwtOvertime::sum('amount');
        $phlOvertimeCost = PhlOvertime::sum('amount');
        $totalOvertimeCost = $pkwtOvertimeCost + $phlOvertimeCost;
        $pkwtRegHours = PkwtAttendance::sum('duration');
        $phlRegHours = PhlAttendance::sum('duration');
        $totalRegHours = $pkwtRegHours + $phlRegHours;

        $pkwtOvtHours = PkwtOvertime::sum('hours');
        $phlOvtHours = PhlOvertime::sum('hours');
        $totalOvtHours = $pkwtOvtHours + $phlOvtHours;

        $totalWorkEffort = $totalRegHours + $totalOvtHours;

        $pkwtResigned = Employee::where('employment_type', 'PKWT')->where('status', '!=', 'Aktif')->count();
        $phlResigned = Employee::where('employment_type', 'PHL')->where('status', '!=', 'Aktif')->count();
        $totalResigned = $pkwtResigned + $phlResigned;

        $totalAllTime = $totalManpower + $totalResigned;
        $turnoverRate = $totalAllTime > 0 ? round(($totalResigned / $totalAllTime) * 100, 1) : 0.0;

        $months = [];
        $payrollRealData = [];
        $payrollEstData = [];
        $recruitmentPkwt = [];
        $recruitmentPhl = [];
        $turnoverData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');
            $monthNum = $date->format('m');
            $yearNum = $date->format('Y');

            $months[] = $monthName;

            // Recruitment counts in this specific month
            $recPkwt = Employee::where('employment_type', 'PKWT')
                ->whereYear('created_at', $yearNum)
                ->whereMonth('created_at', $monthNum)
                ->count();
            $recPhl = Employee::where('employment_type', 'PHL')
                ->whereYear('created_at', $yearNum)
                ->whereMonth('created_at', $monthNum)
                ->count();

            $recruitmentPkwt[] = $recPkwt;
            $recruitmentPhl[] = $recPhl;

            // Turnover count in this month (Count actual resignation/SPHK transactions from EmployeeStatus)
            $resignedCount = \App\Models\EmployeeStatus::whereYear('effective_date', $yearNum)
                ->whereMonth('effective_date', $monthNum)
                ->count();
            $turnoverData[] = $resignedCount;

            // Monthly Payroll real/est totals (in Millions Rp)
            // PKWT
            $mPkwtPeriodTotal = 0;
            $mPkwtPeriods = PkwtPayrollPeriod::whereYear('start_date', $yearNum)
                ->whereMonth('start_date', $monthNum)
                ->with(['attendances.employee', 'overtimes', 'riskAllowances', 'otherAllowances'])
                ->get();
            foreach ($mPkwtPeriods as $period) {
                $startDate = Carbon::parse($period->start_date);
                $endDate = Carbon::parse($period->end_date);
                $totalPeriodDays = $startDate->diffInDays($endDate) + 1;

                $groupedAttendances = $period->attendances->groupBy('employee_id');
                foreach ($groupedAttendances as $empId => $empAttendances) {
                    $employee = $empAttendances->first()->employee;
                    if (!$employee)
                        continue;

                    $daysWorked = $empAttendances->count();
                    $harian = $totalPeriodDays > 0 ? ($employee->salary_monthly / $totalPeriodDays) : 0;
                    $pokok = $daysWorked * $harian;

                    $lembur = $period->overtimes->where('employee_id', $empId)->sum('amount');
                    $risiko = $period->riskAllowances->where('employee_id', $empId)->sum('amount');
                    $other = $period->otherAllowances->where('employee_id', $empId)->sum('amount');

                    $bpjsHealth = $employee->bpjs_health ?? 0;
                    $bpjsTk = $employee->bpjs_tk ?? 0;
                    $pph21 = $employee->pph21 ?? 0;
                    $potongan = $bpjsHealth + $bpjsTk + $pph21;

                    $netPay = max(0, $pokok + $lembur + $risiko + $other - $potongan);
                    $mPkwtPeriodTotal += $netPay;
                }
            }

            // PHL
            $mPhlPeriodTotal = 0;
            $mPhlPeriods = PhlPayrollPeriod::whereYear('start_date', $yearNum)
                ->whereMonth('start_date', $monthNum)
                ->with(['attendances.employee', 'overtimes', 'riskAllowances'])
                ->get();
            foreach ($mPhlPeriods as $period) {
                $totalPokok = 0;
                foreach ($period->attendances as $attendance) {
                    if ($attendance->duration > 0) {
                        $totalPokok += ($attendance->employee?->salary_daily ?? 0);
                    }
                }
                $totalOvertime = $period->overtimes->sum('amount');
                $totalRisk = $period->riskAllowances->sum('amount');
                $mPhlPeriodTotal += ($totalPokok + $totalOvertime + $totalRisk);
            }

            $monthlyReal = round(($mPkwtPeriodTotal + $mPhlPeriodTotal) / 1000000, 1);
            $payrollRealData[] = $monthlyReal;
            $payrollEstData[] = round($monthlyReal * 1.05, 1);
        }

        // Sparkline for turnover rate
        $turnoverSparklineData = array_slice($turnoverData, -7);

        return view('pages.dashboard.payroll', [
            'title' => 'Dashboard Penggajian',

            // Manpower props
            'totalManpower' => $totalManpower,
            'pkwtCount' => $pkwtCount,
            'phlCount' => $phlCount,

            // Salary props
            'totalSalaryCost' => $totalSalaryCost,
            'totalPkwtSalary' => $totalPkwtSalary,
            'totalPhlSalary' => $totalPhlSalary,

            // Overtime props
            'totalOvertimeCost' => $totalOvertimeCost,
            'pkwtOvertimeCost' => $pkwtOvertimeCost,
            'phlOvertimeCost' => $phlOvertimeCost,

            // Effort props
            'totalWorkEffort' => $totalWorkEffort,
            'totalRegHours' => $totalRegHours,
            'totalOvtHours' => $totalOvtHours,

            // Turnover props
            'pkwtResigned' => $pkwtResigned,
            'phlResigned' => $phlResigned,
            'totalResigned' => $totalResigned,
            'turnoverRate' => $turnoverRate,
            'turnoverSparklineData' => $turnoverSparklineData,

            // Chart Trends
            'months' => $months,
            'payrollRealData' => $payrollRealData,
            'payrollEstData' => $payrollEstData,
            'recruitmentPkwt' => $recruitmentPkwt,
            'recruitmentPhl' => $recruitmentPhl
        ]);
    }
}
