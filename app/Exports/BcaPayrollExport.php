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
        // Force Credited Account column (E) to be a string to avoid scientific notation
        if ($cell->getColumn() === 'E' && is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        // Force other string columns to stay as string
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
            ->where(function($q) use ($period) {
                $q->where('status', 'Aktif')
                  ->orWhereHas('phlAttendances', function($sub) use ($period) {
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

            if ($daysWorked > 0 || $totalOvertimeHours > 0 || $totalRiskAmount > 0) {
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
            'A' => 6,   // No
            'B' => 15,  // Transaction ID
            'C' => 15,  // Transfer Type
            'D' => 15,  // Beneficiary ID
            'E' => 25,  // Credited Account
            'F' => 30,  // Receiver Name
            'G' => 20,  // Amount
            'H' => 12,  // NIP
            'I' => 35,  // Remark
            'J' => 25,  // Beneficiary email address
            'K' => 20,  // Receiver Swift Code
            'L' => 20,  // Receiver Cust Type
            'M' => 20,  // Receiver Cust Residence
        ];
    }
}
