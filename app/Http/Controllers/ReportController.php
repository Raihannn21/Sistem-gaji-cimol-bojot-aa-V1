<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\PhlPayrollPeriod;
use App\Models\PkwtPayrollPeriod;
use Carbon\Carbon;
use App\Exports\MonthlyPayrollReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function monthly(Request $request)
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

        $pkwtPeriods = PkwtPayrollPeriod::with(['attendances.employee', 'overtimes', 'riskAllowances', 'otherAllowances'])
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
                $harian = $totalPeriodDays > 0 ? ($employee->salary_monthly / $totalPeriodDays) : 0;
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

    public function exportMonthlyPdf(Request $request)
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

        $pkwtPeriods = PkwtPayrollPeriod::with(['attendances.employee', 'overtimes', 'riskAllowances', 'otherAllowances'])
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
                $harian = $totalPeriodDays > 0 ? ($employee->salary_monthly / $totalPeriodDays) : 0;
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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.monthly-payroll-report', [
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

    public function exportMonthlyExcel(Request $request)
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

        $pkwtPeriods = PkwtPayrollPeriod::with(['attendances.employee', 'overtimes', 'riskAllowances', 'otherAllowances'])
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
                $harian = $totalPeriodDays > 0 ? ($employee->salary_monthly / $totalPeriodDays) : 0;
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

    public function summary(Request $request)
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

    public function employee()
    {
        $employees = Employee::orderBy('name')->get();
        return view('pages.reports.employee', [
            'title' => 'Laporan Individu',
            'employees' => $employees
        ]);
    }

    public function employeeHistory($id)
    {
        $employee = Employee::findOrFail($id);
        $history = [];

        if ($employee->employment_type === 'PHL') {
            $periods = PhlPayrollPeriod::with([
                'attendances' => function ($q) use ($id) {
                    $q->where('employee_id', $id);
                },
                'overtimes' => function ($q) use ($id) {
                    $q->where('employee_id', $id);
                },
                'riskAllowances' => function ($q) use ($id) {
                    $q->where('employee_id', $id);
                }
            ])
                ->whereHas('attendances', function ($q) use ($id) {
                    $q->where('employee_id', $id);
                })
                ->orderBy('start_date', 'desc')
                ->get();

            foreach ($periods as $period) {
                $daysWorked = $period->attendances->where('duration', '>', 0)->count();
                $pokok = $daysWorked * ($employee->salary_daily ?? 0);
                $lembur = $period->overtimes->sum('amount');
                $risiko = $period->riskAllowances->sum('amount');
                $totalBersih = $pokok + $lembur + $risiko;

                if ($daysWorked > 0 || $lembur > 0 || $risiko > 0) {
                    $history[] = [
                        'period_id' => $period->id,
                        'period' => $period->title,
                        'days_worked' => $daysWorked,
                        'type' => 'PHL',
                        'gaji_pokok' => $pokok,
                        'salary_daily' => $employee->salary_daily ?? 0,
                        'lembur' => $lembur,
                        'tunjangan_risiko' => $risiko,
                        'tunjangan_lainnya' => 0,
                        'bpjs_kesehatan' => 0,
                        'bpjs_tk' => 0,
                        'pajak' => 0,
                        'total' => number_format($totalBersih, 0, ',', '.'),
                        'raw_total' => $totalBersih
                    ];
                }
            }
        } else {
            // PKWT
            $periods = PkwtPayrollPeriod::with([
                'attendances' => function ($q) use ($id) {
                    $q->where('employee_id', $id);
                },
                'overtimes' => function ($q) use ($id) {
                    $q->where('employee_id', $id);
                },
                'riskAllowances' => function ($q) use ($id) {
                    $q->where('employee_id', $id);
                },
                'otherAllowances' => function ($q) use ($id) {
                    $q->where('employee_id', $id);
                }
            ])
                ->whereHas('attendances', function ($q) use ($id) {
                    $q->where('employee_id', $id);
                })
                ->orderBy('start_date', 'desc')
                ->get();

            foreach ($periods as $period) {
                $daysWorked = $period->attendances->count();
                $totalPeriodDays = Carbon::parse($period->start_date)->diffInDays(Carbon::parse($period->end_date)) + 1;
                $harian = $totalPeriodDays > 0 ? ($employee->salary_monthly / $totalPeriodDays) : 0;
                $pokok = $daysWorked * $harian;

                $lembur = $period->overtimes->sum('amount');
                $risiko = $period->riskAllowances->sum('amount');
                $other = $period->otherAllowances->sum('amount');

                $bpjsHealth = $employee->bpjs_health ?? 0;
                $bpjsTk = $employee->bpjs_tk ?? 0;
                $pph21 = $employee->pph21 ?? 0;
                $potongan = $bpjsHealth + $bpjsTk + $pph21;

                $totalBersih = max(0, $pokok + $lembur + $risiko + $other - $potongan);

                if ($daysWorked > 0 || $lembur > 0 || $risiko > 0 || $other > 0) {
                    $history[] = [
                        'period_id' => $period->id,
                        'period' => $period->title,
                        'days_worked' => $daysWorked,
                        'type' => 'PKWT',
                        'gaji_pokok' => $pokok,
                        'total_days' => $totalPeriodDays,
                        'tarif_harian' => $harian,
                        'lembur' => $lembur,
                        'tunjangan_risiko' => $risiko,
                        'tunjangan_lainnya' => $other,
                        'bpjs_kesehatan' => $bpjsHealth,
                        'bpjs_tk' => $bpjsTk,
                        'pajak' => $pph21,
                        'total' => number_format($totalBersih, 0, ',', '.'),
                        'raw_total' => $totalBersih
                    ];
                }
            }
        }

        return response()->json($history);
    }
}
