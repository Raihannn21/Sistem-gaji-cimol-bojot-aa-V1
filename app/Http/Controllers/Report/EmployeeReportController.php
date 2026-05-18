<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PhlPayrollPeriod;
use App\Models\PkwtPayrollPeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeReportController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('name')->get();
        return view('pages.reports.employee', [
            'title' => 'Laporan Individu',
            'employees' => $employees
        ]);
    }

    public function history($id)
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
