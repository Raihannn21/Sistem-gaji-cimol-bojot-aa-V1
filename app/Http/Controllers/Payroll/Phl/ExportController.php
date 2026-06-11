<?php

namespace App\Http\Controllers\Payroll\Phl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhlPayrollPeriod;
use App\Models\Employee;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PhlPayrollExport;
use App\Exports\BcaPayrollExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\SalarySlipMail;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function exportExcel($id)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        $fileName = 'REKAP_PAYROLL_PHL_' . str_replace(' ', '_', strtoupper($period->title)) . '.xlsx';

        return Excel::download(new PhlPayrollExport($period), $fileName);
    }

    public function exportBca($id)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        $fileName = 'BCA_TRANSFER_LIST_PHL_' . str_replace(' ', '_', strtoupper($period->title)) . '.xlsx';

        return Excel::download(new BcaPayrollExport($period), $fileName);
    }

    public function exportPdf($id)
    {
        $period = PhlPayrollPeriod::with([
            'attendances.employee',
            'overtimes.employee',
            'riskAllowances.employee'
        ])->findOrFail($id);

        $employees = Employee::where('employment_type', 'PHL')
            ->where(function ($q) use ($period) {
                $q->where(function ($subQ) use ($period) {
                    $subQ->where('status', 'Aktif')
                        ->where('created_at', '<=', $period->end_date . ' 23:59:59');
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

        $pdf = Pdf::loadView('exports.phl-payroll', [
            'period' => $period,
            'rows' => $rows
        ])->setPaper('a4', 'landscape');

        $fileName = 'REKAP_PAYROLL_PHL_' . str_replace(' ', '_', strtoupper($period->title)) . '.pdf';
        return $pdf->download($fileName);
    }

    public function exportIndividualPdf($id, $employeeId)
    {
        $period = PhlPayrollPeriod::with(['attendances.team', 'overtimes', 'riskAllowances'])->findOrFail($id);
        $employee = Employee::where('employment_type', 'PHL')->findOrFail($employeeId);

        $daysWorked = $period->attendances->where('employee_id', $employee->id)->where('duration', '>', 0)->count();
        $salaryDaily = $employee->salary_daily ?? 0;
        $gajiPokok = $daysWorked * $salaryDaily;

        $totalOvertimeHours = $period->overtimes->where('employee_id', $employee->id)->sum('hours');
        $totalOvertimeAmount = $period->overtimes->where('employee_id', $employee->id)->sum('amount');

        $totalRiskAmount = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
        $totalRiskDays = $period->riskAllowances->where('employee_id', $employee->id)->count();

        $takeHomePay = $gajiPokok + $totalOvertimeAmount + $totalRiskAmount;

        $employeeAttendance = $period->attendances->where('employee_id', $employee->id)->first();
        $resolvedTeam = ($period->status === 'Locked' && $employeeAttendance && $employeeAttendance->team_id)
            ? $employeeAttendance->team
            : $employee->team;
        $team_name = $resolvedTeam ? $resolvedTeam->name : '-';

        $totalDays = $period->start_date->diffInDays($period->end_date) + 1;

        $pdf = Pdf::loadView('exports.phl-individual-slip', [
            'period' => $period,
            'employee' => $employee,
            'days_worked' => $daysWorked,
            'total_days' => $totalDays,
            'salary_daily' => $salaryDaily,
            'gaji_pokok' => $gajiPokok,
            'overtime_hours' => $totalOvertimeHours,
            'overtime_amount' => $totalOvertimeAmount,
            'risk_days' => $totalRiskDays,
            'risk_amount' => $totalRiskAmount,
            'take_home_pay' => $takeHomePay,
            'team_name' => $team_name,
        ])->setPaper('a5', 'landscape');

        $fileName = 'SLIP_GAJI_' . str_replace(' ', '_', strtoupper($employee->name)) . '_' . str_replace(' ', '_', strtoupper($period->title)) . '.pdf';
        return $pdf->stream($fileName);
    }

    public function sendIndividualSlip($id, $employeeId)
    {
        $period = PhlPayrollPeriod::with(['attendances.team', 'overtimes', 'riskAllowances'])->findOrFail($id);
        $employee = Employee::where('employment_type', 'PHL')->findOrFail($employeeId);

        if (empty($employee->email)) {
            return back()->with('error', 'Karyawan ' . $employee->name . ' tidak memiliki alamat email yang terdaftar. Harap perbarui data karyawan di menu Karyawan.');
        }

        $daysWorked = $period->attendances->where('employee_id', $employee->id)->where('duration', '>', 0)->count();
        $salaryDaily = $employee->salary_daily ?? 0;
        $gajiPokok = $daysWorked * $salaryDaily;

        $totalOvertimeHours = $period->overtimes->where('employee_id', $employee->id)->sum('hours');
        $totalOvertimeAmount = $period->overtimes->where('employee_id', $employee->id)->sum('amount');

        $totalRiskAmount = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
        $totalRiskDays = $period->riskAllowances->where('employee_id', $employee->id)->count();

        $takeHomePay = $gajiPokok + $totalOvertimeAmount + $totalRiskAmount;

        $employeeAttendance = $period->attendances->where('employee_id', $employee->id)->first();
        $resolvedTeam = ($period->status === 'Locked' && $employeeAttendance && $employeeAttendance->team_id)
            ? $employeeAttendance->team
            : $employee->team;
        $team_name = $resolvedTeam ? $resolvedTeam->name : '-';

        $totalDays = $period->start_date->diffInDays($period->end_date) + 1;

        $pdf = Pdf::loadView('exports.phl-individual-slip', [
            'period' => $period,
            'employee' => $employee,
            'days_worked' => $daysWorked,
            'total_days' => $totalDays,
            'salary_daily' => $salaryDaily,
            'gaji_pokok' => $gajiPokok,
            'overtime_hours' => $totalOvertimeHours,
            'overtime_amount' => $totalOvertimeAmount,
            'risk_days' => $totalRiskDays,
            'risk_amount' => $totalRiskAmount,
            'take_home_pay' => $takeHomePay,
            'team_name' => $team_name,
        ])->setPaper('a5', 'landscape');

        $pdfData = $pdf->output();
        $fileName = 'SLIP_GAJI_' . str_replace(' ', '_', strtoupper($employee->name)) . '_' . str_replace(' ', '_', strtoupper($period->title)) . '.pdf';

        try {
            Mail::to($employee->email)->send(new SalarySlipMail($employee, $period->title, $takeHomePay, $pdfData, $fileName));
            return back()->with('success', 'Slip gaji berhasil dikirim ke email ' . $employee->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    public function sendAllSlips($id)
    {
        $period = PhlPayrollPeriod::with(['attendances.team', 'overtimes', 'riskAllowances'])->findOrFail($id);
        $totalDays = $period->start_date->diffInDays($period->end_date) + 1;
        
        $employees = Employee::where('employment_type', 'PHL')
            ->where(function ($q) use ($period) {
                $q->where(function ($subQ) use ($period) {
                    $subQ->where('status', 'Aktif')
                        ->where('created_at', '<=', $period->end_date . ' 23:59:59');
                })
                ->orWhereHas('phlAttendances', function ($sub) use ($period) {
                    $sub->where('phl_payroll_period_id', $period->id);
                });
            })
            ->distinct()
            ->get();

        $successCount = 0;
        $failCount = 0;

        foreach ($employees as $employee) {
            if (empty($employee->email)) {
                $failCount++;
                continue;
            }

            $daysWorked = $period->attendances->where('employee_id', $employee->id)->where('duration', '>', 0)->count();
            $salaryDaily = $employee->salary_daily ?? 0;
            $gajiPokok = $daysWorked * $salaryDaily;

            $totalOvertimeHours = $period->overtimes->where('employee_id', $employee->id)->sum('hours');
            $totalOvertimeAmount = $period->overtimes->where('employee_id', $employee->id)->sum('amount');

            $totalRiskAmount = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
            $totalRiskDays = $period->riskAllowances->where('employee_id', $employee->id)->count();

            $takeHomePay = $gajiPokok + $totalOvertimeAmount + $totalRiskAmount;

            $employeeAttendance = $period->attendances->where('employee_id', $employee->id)->first();
            $resolvedTeam = ($period->status === 'Locked' && $employeeAttendance && $employeeAttendance->team_id)
                ? $employeeAttendance->team
                : $employee->team;
            $team_name = $resolvedTeam ? $resolvedTeam->name : '-';

            if ($takeHomePay >= 0) {
                $pdf = Pdf::loadView('exports.phl-individual-slip', [
                    'period' => $period,
                    'employee' => $employee,
                    'days_worked' => $daysWorked,
                    'total_days' => $totalDays,
                    'salary_daily' => $salaryDaily,
                    'gaji_pokok' => $gajiPokok,
                    'overtime_hours' => $totalOvertimeHours,
                    'overtime_amount' => $totalOvertimeAmount,
                    'risk_days' => $totalRiskDays,
                    'risk_amount' => $totalRiskAmount,
                    'take_home_pay' => $takeHomePay,
                    'team_name' => $team_name,
                ])->setPaper('a5', 'landscape');

                $pdfData = $pdf->output();
                $fileName = 'SLIP_GAJI_' . str_replace(' ', '_', strtoupper($employee->name)) . '_' . str_replace(' ', '_', strtoupper($period->title)) . '.pdf';

                try {
                    Mail::to($employee->email)->send(new SalarySlipMail($employee, $period->title, $takeHomePay, $pdfData, $fileName));
                    $successCount++;
                } catch (\Exception $e) {
                    $failCount++;
                }
            }
        }

        $msg = "Proses selesai. Berhasil mengirim $successCount email.";
        if ($failCount > 0) {
            $msg .= " Namun, $failCount karyawan gagal dikirim (mungkin tidak ada alamat email atau error SMTP).";
            return back()->with('warning', $msg);
        }

        return back()->with('success', $msg);
    }
}
