<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PhlPayrollPeriod;
use App\Models\PkwtPayrollPeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SummaryReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', Carbon::now()->format('Y'));

        $currentYear = intval(Carbon::now()->format('Y'));
        $years = range(2025, max($currentYear, 2026) + 1);

        $monthsName = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $summaryData = [];
        $totalAnnualPkwtCost = 0;
        $totalAnnualPhlCost = 0;

        $globalPkwtPokok = 0;
        $globalPkwtLemburTunjangan = 0;

        $globalPhlPokok = 0;
        $globalPhlLemburTunjangan = 0;

        foreach ($monthsName as $num => $name) {
            $startDate = Carbon::create($year, $num, 1)->startOfMonth();
            $endDate = Carbon::create($year, $num, 1)->endOfMonth();

            $phlPeriods = PhlPayrollPeriod::with(['attendances.employee', 'overtimes', 'riskAllowances'])
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q2) use ($startDate, $endDate) {
                            $q2->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })->get();

            $phlEmployeeIds = [];
            $phlPokok = 0;
            $phlLembur = 0;
            $phlRisiko = 0;

            foreach ($phlPeriods as $period) {
                foreach ($period->attendances as $attendance) {
                    if ($attendance->duration > 0) {
                        $phlEmployeeIds[$attendance->employee_id] = true;
                        $phlPokok += ($attendance->employee->salary_daily ?? 0);
                    }
                }
                $phlLembur += $period->overtimes->sum('amount');
                $phlRisiko += $period->riskAllowances->sum('amount');
            }

            $phlCost = $phlPokok + $phlLembur + $phlRisiko;
            $totalAnnualPhlCost += $phlCost;

            $globalPhlPokok += $phlPokok;
            $globalPhlLemburTunjangan += ($phlLembur + $phlRisiko);

            $pkwtPeriods = PkwtPayrollPeriod::with(['attendances.employee', 'overtimes', 'riskAllowances', 'otherAllowances'])
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q2) use ($startDate, $endDate) {
                            $q2->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })->get();

            $pkwtEmployeeIds = [];
            $pkwtPokok = 0;
            $pkwtLembur = 0;
            $pkwtRisiko = 0;
            $pkwtLain = 0;
            $pkwtPotongan = 0;

            foreach ($pkwtPeriods as $period) {
                $totalPeriodDays = Carbon::parse($period->start_date)->diffInDays(Carbon::parse($period->end_date)) + 1;

                $periodEmployeeIds = $period->attendances->pluck('employee_id')->unique();
                foreach ($periodEmployeeIds as $empId) {
                    $pkwtEmployeeIds[$empId] = true;
                }

                $employees = Employee::whereIn('id', $periodEmployeeIds)->get();
                foreach ($employees as $employee) {
                    $daysWorked = $period->attendances->where('employee_id', $employee->id)->count();
                    $harian = $totalPeriodDays > 0 ? ($employee->salary_monthly / $totalPeriodDays) : 0;
                    $pkwtPokok += ($daysWorked * $harian);
                    $pkwtPotongan += ($employee->bpjs_health ?? 0) + ($employee->bpjs_tk ?? 0) + ($employee->pph21 ?? 0);
                }

                $pkwtLembur += $period->overtimes->sum('amount');
                $pkwtRisiko += $period->riskAllowances->sum('amount');
                $pkwtLain += $period->otherAllowances->sum('amount');
            }

            $pkwtCost = max(0, $pkwtPokok + $pkwtLembur + $pkwtRisiko + $pkwtLain - $pkwtPotongan);
            $totalAnnualPkwtCost += $pkwtCost;

            $globalPkwtPokok += ($pkwtPokok - $pkwtPotongan);
            $globalPkwtLemburTunjangan += ($pkwtLembur + $pkwtRisiko + $pkwtLain);

            $summaryData[] = [
                'month' => $name,
                'pkwt_count' => count($pkwtEmployeeIds),
                'phl_count' => count($phlEmployeeIds),
                'pkwt_cost' => $pkwtCost,
                'phl_cost' => $phlCost,
                'total_cost' => $pkwtCost + $phlCost
            ];
        }

        $totalAnnualPayroll = $totalAnnualPkwtCost + $totalAnnualPhlCost;

        $pkwtShare = $totalAnnualPayroll > 0 ? ($totalAnnualPkwtCost / $totalAnnualPayroll) * 100 : 0;
        $phlShare = $totalAnnualPayroll > 0 ? ($totalAnnualPhlCost / $totalAnnualPayroll) * 100 : 0;

        $pkwtPokokPercent = $totalAnnualPkwtCost > 0 ? ($globalPkwtPokok / $totalAnnualPkwtCost) * 100 : 0;
        $pkwtLemburTunjanganPercent = $totalAnnualPkwtCost > 0 ? ($globalPkwtLemburTunjangan / $totalAnnualPkwtCost) * 100 : 0;

        $phlPokokPercent = $totalAnnualPhlCost > 0 ? ($globalPhlPokok / $totalAnnualPhlCost) * 100 : 0;
        $phlLemburTunjanganPercent = $totalAnnualPhlCost > 0 ? ($globalPhlLemburTunjangan / $totalAnnualPhlCost) * 100 : 0;

        return view('pages.reports.summary', [
            'title' => 'Rekap PHL & PKWT',
            'selectedYear' => $year,
            'years' => $years,
            'summaryData' => $summaryData,
            'totalAnnualPkwtCost' => $totalAnnualPkwtCost,
            'totalAnnualPhlCost' => $totalAnnualPhlCost,
            'totalAnnualPayroll' => $totalAnnualPayroll,
            'pkwtShare' => $pkwtShare,
            'phlShare' => $phlShare,
            'pkwtPokokPercent' => $pkwtPokokPercent,
            'pkwtLemburTunjanganPercent' => $pkwtLemburTunjanganPercent,
            'phlPokokPercent' => $phlPokokPercent,
            'phlLemburTunjanganPercent' => $phlLemburTunjanganPercent
        ]);
    }
}
