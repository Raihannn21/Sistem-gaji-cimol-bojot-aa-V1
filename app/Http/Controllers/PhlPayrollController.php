<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PhlPayrollPeriod;
use App\Models\Employee;
use App\Models\PhlOvertime;
use App\Models\PhlRiskAllowance;
use App\Models\PhlAttendance;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PhlAttendanceImport;
use App\Imports\PhlOvertimeImport;
use App\Imports\PhlRiskImport;
use App\Http\Requests\PhlPayroll\StorePhlOvertimeRequest;
use App\Http\Requests\PhlPayroll\StorePhlRiskRequest;
use App\Exports\PhlPayrollExport;
use App\Exports\BcaPayrollExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\SalarySlipMail;
use Carbon\Carbon;

class PhlPayrollController extends Controller
{
    public function index()
    {
        $periods = PhlPayrollPeriod::with(['attendances.employee', 'attendances.team', 'overtimes', 'riskAllowances'])
            ->orderBy('start_date', 'desc')
            ->get();

        $phlEmployeeCount = Employee::where('employment_type', 'PHL')
            ->where('status', 'Aktif')
            ->count();

        $currentYear = date('Y');
        $ytdPaid = 0;

        foreach ($periods as $period) {
            $totalPokok = $period->attendances->sum(function ($attendance) {
                return $attendance->duration > 0 ? ($attendance->employee->salary_daily ?? 0) : 0;
            });
            $totalOvertime = $period->overtimes->sum('amount');
            $totalRisk = $period->riskAllowances->sum('amount');
            $periodTotal = $totalPokok + $totalOvertime + $totalRisk;

            $period->total_expenditure = $periodTotal;

            if ($period->status === 'Locked' && $period->start_date->format('Y') == $currentYear) {
                $ytdPaid += $periodTotal;
            }
        }

        return view('pages.payroll.phl.periods', [
            'title' => 'Periode Gaji PHL',
            'periods' => $periods,
            'phlEmployeeCount' => $phlEmployeeCount,
            'ytdPaid' => $ytdPaid
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date_range' => 'required|string',
        ]);

        $dates = explode(' to ', $request->date_range);

        $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
        $endDate = isset($dates[1])
            ? Carbon::createFromFormat('d-m-Y', trim($dates[1]))->format('Y-m-d')
            : $startDate;

        $period = PhlPayrollPeriod::create([
            'title' => $request->title,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'Open',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('payroll.phl.periods.show', $period->id)
            ->with('success', 'Periode gaji baru berhasil dibuka.');
    }

    public function show($id)
    {
        $period = PhlPayrollPeriod::with(['attendances.employee', 'attendances.team', 'overtimes.employee', 'riskAllowances.employee'])->findOrFail($id);

        $period->setRelation('attendances', $period->attendances->sortBy([
            ['employee.name', 'asc'],
            ['date', 'asc']
        ]));

        if ($period->status === 'Locked') {
            $employeeIds = collect()
                ->merge($period->attendances->pluck('employee_id'))
                ->merge($period->overtimes->pluck('employee_id'))
                ->merge($period->riskAllowances->pluck('employee_id'))
                ->unique();

            $employees = Employee::whereIn('id', $employeeIds)->get();
        } else {
            $employees = Employee::where('employment_type', 'PHL')
                ->where('status', 'Aktif')
                ->get();
        }

        return view('pages.payroll.phl.period-detail', [
            'title' => 'Detail Periode Gaji PHL',
            'period' => $period,
            'employees' => $employees
        ]);
    }

    public function importAttendance(Request $request, $id)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'attendance'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file.mimes' => 'Format file harus .xlsx atau .xls.',
        ]);

