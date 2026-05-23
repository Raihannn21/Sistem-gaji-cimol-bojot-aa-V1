<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\PhlPayrollPeriod;
use App\Models\PkwtPayrollPeriod;
use Carbon\Carbon;
use App\Exports\MonthlyPayrollReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MonthlyReportController extends Controller
{
    public function index(Request $request)
    {
        $monthName = $request->get('month', Carbon::now()->isoFormat('MMMM'));
        $year = $request->get('year', Carbon::now()->format('Y'));

        $monthsMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];

        $monthNum = $monthsMap[$monthName] ?? Carbon::now()->month;

        $startDate = Carbon::create($year, $monthNum, 1)->startOfMonth();
        $endDate = Carbon::create($year, $monthNum, 1)->endOfMonth();

        $phlPeriods = PhlPayrollPeriod::with(['attendances.employee', 'overtimes', 'riskAllowances'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })->get();

        $pkwtPeriods = PkwtPayrollPeriod::with(['attendances.employee', 'overtimes', 'riskAllowances', 'otherAllowances', 'periodTeams'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })->get();

        $phlEmployeeIds = [];
        $pkwtEmployeeIds = [];

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
                
                $periodTeam = $period->periodTeams->where('team_id', $employee->team_id)->first();
                $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);
                $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
                $harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
                
                $pkwtPokok += ($daysWorked * $harian);

                $pkwtPotongan += ($employee->bpjs_health ?? 0) + ($employee->bpjs_tk ?? 0) + ($employee->pph21 ?? 0);
            }

            $pkwtLembur += $period->overtimes->sum('amount');
            $pkwtRisiko += $period->riskAllowances->sum('amount');
            $pkwtLain += $period->otherAllowances->sum('amount');
        }

        $totalPhlEmployees = count($phlEmployeeIds);
        $totalPkwtEmployees = count($pkwtEmployeeIds);
        $totalEmployees = $totalPhlEmployees + $totalPkwtEmployees;

        $totalPhlCost = $phlPokok + $phlLembur + $phlRisiko;
        $totalPkwtCost = max(0, $pkwtPokok + $pkwtLembur + $pkwtRisiko + $pkwtLain - $pkwtPotongan);
        $totalPayroll = $totalPhlCost + $totalPkwtCost;

        $totalLembur = $phlLembur + $pkwtLembur;
        $totalPotongan = $pkwtPotongan;

        return view('pages.reports.monthly', [
            'title' => 'Rekap Bulanan',
            'selectedMonth' => $monthName,
            'selectedYear' => $year,
            'totalPayroll' => $totalPayroll,
            'totalEmployees' => $totalEmployees,
            'totalPhlEmployees' => $totalPhlEmployees,
            'totalPkwtEmployees' => $totalPkwtEmployees,
            'totalLembur' => $totalLembur,
            'totalPotongan' => $totalPotongan,
            'phlPokok' => $phlPokok,
            'phlLembur' => $phlLembur,
            'phlRisiko' => $phlRisiko,
            'pkwtPokok' => $pkwtPokok,
            'pkwtLembur' => $pkwtLembur,
            'pkwtRisiko' => $pkwtRisiko,
            'pkwtLain' => $pkwtLain,
            'pkwtPotongan' => $pkwtPotongan,
            'totalPhlCost' => $totalPhlCost,
            'totalPkwtCost' => $totalPkwtCost,
            'phlPeriods' => $phlPeriods,
            'pkwtPeriods' => $pkwtPeriods
        ]);
    }

    public function exportPdf(Request $request)
    {
        $monthName = $request->get('month', Carbon::now()->isoFormat('MMMM'));
        $year = $request->get('year', Carbon::now()->format('Y'));
        $monthsMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];

        $monthNum = $monthsMap[$monthName] ?? Carbon::now()->month;

        $startDate = Carbon::create($year, $monthNum, 1)->startOfMonth();
        $endDate = Carbon::create($year, $monthNum, 1)->endOfMonth();

        $phlPeriods = PhlPayrollPeriod::with(['attendances.employee', 'overtimes', 'riskAllowances'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })->get();

        $pkwtPeriods = PkwtPayrollPeriod::with(['attendances.employee', 'overtimes', 'riskAllowances', 'otherAllowances', 'periodTeams'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })->get();

        $phlEmployeeIds = [];
        $pkwtEmployeeIds = [];

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
                
                $periodTeam = $period->periodTeams->where('team_id', $employee->team_id)->first();
                $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);
                $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
                $harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
                
                $pkwtPokok += ($daysWorked * $harian);
                $pkwtPotongan += ($employee->bpjs_health ?? 0) + ($employee->bpjs_tk ?? 0) + ($employee->pph21 ?? 0);
            }

            $pkwtLembur += $period->overtimes->sum('amount');
            $pkwtRisiko += $period->riskAllowances->sum('amount');
            $pkwtLain += $period->otherAllowances->sum('amount');
        }

        $totalPhlEmployees = count($phlEmployeeIds);
        $totalPkwtEmployees = count($pkwtEmployeeIds);
        $totalEmployees = $totalPhlEmployees + $totalPkwtEmployees;

        $totalPhlCost = $phlPokok + $phlLembur + $phlRisiko;
        $totalPkwtCost = max(0, $pkwtPokok + $pkwtLembur + $pkwtRisiko + $pkwtLain - $pkwtPotongan);
        $totalPayroll = $totalPhlCost + $totalPkwtCost;

        $totalLembur = $phlLembur + $pkwtLembur;
        $totalPotongan = $pkwtPotongan;

        $pdf = Pdf::loadView('exports.monthly-payroll-report', [
            'selectedMonth' => $monthName,
            'selectedYear' => $year,
            'totalPayroll' => $totalPayroll,
            'totalEmployees' => $totalEmployees,
            'totalPhlEmployees' => $totalPhlEmployees,
            'totalPkwtEmployees' => $totalPkwtEmployees,
            'totalLembur' => $totalLembur,
            'totalPotongan' => $totalPotongan,
            'phlPokok' => $phlPokok,
            'phlLembur' => $phlLembur,
            'phlRisiko' => $phlRisiko,
            'pkwtPokok' => $pkwtPokok,
            'pkwtLembur' => $pkwtLembur,
            'pkwtRisiko' => $pkwtRisiko,
            'pkwtLain' => $pkwtLain,
            'pkwtPotongan' => $pkwtPotongan,
            'totalPhlCost' => $totalPhlCost,
            'totalPkwtCost' => $totalPkwtCost,
            'phlPeriods' => $phlPeriods,
            'pkwtPeriods' => $pkwtPeriods
        ]);

        $fileName = 'LAPORAN_REKAPITULASI_GAJI_' . strtoupper($monthName) . '_' . $year . '.pdf';
        return $pdf->stream($fileName);
    }

    public function exportExcel(Request $request)
    {
        $monthName = $request->get('month', Carbon::now()->isoFormat('MMMM'));
        $year = $request->get('year', Carbon::now()->format('Y'));

        $monthsMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];

        $monthNum = $monthsMap[$monthName] ?? Carbon::now()->month;

        $startDate = Carbon::create($year, $monthNum, 1)->startOfMonth();
        $endDate = Carbon::create($year, $monthNum, 1)->endOfMonth();

        $phlPeriods = PhlPayrollPeriod::with(['attendances.employee', 'overtimes', 'riskAllowances'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })->get();

        $pkwtPeriods = PkwtPayrollPeriod::with(['attendances.employee', 'overtimes', 'riskAllowances', 'otherAllowances', 'periodTeams'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })->get();

        $phlEmployeeIds = [];
        $pkwtEmployeeIds = [];

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
                
                $periodTeam = $period->periodTeams->where('team_id', $employee->team_id)->first();
                $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);
                $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
                $harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
                
                $pkwtPokok += ($daysWorked * $harian);
                $pkwtPotongan += ($employee->bpjs_health ?? 0) + ($employee->bpjs_tk ?? 0) + ($employee->pph21 ?? 0);
            }

            $pkwtLembur += $period->overtimes->sum('amount');
            $pkwtRisiko += $period->riskAllowances->sum('amount');
            $pkwtLain += $period->otherAllowances->sum('amount');
        }

        $totalPhlEmployees = count($phlEmployeeIds);
        $totalPkwtEmployees = count($pkwtEmployeeIds);
        $totalEmployees = $totalPhlEmployees + $totalPkwtEmployees;

        $totalPhlCost = $phlPokok + $phlLembur + $phlRisiko;
        $totalPkwtCost = max(0, $pkwtPokok + $pkwtLembur + $pkwtRisiko + $pkwtLain - $pkwtPotongan);
        $totalPayroll = $totalPhlCost + $totalPkwtCost;

        $totalLembur = $phlLembur + $pkwtLembur;
        $totalPotongan = $pkwtPotongan;

        $data = [
            'selectedMonth' => $monthName,
            'selectedYear' => $year,
            'totalPayroll' => $totalPayroll,
            'totalEmployees' => $totalEmployees,
            'totalPhlEmployees' => $totalPhlEmployees,
            'totalPkwtEmployees' => $totalPkwtEmployees,
            'totalLembur' => $totalLembur,
            'totalPotongan' => $totalPotongan,
            'phlPokok' => $phlPokok,
            'phlLembur' => $phlLembur,
            'phlRisiko' => $phlRisiko,
            'pkwtPokok' => $pkwtPokok,
            'pkwtLembur' => $pkwtLembur,
            'pkwtRisiko' => $pkwtRisiko,
            'pkwtLain' => $pkwtLain,
            'pkwtPotongan' => $pkwtPotongan,
            'totalPhlCost' => $totalPhlCost,
            'totalPkwtCost' => $totalPkwtCost,
            'phlPeriods' => $phlPeriods,
            'pkwtPeriods' => $pkwtPeriods
        ];

        $fileName = 'LAPORAN_REKAPITULASI_GAJI_' . strtoupper($monthName) . '_' . $year . '.xlsx';
        return Excel::download(new MonthlyPayrollReportExport($data), $fileName);
    }
}
