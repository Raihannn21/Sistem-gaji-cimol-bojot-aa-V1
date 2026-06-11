<?php

namespace App\Http\Controllers\Payroll\Pkwt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PkwtPayrollPeriod;
use App\Models\Employee;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PkwtPayrollExport;
use App\Exports\PkwtBcaPayrollExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\SalarySlipMail;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function exportExcel($id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        $fileName = 'REKAP_PAYROLL_PKWT_' . str_replace(' ', '_', strtoupper($period->title)) . '.xlsx';

        return Excel::download(new PkwtPayrollExport($period), $fileName);
    }

    public function exportBca($id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        $fileName = 'BCA_TRANSFER_LIST_PKWT_' . str_replace(' ', '_', strtoupper($period->title)) . '.xlsx';

        return Excel::download(new PkwtBcaPayrollExport($period), $fileName);
    }

    public function exportPdf($id)
    {
        $period = PkwtPayrollPeriod::with([
            'attendances.employee',
            'attendances.team',
            'overtimes.employee',
            'riskAllowances.employee',
            'otherAllowances.employee',
            'periodTeams'
        ])->findOrFail($id);

        $selectedTeamIds = $period->periodTeams->pluck('team_id')->toArray();
        $employees = Employee::where('employment_type', 'PKWT')
            ->where(function ($q) use ($period, $selectedTeamIds) {
                $q->where(function($subQ) use ($selectedTeamIds, $period) {
                    $subQ->where('status', 'Aktif')
                        ->whereIn('team_id', $selectedTeamIds)
                        ->where('created_at', '<=', Carbon::parse($period->end_date)->endOfDay());
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

        $startDate = Carbon::parse($period->start_date);
        $endDate = Carbon::parse($period->end_date);
        $totalPeriodDays = $startDate->diffInDays($endDate) + 1;

        $rows = [];
        foreach ($employees as $employee) {
            $employeeAttendance = $period->attendances->where('employee_id', $employee->id)->first();
            $resolvedTeamId = ($period->status === 'Locked' && $employeeAttendance && $employeeAttendance->team_id)
                ? $employeeAttendance->team_id
                : $employee->team_id;

            $periodTeam = $period->periodTeams->where('team_id', $resolvedTeamId)->first();
            $offDates = $periodTeam ? ($periodTeam->off_dates ?? []) : [];

            $daysWorked = $period->attendances->where('employee_id', $employee->id)
                ->filter(function ($att) use ($offDates) {
                    if ($att->duration <= 0) {
                        return false;
                    }
                    $dateStr = $att->date instanceof Carbon ? $att->date->format('Y-m-d') : $att->date;
                    return !in_array($dateStr, $offDates);
                })
                ->count();

            $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);

            $daysAbsent = max(0, $workDays - $daysWorked);

            $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
            $harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
            $pokok = $daysWorked * $harian;

            $lembur = $period->overtimes->where('employee_id', $employee->id)->sum('amount');
            $risiko = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
            $tunjanganLain = $period->otherAllowances->where('employee_id', $employee->id)->sum('amount');

            $potongan = ($employee->bpjs_health ?? 0) + ($employee->bpjs_tk ?? 0) + ($employee->pph21 ?? 0);
            $total = max(0, $pokok + $lembur + $risiko + $tunjanganLain - $potongan);

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

        $pdf = Pdf::loadView('exports.pkwt-payroll', [
            'period' => $period,
            'rows' => $rows
        ])->setPaper('a4', 'landscape');

        $fileName = 'REKAP_PAYROLL_PKWT_' . str_replace(' ', '_', strtoupper($period->title)) . '.pdf';
        return $pdf->download($fileName);
    }

    public function exportIndividualPdf($id, $employeeId)
    {
        $period = PkwtPayrollPeriod::with([
            'attendances.team',
            'overtimes',
            'riskAllowances',
            'otherAllowances',
            'periodTeams'
        ])->findOrFail($id);

        $employee = Employee::where('employment_type', 'PKWT')->findOrFail($employeeId);

        $startDate = Carbon::parse($period->start_date);
        $endDate = Carbon::parse($period->end_date);
        $totalPeriodDays = $startDate->diffInDays($endDate) + 1;
        
        $employeeAttendance = $period->attendances->where('employee_id', $employee->id)->first();
        $resolvedTeam = ($period->status === 'Locked' && $employeeAttendance && $employeeAttendance->team_id)
            ? $employeeAttendance->team
            : $employee->team;
        $resolvedTeamId = $resolvedTeam ? $resolvedTeam->id : $employee->team_id;
        $team_name = $resolvedTeam ? $resolvedTeam->name : '-';

        $periodTeam = $period->periodTeams->where('team_id', $resolvedTeamId)->first();
        $offDates = $periodTeam ? ($periodTeam->off_dates ?? []) : [];

        $daysWorked = $period->attendances->where('employee_id', $employee->id)
            ->filter(function ($att) use ($offDates) {
                if ($att->duration <= 0) {
                    return false;
                }
                $dateStr = $att->date instanceof Carbon ? $att->date->format('Y-m-d') : $att->date;
                return !in_array($dateStr, $offDates);
            })
            ->count();

        $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);

        $daysAbsent = max(0, $workDays - $daysWorked);

        $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
        $tarif_harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
        $pokok = $daysWorked * $tarif_harian;

        $lembur = $period->overtimes->where('employee_id', $employee->id)->sum('amount');
        $totalOvertimeHours = $period->overtimes->where('employee_id', $employee->id)->sum('hours');
        $risiko = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
        $lain_lain = $period->otherAllowances->where('employee_id', $employee->id)->sum('amount');

        $bpjs_health = (int) ($employee->bpjs_health ?? 0);
        $bpjs_tk = (int) ($employee->bpjs_tk ?? 0);
        $pph21 = (int) ($employee->pph21 ?? 0);
        $potongan = $bpjs_health + $bpjs_tk + $pph21;

        $total = max(0, $pokok + $lembur + $risiko + $lain_lain - $potongan);

        $pdf = Pdf::loadView('exports.pkwt-individual-slip', [
            'period' => $period,
            'employee' => $employee,
            'days_worked' => $daysWorked,
            'days_absent' => $daysAbsent,
            'total_days' => $workDays,
            'salary_monthly' => $employee->salary_monthly,
            'tarif_harian' => $tarif_harian,
            'pokok' => $pokok,
            'lembur' => $lembur,
            'overtime_hours' => $totalOvertimeHours,
            'risiko' => $risiko,
            'lain_lain' => $lain_lain,
            'bpjs_health' => $bpjs_health,
            'bpjs_tk' => $bpjs_tk,
            'pph21' => $pph21,
            'potongan' => $potongan,
            'total' => $total,
            'team_name' => $team_name,
        ])->setPaper('a5', 'landscape');

        $fileName = 'SLIP_GAJI_' . str_replace(' ', '_', strtoupper($employee->name)) . '_' . str_replace(' ', '_', strtoupper($period->title)) . '.pdf';
        return $pdf->stream($fileName);
    }

    public function sendIndividualSlip($id, $employeeId)
    {
        $period = PkwtPayrollPeriod::with(['attendances.team', 'overtimes', 'riskAllowances', 'otherAllowances', 'periodTeams'])->findOrFail($id);
        $employee = Employee::where('employment_type', 'PKWT')->findOrFail($employeeId);

        if (empty($employee->email)) {
            return back()->with('error', 'Karyawan ' . $employee->name . ' tidak memiliki alamat email yang terdaftar. Harap perbarui data karyawan di menu Karyawan.');
        }

        $startDate = Carbon::parse($period->start_date);
        $endDate = Carbon::parse($period->end_date);
        $totalPeriodDays = $startDate->diffInDays($endDate) + 1;

        $employeeAttendance = $period->attendances->where('employee_id', $employee->id)->first();
        $resolvedTeam = ($period->status === 'Locked' && $employeeAttendance && $employeeAttendance->team_id)
            ? $employeeAttendance->team
            : $employee->team;
        $resolvedTeamId = $resolvedTeam ? $resolvedTeam->id : $employee->team_id;
        $team_name = $resolvedTeam ? $resolvedTeam->name : '-';

        $periodTeam = $period->periodTeams->where('team_id', $resolvedTeamId)->first();
        $offDates = $periodTeam ? ($periodTeam->off_dates ?? []) : [];

        $daysWorked = $period->attendances->where('employee_id', $employee->id)
            ->filter(function ($att) use ($offDates) {
                if ($att->duration <= 0) {
                    return false;
                }
                $dateStr = $att->date instanceof Carbon ? $att->date->format('Y-m-d') : $att->date;
                return !in_array($dateStr, $offDates);
            })
            ->count();

        $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);

        $daysAbsent = max(0, $workDays - $daysWorked);

        $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
        $tarif_harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
        $pokok = $daysWorked * $tarif_harian;

        $lembur = $period->overtimes->where('employee_id', $employee->id)->sum('amount');
        $totalOvertimeHours = $period->overtimes->where('employee_id', $employee->id)->sum('hours');
        $risiko = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
        $lain_lain = $period->otherAllowances->where('employee_id', $employee->id)->sum('amount');

        $bpjs_health = (int) ($employee->bpjs_health ?? 0);
        $bpjs_tk = (int) ($employee->bpjs_tk ?? 0);
        $pph21 = (int) ($employee->pph21 ?? 0);
        $potongan = $bpjs_health + $bpjs_tk + $pph21;

        $total = max(0, $pokok + $lembur + $risiko + $lain_lain - $potongan);

        $pdf = Pdf::loadView('exports.pkwt-individual-slip', [
            'period' => $period,
            'employee' => $employee,
            'days_worked' => $daysWorked,
            'days_absent' => $daysAbsent,
            'total_days' => $workDays,
            'salary_monthly' => $employee->salary_monthly,
            'tarif_harian' => $tarif_harian,
            'pokok' => $pokok,
            'lembur' => $lembur,
            'overtime_hours' => $totalOvertimeHours,
            'risiko' => $risiko,
            'lain_lain' => $lain_lain,
            'bpjs_health' => $bpjs_health,
            'bpjs_tk' => $bpjs_tk,
            'pph21' => $pph21,
            'potongan' => $potongan,
            'total' => $total,
            'team_name' => $team_name,
        ])->setPaper('a5', 'landscape');

        $pdfData = $pdf->output();
        $fileName = 'SLIP_GAJI_' . str_replace(' ', '_', strtoupper($employee->name)) . '_' . str_replace(' ', '_', strtoupper($period->title)) . '.pdf';

        try {
            Mail::to($employee->email)->send(new SalarySlipMail($employee, $period->title, $total, $pdfData, $fileName));
            return back()->with('success', 'Slip gaji berhasil dikirim ke email ' . $employee->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    public function sendAllSlips($id)
    {
        $period = PkwtPayrollPeriod::with(['attendances.team', 'overtimes', 'riskAllowances', 'otherAllowances', 'periodTeams'])->findOrFail($id);

        $selectedTeamIds = $period->periodTeams->pluck('team_id')->toArray();
        $employees = Employee::where('employment_type', 'PKWT')
            ->where(function ($q) use ($period, $selectedTeamIds) {
                $q->where(function($subQ) use ($selectedTeamIds, $period) {
                    $subQ->where('status', 'Aktif')
                        ->whereIn('team_id', $selectedTeamIds)
                        ->where('created_at', '<=', Carbon::parse($period->end_date)->endOfDay());
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

        $successCount = 0;
        $failCount = 0;

        $startDate = Carbon::parse($period->start_date);
        $endDate = Carbon::parse($period->end_date);
        $totalPeriodDays = $startDate->diffInDays($endDate) + 1;

        foreach ($employees as $employee) {
            if (empty($employee->email)) {
                $failCount++;
                continue;
            }

            $employeeAttendance = $period->attendances->where('employee_id', $employee->id)->first();
            $resolvedTeam = ($period->status === 'Locked' && $employeeAttendance && $employeeAttendance->team_id)
                ? $employeeAttendance->team
                : $employee->team;
            $resolvedTeamId = $resolvedTeam ? $resolvedTeam->id : $employee->team_id;
            $team_name = $resolvedTeam ? $resolvedTeam->name : '-';

            $periodTeam = $period->periodTeams->where('team_id', $resolvedTeamId)->first();
            $offDates = $periodTeam ? ($periodTeam->off_dates ?? []) : [];

            $daysWorked = $period->attendances->where('employee_id', $employee->id)
                ->filter(function ($att) use ($offDates) {
                    if ($att->duration <= 0) {
                        return false;
                    }
                    $dateStr = $att->date instanceof Carbon ? $att->date->format('Y-m-d') : $att->date;
                    return !in_array($dateStr, $offDates);
                })
                ->count();

            $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);

            $daysAbsent = max(0, $workDays - $daysWorked);

            $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
            $tarif_harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
            $pokok = $daysWorked * $tarif_harian;

            $lembur = $period->overtimes->where('employee_id', $employee->id)->sum('amount');
            $totalOvertimeHours = $period->overtimes->where('employee_id', $employee->id)->sum('hours');
            $risiko = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
            $lain_lain = $period->otherAllowances->where('employee_id', $employee->id)->sum('amount');

            $bpjs_health = (int) ($employee->bpjs_health ?? 0);
            $bpjs_tk = (int) ($employee->bpjs_tk ?? 0);
            $pph21 = (int) ($employee->pph21 ?? 0);
            $potongan = $bpjs_health + $bpjs_tk + $pph21;

            $total = max(0, $pokok + $lembur + $risiko + $lain_lain - $potongan);

            if ($total >= 0) {
                $pdf = Pdf::loadView('exports.pkwt-individual-slip', [
                    'period' => $period,
                    'employee' => $employee,
                    'days_worked' => $daysWorked,
                    'days_absent' => $daysAbsent,
                    'total_days' => $workDays,
                    'salary_monthly' => $employee->salary_monthly,
                    'tarif_harian' => $tarif_harian,
                    'pokok' => $pokok,
                    'lembur' => $lembur,
                    'overtime_hours' => $totalOvertimeHours,
                    'risiko' => $risiko,
                    'lain_lain' => $lain_lain,
                    'bpjs_health' => $bpjs_health,
                    'bpjs_tk' => $bpjs_tk,
                    'pph21' => $pph21,
                    'potongan' => $potongan,
                    'total' => $total,
                    'team_name' => $team_name,
                ])->setPaper('a5', 'landscape');

                $pdfData = $pdf->output();
                $fileName = 'SLIP_GAJI_' . str_replace(' ', '_', strtoupper($employee->name)) . '_' . str_replace(' ', '_', strtoupper($period->title)) . '.pdf';

                try {
                    Mail::to($employee->email)->send(new SalarySlipMail($employee, $period->title, $total, $pdfData, $fileName));
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
