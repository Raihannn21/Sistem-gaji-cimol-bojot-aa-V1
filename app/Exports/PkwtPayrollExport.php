<?php

namespace App\Exports;

use App\Models\PkwtPayrollPeriod;
use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class PkwtPayrollExport implements FromView, WithTitle, WithColumnWidths
{
    protected $period;

    public function __construct(PkwtPayrollPeriod $period)
    {
        $this->period = $period;
    }

    public function view(): View
    {
        $period = PkwtPayrollPeriod::with([
            'attendances.employee',
            'overtimes.employee',
            'riskAllowances.employee',
            'otherAllowances.employee'
        ])->findOrFail($this->period->id);

        $employees = Employee::where('employment_type', 'PKWT')
            ->where(function ($q) use ($period) {
                $q->where('status', 'Aktif')
                    ->orWhereHas('pkwtAttendances', function ($sub) use ($period) {
                        $sub->where('pkwt_payroll_period_id', $period->id);
                    });
            })
            ->distinct()
            ->get();

        $startDate = \Carbon\Carbon::parse($period->start_date);
        $endDate = \Carbon\Carbon::parse($period->end_date);
        $totalPeriodDays = $startDate->diffInDays($endDate) + 1;

        $rows = [];
        foreach ($employees as $employee) {
            $daysWorked = $period->attendances->where('employee_id', $employee->id)->count();
            $daysAbsent = max(0, $totalPeriodDays - $daysWorked);
            $harian = $totalPeriodDays > 0 ? ($employee->salary_monthly / $totalPeriodDays) : 0;
            $pokok = $daysWorked * $harian;

            $lembur = $period->overtimes->where('employee_id', $employee->id)->sum('amount');
            $risiko = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
            $tunjanganLain = $period->otherAllowances->where('employee_id', $employee->id)->sum('amount');

            $potongan = ($employee->bpjs_health ?? 0) + ($employee->bpjs_tk ?? 0) + ($employee->pph21 ?? 0);
            $total = max(0, $pokok + $lembur + $risiko + $tunjanganLain - $potongan);

            if ($daysWorked > 0 || $lembur > 0 || $risiko > 0 || $tunjanganLain > 0) {
                $rows[] = [
                    'employee' => $employee,
                    'days_worked' => $daysWorked,
                    'days_absent' => $daysAbsent,
                    'tarif_harian' => $harian,
                    'gaji_pokok_didapat' => $pokok,
                    'lembur' => $lembur,
                    'risiko' => $risiko,
                    'lain_lain' => $tunjanganLain,
                    'potongan' => $potongan,
                    'total_bersih' => $total,
                ];
            }
        }

        return view('exports.pkwt-payroll-excel', [
            'period' => $period,
            'rows' => $rows
        ]);
    }

    public function title(): string
    {
        return 'REKAP_PAYROLL_PKWT';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 15,
            'C' => 30,
            'D' => 15,
            'E' => 15,
            'F' => 20,
            'G' => 24,
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'L' => 24,
        ];
    }
}