        try {
            $array = Excel::toArray(new PhlAttendanceImport($id), $request->file('file'));
            if (empty($array) || empty($array[0])) {
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'attendance'])->with('error', 'File Excel terbaca kosong. Jika ini file dari mesin absen (berformat .xls), silakan buka file tersebut di aplikasi Excel lalu pilih "Save As" ke format .xlsx (Excel Workbook) dan coba import kembali file yang baru.');
            }

            $import = new PhlAttendanceImport($id);
            Excel::import($import, $request->file('file'));

            $imported = $import->importedCount;
            $skipped = $import->skippedCount;
            $skippedList = array_unique($import->skippedEmployees);

            if ($imported === 0 && $skipped > 0) {
                $msg = 'Peringatan: Tidak ada data absensi yang diimpor. Semua baris (' . $skipped . ' data) dilewati karena nomor ID karyawan berikut tidak terdaftar di sistem: (' . implode(', ', $skippedList) . ').';
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'attendance'])->with('error', $msg);
            } elseif ($skipped > 0) {
                $msg = 'Berhasil mengimpor ' . $imported . ' data absensi. Sebanyak ' . $skipped . ' baris data dilewati karena nomor ID karyawan berikut tidak terdaftar: (' . implode(', ', $skippedList) . ').';
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'attendance'])->with('warning', $msg);
            }

            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'attendance'])->with('success', 'Data absensi berhasil diimport (' . $imported . ' data).');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'attendance'])->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function storeOvertime(StorePhlOvertimeRequest $request, $id)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $existing = PhlOvertime::where('phl_payroll_period_id', $period->id)
                ->where('employee_id', $request->employee_id)
                ->where('date', $request->overtime_date)
                ->first();

            if ($existing) {
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Karyawan tersebut sudah memiliki data lembur terdaftar pada tanggal tersebut.');
            }

            PhlOvertime::create([
                'phl_payroll_period_id' => $period->id,
                'employee_id' => $request->employee_id,
                'date' => $request->overtime_date,
                'hours' => $request->hours,
                'amount' => $request->amount,
                'note' => $request->note,
            ]);

            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('success', 'Data lembur berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateOvertime(StorePhlOvertimeRequest $request, $id, $overtimeId)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $overtime = PhlOvertime::where('phl_payroll_period_id', $id)->findOrFail($overtimeId);
            $existing = PhlOvertime::where('phl_payroll_period_id', $id)
                ->where('employee_id', $request->employee_id)
                ->where('date', $request->overtime_date)
                ->where('id', '!=', $overtimeId)
                ->first();

            if ($existing) {
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Karyawan tersebut sudah memiliki data lembur terdaftar pada tanggal tersebut.');
            }

            $overtime->update([
                'employee_id' => $request->employee_id,
                'date' => $request->overtime_date,
                'hours' => $request->hours,
                'amount' => $request->amount,
                'note' => $request->note,
            ]);

            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('success', 'Data lembur berhasil diubah.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Terjadi kesalahan saat mengubah data lembur: ' . $e->getMessage());
        }
    }

    public function destroyOvertime($id, $overtimeId)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $overtime = PhlOvertime::where('phl_payroll_period_id', $id)->findOrFail($overtimeId);
            $overtime->delete();

            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('success', 'Data lembur berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Gagal menghapus data lembur: ' . $e->getMessage());
        }
    }

    public function importOvertime(Request $request, $id)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
        ]);

        try {
            $array = Excel::toArray(new PhlOvertimeImport($id), $request->file('file'));
            if (empty($array) || empty($array[0])) {
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'File terbaca kosong.');
            }

            $import = new PhlOvertimeImport($id);
            Excel::import($import, $request->file('file'));

            $imported = $import->importedCount;
            $skipped = $import->skippedCount;
            $skippedList = array_unique($import->skippedEmployees);

            if ($imported === 0 && $skipped > 0) {
                $msg = 'Peringatan: Tidak ada data lembur PHL yang diimpor. Semua baris (' . $skipped . ' data) dilewati karena nomor ID karyawan tidak terdaftar, bukan PHL, atau tanggal di luar periode: (' . implode(', ', $skippedList) . ').';
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('error', $msg);
            } elseif ($skipped > 0) {
                $msg = 'Berhasil mengimpor ' . $imported . ' data lembur PHL. Sebanyak ' . $skipped . ' baris data dilewati karena nomor ID karyawan tidak terdaftar, bukan PHL, atau tanggal di luar periode: (' . implode(', ', $skippedList) . ').';
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('warning', $msg);
            }

            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('success', 'Data lembur PHL berhasil diimport (' . $imported . ' data).');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function importRisk(Request $request, $id)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
        ]);

        try {
            $array = Excel::toArray(new PhlRiskImport($id), $request->file('file'));
            if (empty($array) || empty($array[0])) {
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('error', 'File terbaca kosong.');
            }

            $import = new PhlRiskImport($id);
            Excel::import($import, $request->file('file'));

            $imported = $import->importedCount;
            $skipped = $import->skippedCount;
            $skippedList = array_unique($import->skippedEmployees);

            if ($imported === 0 && $skipped > 0) {
                $msg = 'Peringatan: Tidak ada data tunjangan risiko PHL yang diimpor. Semua baris (' . $skipped . ' data) dilewati karena nomor ID karyawan tidak terdaftar, bukan PHL, atau tanggal di luar periode: (' . implode(', ', $skippedList) . ').';
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('error', $msg);
            } elseif ($skipped > 0) {
                $msg = 'Berhasil mengimpor ' . $imported . ' data tunjangan risiko PHL. Sebanyak ' . $skipped . ' baris data dilewati karena nomor ID karyawan tidak terdaftar, bukan PHL, atau tanggal di luar periode: (' . implode(', ', $skippedList) . ').';
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('warning', $msg);
            }

            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('success', 'Data tunjangan risiko PHL berhasil diimport (' . $imported . ' data).');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function storeRisk(StorePhlRiskRequest $request, $id)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $existing = PhlRiskAllowance::where('phl_payroll_period_id', $period->id)
                ->where('employee_id', $request->employee_id)
                ->where('date', $request->risk_date)
                ->first();

            if ($existing) {
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Karyawan tersebut sudah memiliki data tunjangan risiko terdaftar pada tanggal tersebut.');
            }

            PhlRiskAllowance::create([
                'phl_payroll_period_id' => $period->id,
                'employee_id' => $request->employee_id,
                'date' => $request->risk_date,
                'amount' => $request->amount,
                'note' => $request->note,
            ]);

            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('success', 'Data tunjangan risiko berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateRisk(StorePhlRiskRequest $request, $id, $riskId)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $risk = PhlRiskAllowance::where('phl_payroll_period_id', $id)->findOrFail($riskId);
            $existing = PhlRiskAllowance::where('phl_payroll_period_id', $id)
                ->where('employee_id', $request->employee_id)
                ->where('date', $request->risk_date)
                ->where('id', '!=', $riskId)
                ->first();

            if ($existing) {
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Karyawan tersebut sudah memiliki data tunjangan risiko terdaftar pada tanggal tersebut.');
            }

            $risk->update([
                'employee_id' => $request->employee_id,
                'date' => $request->risk_date,
                'amount' => $request->amount,
                'note' => $request->note,
            ]);

            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('success', 'Data tunjangan risiko berhasil diubah.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Terjadi kesalahan saat mengubah data tunjangan risiko: ' . $e->getMessage());
        }
    }

    public function destroyRisk($id, $riskId)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $risk = PhlRiskAllowance::where('phl_payroll_period_id', $id)->findOrFail($riskId);
            $risk->delete();

            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('success', 'Data tunjangan risiko berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Gagal menghapus data tunjangan risiko: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        $period->delete();

        return redirect()->route('payroll.phl.periods')->with('success', 'Periode gaji berhasil dihapus.');
    }

    public function generate($id)
    {
        try {
            $period = PhlPayrollPeriod::findOrFail($id);
            if ($period->status === 'Locked') {
                return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'slips'])->with('error', 'Periode payroll ini sudah dikunci.');
            }
            
            \DB::transaction(function () use ($period) {
                // Freeze team for all attendances in this period
                \DB::statement('
                    UPDATE phl_attendances 
                    SET team_id = (SELECT team_id FROM employees WHERE employees.id = phl_attendances.employee_id)
                    WHERE phl_payroll_period_id = ?
                ', [$period->id]);

                $period->update([
                    'status' => 'Locked'
                ]);
            });

            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'slips'])
                ->with('success', 'Payroll berhasil digenerate dan slip gaji telah diterbitkan!');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'overview'])
                ->with('error', 'Terjadi kesalahan saat memproses payroll: ' . $e->getMessage());
        }
    }

    public function unlock($id)
    {
        try {
            $period = PhlPayrollPeriod::findOrFail($id);
            if ($period->status !== 'Locked') {
                return redirect()->route('payroll.phl.periods.show', $id)
                    ->with('error', 'Periode payroll ini tidak dalam status terkunci.');
            }

            $period->update([
                'status' => 'Open'
            ]);

            return redirect()->route('payroll.phl.periods.show', $id)
                ->with('success', 'Periode payroll berhasil dibuka kunci. Anda sekarang dapat melakukan perubahan data.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', $id)
                ->with('error', 'Terjadi kesalahan saat membuka kunci payroll: ' . $e->getMessage());
        }
    }

    public function exportExcel($id)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        $fileName = 'REKAP_PAYROLL_PHL_' . str_replace(' ', '_', strtoupper($period->title)) . '.xlsx';

        return Excel::download(new PhlPayrollExport($period), $fileName);
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
                $q->where('status', 'Aktif')
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

            if ($daysWorked > 0 || $totalOvertimeHours > 0 || $totalRiskAmount > 0) {
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
        }

        $pdf = Pdf::loadView('exports.phl-payroll', [
            'period' => $period,
            'rows' => $rows
        ])->setPaper('a4', 'landscape');

        $fileName = 'REKAP_PAYROLL_PHL_' . str_replace(' ', '_', strtoupper($period->title)) . '.pdf';
        return $pdf->download($fileName);
    }

    public function exportBca($id)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        $fileName = 'BCA_TRANSFER_LIST_PHL_' . str_replace(' ', '_', strtoupper($period->title)) . '.xlsx';

        return Excel::download(new BcaPayrollExport($period), $fileName);
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

    public function updateAttendance(Request $request, $id, $attendanceId)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'attendance'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        $request->validate([
            'scan_in' => 'nullable|string',
            'scan_out' => 'nullable|string',
            'late_time' => 'nullable|string',
            'early_time' => 'nullable|string',
        ]);

        try {
            $attendance = PhlAttendance::where('phl_payroll_period_id', $id)->findOrFail($attendanceId);

            $scanIn = $request->scan_in ? Carbon::parse($request->scan_in)->format('H:i:s') : null;
            $scanOut = $request->scan_out ? Carbon::parse($request->scan_out)->format('H:i:s') : null;

            $duration = 0;
            if ($scanIn && $scanOut) {
                $start = Carbon::parse($scanIn);
                $end = Carbon::parse($scanOut);

                if ($end->gt($start)) {
                    $diffInMinutes = abs($end->diffInMinutes($start));
                    $hours = round($diffInMinutes / 60, 2);
                    $duration = (int) round(min($hours, 8));
                }
            } elseif ($scanIn || $scanOut) {
                $duration = 8;
            }

            $attendance->update([
                'scan_in' => $scanIn,
                'scan_out' => $scanOut,
                'late_time' => $request->late_time,
                'early_time' => $request->early_time,
                'duration' => $duration,
            ]);

            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'attendance'])->with('success', 'Data absensi berhasil diubah.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'attendance'])->with('error', 'Terjadi kesalahan saat mengubah data absensi: ' . $e->getMessage());
        }
    }

    public function destroyAttendance($id, $attendanceId)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'attendance'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $attendance = PhlAttendance::where('phl_payroll_period_id', $id)->findOrFail($attendanceId);
            $attendance->delete();

            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'attendance'])->with('success', 'Data absensi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.phl.periods.show', [$id, 'tab' => 'attendance'])->with('error', 'Gagal menghapus data absensi: ' . $e->getMessage());
        }
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
                $q->where('status', 'Aktif')
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

            if ($daysWorked > 0 || $totalOvertimeHours > 0 || $totalRiskAmount > 0) {
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
