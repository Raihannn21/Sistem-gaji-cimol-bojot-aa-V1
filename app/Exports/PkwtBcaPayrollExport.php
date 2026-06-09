<?php

namespace App\Exports;

use App\Models\PkwtPayrollPeriod;
use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;

class PkwtBcaPayrollExport extends DefaultValueBinder implements FromView, WithTitle, WithColumnWidths, WithCustomValueBinder
{
    protected $period;

    public function __construct(PkwtPayrollPeriod $period)
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
        $period = PkwtPayrollPeriod::with([
            'attendances.employee',
            'overtimes.employee',
            'riskAllowances.employee',
            'otherAllowances.employee',
            'periodTeams'
        ])->findOrFail($this->period->id);

        $selectedTeamIds = $period->periodTeams->pluck('team_id')->toArray();
        $employees = Employee::where('employment_type', 'PKWT')
            ->where(function ($q) use ($period, $selectedTeamIds) {
                $q->where(function($subQ) use ($selectedTeamIds) {
                    $subQ->where('status', 'Aktif')
                        ->whereIn('team_id', $selectedTeamIds);
                })
                ->orWhereHas('pkwtAttendances', function ($sub) use ($period) {
                    $sub->where('pkwt_payroll_period_id', $period->id);
                })
                ->orWhereHas('pkwtOvertimes', function ($sub) use ($period) {
                    $sub->where('pkwt_payroll_period_id', $period->id);
                })
                ->orWhereHas('pkwtRiskAllowances', function ($sub) use ($period) {
                    $sub->where('pkwt_payroll_period_id', $period->id);
                })
                ->orWhereHas('pkwtOtherAllowances', function ($sub) use ($period) {
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

            $periodTeam = $period->periodTeams->where('team_id', $employee->team_id)->first();
            $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);

            $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
            $harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
            $pokok = $daysWorked * $harian;

            $lembur = $period->overtimes->where('employee_id', $employee->id)->sum('amount');
            $risiko = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
            $tunjanganLain = $period->otherAllowances->where('employee_id', $employee->id)->sum('amount');

            $potongan = ($employee->bpjs_health ?? 0) + ($employee->bpjs_tk ?? 0) + ($employee->pph21 ?? 0);
            $total = max(0, $pokok + $lembur + $risiko + $tunjanganLain - $potongan);

            if ($daysWorked > 0 || $lembur > 0 || $risiko > 0 || $tunjanganLain > 0) {
                $rows[] = [
                    'transfer_type' => $employee->bank_name ?: 'BCA',
                    'credited_account' => $employee->bank_account ?: '',
                    'receiver_name' => $employee->name,
                    'amount' => $total,
                    'remark' => $period->title,
                ];
            }
        }

        return view('exports.pkwt-bca-excel', [
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
