<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeStatus;
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
    private function getLateMinutes($lateTime)
    {
        if (!$lateTime || $lateTime === '-' || $lateTime === '00:00') {
            return 0;
        }
        try {
            $parts = explode(':', $lateTime);
            if (count($parts) >= 2) {
                return intval($parts[0]) * 60 + intval($parts[1]);
            }
            return intval($lateTime);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function index(Request $request)
    {
        $latestPeriod = PkwtPayrollPeriod::orderBy('start_date', 'desc')->first();
        $defaultYear = $latestPeriod ? Carbon::parse($latestPeriod->start_date)->format('Y') : date('Y');
        $defaultMonth = $latestPeriod ? Carbon::parse($latestPeriod->start_date)->format('m') : date('m');

        $selectedYear = $request->query('year', $defaultYear);
        $selectedMonth = $request->query('month', $defaultMonth);

        $monthNum = sprintf("%02d", $selectedMonth);
        $yearNum = $selectedYear;

        $totalManpower = Employee::where('status', 'Aktif')->count();
        $pkwtCount = Employee::where('employment_type', 'PKWT')->where('status', 'Aktif')->count();
        $phlCount = Employee::where('employment_type', 'PHL')->where('status', 'Aktif')->count();

        // Filter by the selected month and year
        $pkwtPeriods = PkwtPayrollPeriod::whereYear('start_date', $yearNum)
            ->whereMonth('start_date', $monthNum)
            ->with(['attendances.employee', 'overtimes', 'riskAllowances', 'otherAllowances', 'periodTeams'])
            ->get();

        $phlPeriods = PhlPayrollPeriod::whereYear('start_date', $yearNum)
            ->whereMonth('start_date', $monthNum)
            ->with(['attendances.employee', 'overtimes', 'riskAllowances'])
            ->get();

        // Calculate current month lateness metrics for PKWT (Monthly)
        $pkwtLateCount = 0;
        $pkwtLateMinutes = 0;
        $pkwtTotalAttendances = 0;

        foreach ($pkwtPeriods as $period) {
            foreach ($period->attendances as $att) {
                $pkwtTotalAttendances++;
                $lateTime = $att->late_time;
                if ($lateTime && $lateTime !== '-' && $lateTime !== '00:00') {
                    $pkwtLateCount++;
                    $pkwtLateMinutes += $this->getLateMinutes($lateTime);
                }
            }
        }

        $pkwtLateHours = round($pkwtLateMinutes / 60, 1);
        $pkwtLateRate = $pkwtTotalAttendances > 0 ? round(($pkwtLateCount / $pkwtTotalAttendances) * 100, 1) : 0;

        // Calculate current month lateness metrics for PHL (Daily)
        $phlLateCount = 0;
        $phlLateMinutes = 0;
        $phlTotalAttendances = 0;

        foreach ($phlPeriods as $period) {
            foreach ($period->attendances as $att) {
                $phlTotalAttendances++;
                $lateTime = $att->late_time;
                if ($lateTime && $lateTime !== '-' && $lateTime !== '00:00') {
                    $phlLateCount++;
                    $phlLateMinutes += $this->getLateMinutes($lateTime);
                }
            }
        }

        $phlLateHours = round($phlLateMinutes / 60, 1);
        $phlLateRate = $phlTotalAttendances > 0 ? round(($phlLateCount / $phlTotalAttendances) * 100, 1) : 0;

        // Calculate previous month lateness metrics for PKWT (for comparison)
        $prevMonth = intval($selectedMonth) - 1;
        $prevYear = intval($selectedYear);
        if ($prevMonth === 0) {
            $prevMonth = 12;
            $prevYear--;
        }

        $prevPkwtPeriods = PkwtPayrollPeriod::whereYear('start_date', $prevYear)
            ->whereMonth('start_date', sprintf("%02d", $prevMonth))
            ->with(['attendances'])
            ->get();

        $prevPkwtLateCount = 0;
        $prevPkwtTotalAttendances = 0;
        foreach ($prevPkwtPeriods as $period) {
            foreach ($period->attendances as $att) {
                $prevPkwtTotalAttendances++;
                $lateTime = $att->late_time;
                if ($lateTime && $lateTime !== '-' && $lateTime !== '00:00') {
                    $prevPkwtLateCount++;
                }
            }
        }
        $prevPkwtLateRate = $prevPkwtTotalAttendances > 0 ? round(($prevPkwtLateCount / $prevPkwtTotalAttendances) * 100, 1) : 0;
        $pkwtLateRateDiff = round($pkwtLateRate - $prevPkwtLateRate, 1);

        // Calculate previous month lateness metrics for PHL (for comparison)
        $prevPhlPeriods = PhlPayrollPeriod::whereYear('start_date', $prevYear)
            ->whereMonth('start_date', sprintf("%02d", $prevMonth))
            ->with(['attendances'])
            ->get();

        $prevPhlLateCount = 0;
        $prevPhlTotalAttendances = 0;
        foreach ($prevPhlPeriods as $period) {
            foreach ($period->attendances as $att) {
                $prevPhlTotalAttendances++;
                $lateTime = $att->late_time;
                if ($lateTime && $lateTime !== '-' && $lateTime !== '00:00') {
                    $prevPhlLateCount++;
                }
            }
        }
        $prevPhlLateRate = $prevPhlTotalAttendances > 0 ? round(($prevPhlLateCount / $prevPhlTotalAttendances) * 100, 1) : 0;
        $phlLateRateDiff = round($phlLateRate - $prevPhlLateRate, 1);

        // Calculate overall combined lateness metrics
        $totalAttendances = $pkwtTotalAttendances + $phlTotalAttendances;
        $totalLateCount = $pkwtLateCount + $phlLateCount;
        $overallLateRate = $totalAttendances > 0 ? round(($totalLateCount / $totalAttendances) * 100, 1) : 0;

        $prevTotalAttendances = $prevPkwtTotalAttendances + $prevPhlTotalAttendances;
        $prevTotalLateCount = $prevPkwtLateCount + $prevPhlLateCount;
        $prevOverallLateRate = $prevTotalAttendances > 0 ? round(($prevTotalLateCount / $prevTotalAttendances) * 100, 1) : 0;
        $overallLateRateDiff = round($overallLateRate - $prevOverallLateRate, 1);

        // Daily lateness trend for the active period (PHL - Harian)
        $dailyDates = [];
        $dailyLateCounts = [];
        $dailyLateHours = [];

        if ($phlPeriods->count() > 0) {
            $minStart = null;
            $maxEnd = null;
            foreach ($phlPeriods as $period) {
                $pStart = Carbon::parse($period->start_date);
                $pEnd = Carbon::parse($period->end_date);
                if (!$minStart || $pStart->lt($minStart)) $minStart = $pStart;
                if (!$maxEnd || $pEnd->gt($maxEnd)) $maxEnd = $pEnd;
            }

            if ($minStart && $maxEnd) {
                $current = $minStart->copy();
                while ($current->lte($maxEnd)) {
                    $dateStr = $current->format('Y-m-d');
                    $dailyDates[] = $current->format('d M');

                    $lateCount = 0;
                    $lateMin = 0;
                    foreach ($phlPeriods as $period) {
                        $dateAttendances = $period->attendances->filter(function($att) use ($dateStr) {
                            $attDate = $att->date;
                            if ($attDate instanceof \Carbon\Carbon) {
                                return $attDate->format('Y-m-d') === $dateStr;
                            }
                            return substr((string) $attDate, 0, 10) === $dateStr;
                        });
                        foreach ($dateAttendances as $att) {
                            $lateTime = $att->late_time;
                            if ($lateTime && $lateTime !== '-' && $lateTime !== '00:00') {
                                $lateCount++;
                                $lateMin += $this->getLateMinutes($lateTime);
                            }
                        }
                    }

                    $dailyLateCounts[] = $lateCount;
                    $dailyLateHours[] = round($lateMin / 60, 1);
                    $current->addDay();
                }
            }
        }

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
                $periodTeam = $period->periodTeams->where('team_id', $employee->team_id)->first();
                $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);
                $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
                $harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
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

        // PHL total salary expenditure in selected period
        // Already loaded above as $phlPeriods

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

        $pkwtOvertimeCost = 0;
        foreach ($pkwtPeriods as $period) {
            $pkwtOvertimeCost += $period->overtimes->sum('amount');
        }
        $phlOvertimeCost = 0;
        foreach ($phlPeriods as $period) {
            $phlOvertimeCost += $period->overtimes->sum('amount');
        }
        $totalOvertimeCost = $pkwtOvertimeCost + $phlOvertimeCost;

        $pkwtRegHours = 0;
        foreach ($pkwtPeriods as $period) {
            $pkwtRegHours += $period->attendances->sum('duration');
        }
        $phlRegHours = 0;
        foreach ($phlPeriods as $period) {
            $phlRegHours += $period->attendances->sum('duration');
        }
        $totalRegHours = $pkwtRegHours + $phlRegHours;

        $pkwtOvtHours = 0;
        foreach ($pkwtPeriods as $period) {
            $pkwtOvtHours += $period->overtimes->sum('hours');
        }
        $phlOvtHours = 0;
        foreach ($phlPeriods as $period) {
            $phlOvtHours += $period->overtimes->sum('hours');
        }
        $totalOvtHours = $pkwtOvtHours + $phlOvtHours;

        $totalWorkEffort = $totalRegHours + $totalOvtHours;

        // Filter resignations by selected month and year
        $resignedEmployees = EmployeeStatus::whereYear('effective_date', $yearNum)
            ->whereMonth('effective_date', $monthNum)
            ->with('employee')
            ->get();
            
        $pkwtResigned = 0;
        $phlResigned = 0;
        foreach ($resignedEmployees as $statusRecord) {
            if ($statusRecord->employee) {
                if ($statusRecord->employee->employment_type === 'PKWT') {
                    $pkwtResigned++;
                } elseif ($statusRecord->employee->employment_type === 'PHL') {
                    $phlResigned++;
                }
            }
        }
        $totalResigned = $pkwtResigned + $phlResigned;

        $totalAllTime = $totalManpower + $totalResigned;
        $turnoverRate = $totalAllTime > 0 ? round(($totalResigned / $totalAllTime) * 100, 1) : 0.0;

        $months = [];
        $payrollRealData = [];
        $payrollEstData = [];
        $recruitmentPkwt = [];
        $recruitmentPhl = [];
        $turnoverData = [];
        $monthlyLatenessTrend = [];

        for ($m = 1; $m <= 12; $m++) {
            $date = Carbon::createFromDate($selectedYear, $m, 1);
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
            $resignedCount = EmployeeStatus::whereYear('effective_date', $yearNum)
                ->whereMonth('effective_date', $monthNum)
                ->count();
            $turnoverData[] = $resignedCount;

            // Monthly Payroll real/est totals (in Millions Rp)
            // PKWT
            $mPkwtPeriodTotal = 0;
            $mPkwtPeriods = PkwtPayrollPeriod::whereYear('start_date', $yearNum)
                ->whereMonth('start_date', $monthNum)
                ->with(['attendances.employee', 'overtimes', 'riskAllowances', 'otherAllowances', 'periodTeams'])
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
                    $periodTeam = $period->periodTeams->where('team_id', $employee->team_id)->first();
                    $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);
                    $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
                    $harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
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

            // Calculate monthly lateness rate for trend chart (PKWT - Bulanan)
            $mLateCount = 0;
            $mTotalAttendances = 0;
            foreach ($mPkwtPeriods as $period) {
                foreach ($period->attendances as $att) {
                    $mTotalAttendances++;
                    $lateTime = $att->late_time;
                    if ($lateTime && $lateTime !== '-' && $lateTime !== '00:00') {
                        $mLateCount++;
                    }
                }
            }
            $monthlyPkwtLatenessTrend[] = $mTotalAttendances > 0 ? round(($mLateCount / $mTotalAttendances) * 100, 1) : 0;

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
        $turnoverSparklineData = $turnoverData;

        return view('pages.dashboard.payroll', [
            'title' => 'Dashboard Penggajian',
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,

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
            'recruitmentPhl' => $recruitmentPhl,

            // Lateness props
            'overallLateRate' => $overallLateRate,
            'overallLateRateDiff' => $overallLateRateDiff,
            'pkwtLateRate' => $pkwtLateRate,
            'pkwtLateHours' => $pkwtLateHours,
            'pkwtLateCount' => $pkwtLateCount,
            'pkwtLateRateDiff' => $pkwtLateRateDiff,
            'phlLateRate' => $phlLateRate,
            'phlLateHours' => $phlLateHours,
            'phlLateCount' => $phlLateCount,
            'phlLateRateDiff' => $phlLateRateDiff,
            'monthlyPkwtLatenessTrend' => $monthlyPkwtLatenessTrend,
            'dailyDates' => $dailyDates,
            'dailyLateCounts' => $dailyLateCounts,
            'dailyLateHours' => $dailyLateHours
        ]);
    }
}
