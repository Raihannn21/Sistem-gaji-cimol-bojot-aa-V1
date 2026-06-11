<?php

namespace App\Exports;

use App\Models\PhlPayrollPeriod;
use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class PhlPayrollExport implements FromView, WithTitle, WithColumnWidths
{
    protected $period;

    public function __construct(PhlPayrollPeriod $period)
    {
        $this->period = $period;
    }

    public function view(): View
    {
        $period = PhlPayrollPeriod::with([
            'attendances.employee',
            'overtimes.employee',
            'riskAllowances.employee'
        ])->findOrFail($this->period->id);

        $employees = Employee::where('employment_type', 'PHL')
            ->where(function ($q) use ($period) {
                $q->where(function ($subQ) use ($period) {
                    $subQ->where('status', 'Aktif')
                        ->where('created_at', '<=', \Carbon\Carbon::parse($period->end_date)->endOfDay());
                })
                ->orWhereHas('phlAttendances', function ($sub) use ($period) {
                    $sub->where('phl_payroll_period_id', $period->id);
                });
            })
            ->distinct()
            ->get();

        $rows = [];
        foreach ($employees as $employee) {
            $daysWorked = $period->attendances->where('employee_id', $employee->id)->where('duration', '>', 0)->count();
            $salaryDaily = $employee->salary_daily ?? 0;
            $gajiPokok = $daysWorked * $salaryDaily;

            $totalOvertimeHours = $period->overtimes->where('employee_id', $employee->id)->sum('hours');
            $totalOvertimeAmount = $period->overtimes->where('employee_id', $employee->id)->sum('amount');

            $totalRiskAmount = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
            $totalRiskDays = $period->riskAllowances->where('employee_id', $employee->id)->count();

            $takeHomePay = $gajiPokok + $totalOvertimeAmount + $totalRiskAmount;

            $rows[] = [
                'employee' => $employee,
                'days_worked' => $daysWorked,
                'salary_daily' => $salaryDaily,
                'gaji_pokok' => $gajiPokok,
                'overtime_hours' => $totalOvertimeHours,
                'overtime_amount' => $totalOvertimeAmount,
                'risk_days' => $totalRiskDays,
                'risk_amount' => $totalRiskAmount,
                'take_home_pay' => $takeHomePay,
            ];
        }

        return view('exports.phl-payroll-excel', [
            'period' => $period,
            'rows' => $rows
        ]);
    }

    public function title(): string
    {
        return 'REKAP_PAYROLL_PHL';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 15,
            'C' => 30,
            'D' => 15,
            'E' => 22,
            'F' => 22,
            'G' => 15,
            'H' => 22,
            'I' => 15,
            'J' => 22,
            'K' => 24,
        ];
    }
}
