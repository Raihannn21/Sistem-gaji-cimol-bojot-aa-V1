<?php

namespace App\Exports;

use App\Models\PhlPayrollPeriod;
use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;

class BcaPayrollExport extends DefaultValueBinder implements FromView, WithTitle, WithColumnWidths, WithCustomValueBinder
{
    protected $period;

    public function __construct(PhlPayrollPeriod $period)
    {
        $this->period = $period;
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() === 'E' && is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
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

            if ($takeHomePay > 0) {
                $rows[] = [
                    'transfer_type' => $employee->bank_name ?: 'BCA',
                    'credited_account' => $employee->bank_account ?: '',
                    'receiver_name' => $employee->name,
                    'amount' => $takeHomePay,
                    'remark' => $period->title,
                ];
            }
        }

        return view('exports.phl-bca-excel', [
            'period' => $period,
            'rows' => $rows
        ]);
    }

    public function title(): string
    {
        return 'BCA_TRANSFER_LIST';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 15,
            'C' => 15,
            'D' => 15,
            'E' => 25,
            'F' => 30,
            'G' => 20,
            'H' => 12,
            'I' => 35,
            'J' => 25,
            'K' => 20,
            'L' => 20,
            'M' => 20,
        ];
    }
}
