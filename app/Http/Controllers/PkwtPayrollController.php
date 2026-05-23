<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PkwtPayrollPeriod;
use App\Models\PkwtAttendance;
use App\Models\PkwtOvertime;
use App\Models\PkwtRiskAllowance;
use App\Models\PkwtOtherAllowance;
use App\Models\Team;
use App\Models\PkwtPayrollPeriodTeam;
use App\Http\Requests\PkwtPayroll\StorePkwtOvertimeRequest;
use App\Http\Requests\PkwtPayroll\StorePkwtRiskRequest;
use App\Http\Requests\PkwtPayroll\StorePkwtOtherAllowanceRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PkwtAttendanceImport;
use App\Imports\PkwtOvertimeImport;
use App\Imports\PkwtRiskImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SalarySlipMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PkwtPayrollController extends Controller
{
    public function index()
    {
        $periods = PkwtPayrollPeriod::with(['attendances.employee', 'overtimes', 'riskAllowances', 'otherAllowances', 'periodTeams'])
            ->orderBy('start_date', 'desc')
            ->get();

        $pkwtEmployeeCount = Employee::where('employment_type', 'PKWT')
            ->where('status', 'Aktif')
            ->count();

        $currentYear = date('Y');
        $ytdPaid = 0;

        foreach ($periods as $period) {
            $startDate = Carbon::parse($period->start_date);
            $endDate = Carbon::parse($period->end_date);
            $totalPeriodDays = $startDate->diffInDays($endDate) + 1;

            $periodEmployeeIds = $period->attendances->pluck('employee_id')->unique();
            $employeesInPeriodCount = count($periodEmployeeIds);

            $periodTotal = 0;
            $groupedAttendances = $period->attendances->groupBy('employee_id');

            foreach ($groupedAttendances as $empId => $empAttendances) {
                $employee = $empAttendances->first()->employee;
                if (!$employee)
                    continue;

                $daysWorked = $empAttendances->count();
                
                $periodTeam = $period->periodTeams->where('team_id', $employee->team_id)->first();
                $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);
                $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
                $harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
                $pokok = $daysWorked * $harian;

                $lembur = $period->overtimes->where('employee_id', $empId)->sum('amount');
                $risiko = $period->riskAllowances->where('employee_id', $empId)->sum('amount');
                $other = $period->otherAllowances->where('employee_id', $empId)->sum('amount');

                $bpjsHealth = $employee->bpjs_health ?? 0;
                $bpjsTk = $employee->bpjs_tk ?? 0;
                $pph21 = $employee->pph21 ?? 0;
                $potongan = $bpjsHealth + $bpjsTk + $pph21;

                $netPay = max(0, $pokok + $lembur + $risiko + $other - $potongan);
                $periodTotal += $netPay;
            }

            $period->total_expenditure = $periodTotal;
            $period->total_employees = ($employeesInPeriodCount === 0 && $period->status === 'Open')
                ? $pkwtEmployeeCount
                : $employeesInPeriodCount;

            if ($period->status === 'Locked' && $period->start_date->format('Y') == $currentYear) {
                $ytdPaid += $periodTotal;
            }
        }

        return view('pages.payroll.pkwt.periods', [
            'title' => 'Periode Gaji PKWT',
            'periods' => $periods,
            'pkwtEmployeeCount' => $pkwtEmployeeCount,
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

        $period = PkwtPayrollPeriod::create([
            'title' => $request->title,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'Open',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('payroll.pkwt.periods.setup', $period->id)
            ->with('success', 'Periode gaji PKWT baru berhasil dibuka. Silakan tentukan tim dan hari libur.');
    }

    public function setup($id)
    {
        $period = PkwtPayrollPeriod::with('periodTeams')->findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', $id)
                ->with('error', 'Periode ini sudah dikunci dan tidak dapat diatur lagi.');
        }

        $teams = Team::withCount(['employees' => function($query) {
            $query->where('employment_type', 'PKWT')->where('status', 'Aktif');
        }])->orderBy('name', 'asc')->get();
        
        // Generate list of all dates from start_date to end_date
        $dates = [];
        $current = Carbon::parse($period->start_date);
        $end = Carbon::parse($period->end_date);
        while ($current->lte($end)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        return view('pages.payroll.pkwt.setup', [
            'title' => 'Setup Tim & Hari Libur - PKWT',
            'period' => $period,
            'teams' => $teams,
            'dates' => $dates,
        ]);
    }

    public function saveSetup(Request $request, $id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', $id)
                ->with('error', 'Periode ini sudah dikunci.');
        }

        $request->validate([
            'teams' => 'required|array',
            'teams.*' => 'exists:teams,id',
            'off_dates' => 'nullable|array',
        ]);

        \DB::transaction(function () use ($period, $request) {
            // Delete old setup
            $period->periodTeams()->delete();

            $totalPeriodDays = Carbon::parse($period->start_date)->diffInDays(Carbon::parse($period->end_date)) + 1;

            foreach ($request->teams as $teamId) {
                $teamOffDates = $request->input("off_dates.{$teamId}", []);
                if (is_string($teamOffDates)) {
                    $teamOffDates = json_decode($teamOffDates, true) ?: [];
                }
                // Filter empty strings/nulls
                $teamOffDates = array_values(array_filter($teamOffDates));
                
                $workDays = $totalPeriodDays - count($teamOffDates);

                PkwtPayrollPeriodTeam::create([
                    'pkwt_payroll_period_id' => $period->id,
                    'team_id' => $teamId,
                    'off_dates' => $teamOffDates,
                    'work_days' => max(0, $workDays),
                ]);
            }
        });

        return redirect()->route('payroll.pkwt.periods.show', $period->id)
            ->with('success', 'Pengaturan tim dan hari libur berhasil disimpan.');
    }

    public function show($id)
    {
        $period = PkwtPayrollPeriod::with([
            'attendances.employee',
            'overtimes.employee',
            'riskAllowances.employee',
            'otherAllowances.employee',
            'periodTeams.team'
        ])->findOrFail($id);

        $period->setRelation('attendances', $period->attendances->sortBy([
            ['employee.name', 'asc'],
            ['date', 'asc']
        ]));

        if ($period->status === 'Locked') {
            $employeeIds = collect()
                ->merge($period->attendances->pluck('employee_id'))
                ->merge($period->overtimes->pluck('employee_id'))
                ->merge($period->riskAllowances->pluck('employee_id'))
                ->merge($period->otherAllowances->pluck('employee_id'))
                ->unique();

            $employees = Employee::whereIn('id', $employeeIds)->get();
        } else {
            $selectedTeamIds = $period->periodTeams->pluck('team_id')->toArray();

            $employees = Employee::where('employment_type', 'PKWT')
                ->where('status', 'Aktif')
                ->whereIn('team_id', $selectedTeamIds)
                ->get();
        }

        return view('pages.payroll.pkwt.period-detail', [
            'title' => 'Detail Periode Gaji PKWT',
            'period' => $period,
            'employees' => $employees,
        ]);
    }

    public function destroy($id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        $period->delete();

        return redirect()->route('payroll.pkwt.periods')
            ->with('success', 'Periode gaji PKWT berhasil dihapus.');
    }

    public function importAttendance(Request $request, $id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'attendance'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
        ]);

        try {
            $array = Excel::toArray(new PkwtAttendanceImport($id), $request->file('file'));
            if (empty($array) || empty($array[0])) {
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'attendance'])->with('error', 'File terbaca kosong.');
            }

            $import = new PkwtAttendanceImport($id);
            Excel::import($import, $request->file('file'));

            $imported = $import->importedCount;
            $skipped = $import->skippedCount;
            $skippedList = array_unique($import->skippedEmployees);

            if ($imported === 0 && $skipped > 0) {
                $msg = 'Peringatan: Tidak ada data absensi PKWT yang diimpor. Semua baris (' . $skipped . ' data) dilewati karena nomor ID karyawan berikut tidak terdaftar di sistem: (' . implode(', ', $skippedList) . ').';
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'attendance'])->with('error', $msg);
            } elseif ($skipped > 0) {
                $msg = 'Berhasil mengimpor ' . $imported . ' data absensi PKWT. Sebanyak ' . $skipped . ' baris data dilewati karena nomor ID karyawan berikut tidak terdaftar: (' . implode(', ', $skippedList) . ').';
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'attendance'])->with('warning', $msg);
            }

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'attendance'])->with('success', 'Data absensi PKWT berhasil diimport (' . $imported . ' data).');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'attendance'])->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function updateAttendance(Request $request, $id, $attendanceId)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'attendance'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        $request->validate([
            'scan_in' => 'nullable|string',
            'scan_out' => 'nullable|string',
            'late_time' => 'nullable|string',
            'early_time' => 'nullable|string',
        ]);

        try {
            $attendance = PkwtAttendance::where('pkwt_payroll_period_id', $id)->findOrFail($attendanceId);

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

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'attendance'])->with('success', 'Data absensi berhasil diubah.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'attendance'])->with('error', 'Terjadi kesalahan saat mengubah data absensi: ' . $e->getMessage());
        }
    }

    public function destroyAttendance($id, $attendanceId)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'attendance'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $attendance = PkwtAttendance::where('pkwt_payroll_period_id', $id)->findOrFail($attendanceId);
            $attendance->delete();

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'attendance'])->with('success', 'Data absensi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'attendance'])->with('error', 'Gagal menghapus data absensi: ' . $e->getMessage());
        }
    }

    public function storeOvertime(StorePkwtOvertimeRequest $request, $id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $existing = PkwtOvertime::where('pkwt_payroll_period_id', $period->id)
                ->where('employee_id', $request->employee_id)
                ->where('date', $request->overtime_date)
                ->first();

            if ($existing) {
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Karyawan tersebut sudah memiliki data lembur terdaftar pada tanggal tersebut.');
            }

            PkwtOvertime::create([
                'pkwt_payroll_period_id' => $period->id,
                'employee_id' => $request->employee_id,
                'date' => $request->overtime_date,
                'hours' => $request->hours,
                'amount' => $request->amount,
                'note' => $request->note,
            ]);

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('success', 'Data lembur berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateOvertime(StorePkwtOvertimeRequest $request, $id, $overtimeId)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $overtime = PkwtOvertime::where('pkwt_payroll_period_id', $id)->findOrFail($overtimeId);
            $existing = PkwtOvertime::where('pkwt_payroll_period_id', $id)
                ->where('employee_id', $request->employee_id)
                ->where('date', $request->overtime_date)
                ->where('id', '!=', $overtimeId)
                ->first();

            if ($existing) {
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Karyawan tersebut sudah memiliki data lembur terdaftar pada tanggal tersebut.');
            }

            $overtime->update([
                'employee_id' => $request->employee_id,
                'date' => $request->overtime_date,
                'hours' => $request->hours,
                'amount' => $request->amount,
                'note' => $request->note,
            ]);

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('success', 'Data lembur berhasil diubah.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Terjadi kesalahan saat mengubah data lembur: ' . $e->getMessage());
        }
    }

    public function destroyOvertime($id, $overtimeId)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $overtime = PkwtOvertime::where('pkwt_payroll_period_id', $id)->findOrFail($overtimeId);
            $overtime->delete();

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('success', 'Data lembur berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Gagal menghapus data lembur: ' . $e->getMessage());
        }
    }

    public function importOvertime(Request $request, $id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
        ]);

        try {
            $array = Excel::toArray(new PkwtOvertimeImport($id), $request->file('file'));
            if (empty($array) || empty($array[0])) {
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'File terbaca kosong.');
            }

            $import = new PkwtOvertimeImport($id);
            Excel::import($import, $request->file('file'));

            $imported = $import->importedCount;
            $skipped = $import->skippedCount;
            $skippedList = array_unique($import->skippedEmployees);

            if ($imported === 0 && $skipped > 0) {
                $msg = 'Peringatan: Tidak ada data lembur PKWT yang diimpor. Semua baris (' . $skipped . ' data) dilewati karena nomor ID karyawan tidak terdaftar, bukan PKWT, atau tanggal di luar periode: (' . implode(', ', $skippedList) . ').';
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('error', $msg);
            } elseif ($skipped > 0) {
                $msg = 'Berhasil mengimpor ' . $imported . ' data lembur PKWT. Sebanyak ' . $skipped . ' baris data dilewati karena nomor ID karyawan tidak terdaftar, bukan PKWT, atau tanggal di luar periode: (' . implode(', ', $skippedList) . ').';
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('warning', $msg);
            }

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('success', 'Data lembur PKWT berhasil diimport (' . $imported . ' data).');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function importRisk(Request $request, $id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
        ]);

        try {
            $array = Excel::toArray(new PkwtRiskImport($id), $request->file('file'));
            if (empty($array) || empty($array[0])) {
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('error', 'File terbaca kosong.');
            }

            $import = new PkwtRiskImport($id);
            Excel::import($import, $request->file('file'));

            $imported = $import->importedCount;
            $skipped = $import->skippedCount;
            $skippedList = array_unique($import->skippedEmployees);

            if ($imported === 0 && $skipped > 0) {
                $msg = 'Peringatan: Tidak ada data tunjangan risiko PKWT yang diimpor. Semua baris (' . $skipped . ' data) dilewati karena nomor ID karyawan tidak terdaftar, bukan PKWT, atau tanggal di luar periode: (' . implode(', ', $skippedList) . ').';
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('error', $msg);
            } elseif ($skipped > 0) {
                $msg = 'Berhasil mengimpor ' . $imported . ' data tunjangan risiko PKWT. Sebanyak ' . $skipped . ' baris data dilewati karena nomor ID karyawan tidak terdaftar, bukan PKWT, atau tanggal di luar periode: (' . implode(', ', $skippedList) . ').';
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('warning', $msg);
            }

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('success', 'Data tunjangan risiko PKWT berhasil diimport (' . $imported . ' data).');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function storeRisk(StorePkwtRiskRequest $request, $id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $existing = PkwtRiskAllowance::where('pkwt_payroll_period_id', $period->id)
                ->where('employee_id', $request->employee_id)
                ->where('date', $request->risk_date)
                ->first();

            if ($existing) {
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Karyawan tersebut sudah memiliki tunjangan risiko pada tanggal tersebut.');
            }

            PkwtRiskAllowance::create([
                'pkwt_payroll_period_id' => $period->id,
                'employee_id' => $request->employee_id,
                'date' => $request->risk_date,
                'amount' => $request->amount,
                'note' => $request->note,
            ]);

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('success', 'Tunjangan risiko berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateRisk(StorePkwtRiskRequest $request, $id, $riskId)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $risk = PkwtRiskAllowance::where('pkwt_payroll_period_id', $id)->findOrFail($riskId);
            $existing = PkwtRiskAllowance::where('pkwt_payroll_period_id', $id)
                ->where('employee_id', $request->employee_id)
                ->where('date', $request->risk_date)
                ->where('id', '!=', $riskId)
                ->first();

            if ($existing) {
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Karyawan tersebut sudah memiliki tunjangan risiko pada tanggal tersebut.');
            }

            $risk->update([
                'employee_id' => $request->employee_id,
                'date' => $request->risk_date,
                'amount' => $request->amount,
                'note' => $request->note,
            ]);

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('success', 'Tunjangan risiko berhasil diubah.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Terjadi kesalahan saat mengubah tunjangan risiko: ' . $e->getMessage());
        }
    }

    public function destroyRisk($id, $riskId)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $risk = PkwtRiskAllowance::where('pkwt_payroll_period_id', $id)->findOrFail($riskId);
            $risk->delete();

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('success', 'Tunjangan risiko berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'risk'])->with('error', 'Gagal menghapus tunjangan risiko: ' . $e->getMessage());
        }
    }

    public function storeOtherAllowance(StorePkwtOtherAllowanceRequest $request, $id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'others'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            PkwtOtherAllowance::create([
                'pkwt_payroll_period_id' => $period->id,
                'employee_id' => $request->employee_id,
                'allowance_type' => $request->allowance_type,
                'amount' => $request->amount,
                'note' => $request->note,
            ]);

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'others'])->with('success', 'Tunjangan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'others'])->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroyOtherAllowance($id, $allowanceId)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        if ($period->status === 'Locked') {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'others'])->with('error', 'Periode payroll ini sudah dikunci dan tidak dapat diubah lagi.');
        }

        try {
            $allowance = PkwtOtherAllowance::where('pkwt_payroll_period_id', $id)->findOrFail($allowanceId);
            $allowance->delete();

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'others'])->with('success', 'Tunjangan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'others'])->with('error', 'Gagal menghapus tunjangan: ' . $e->getMessage());
        }
    }


    public function generate($id)
    {
        try {
            $period = PkwtPayrollPeriod::findOrFail($id);
            if ($period->status === 'Locked') {
                return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'slips'])->with('error', 'Periode payroll ini sudah dikunci.');
            }
            $period->update([
                'status' => 'Locked'
            ]);

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'slips'])
                ->with('success', 'Payroll PKWT berhasil digenerate dan slip gaji telah diterbitkan!');
        } catch (\Exception $e) {
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overview'])
                ->with('error', 'Terjadi kesalahan saat memproses payroll PKWT: ' . $e->getMessage());
        }
    }

    public function exportExcel($id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        $fileName = 'REKAP_PAYROLL_PKWT_' . str_replace(' ', '_', strtoupper($period->title)) . '.xlsx';

        return Excel::download(new \App\Exports\PkwtPayrollExport($period), $fileName);
    }

    public function exportBca($id)
    {
        $period = PkwtPayrollPeriod::findOrFail($id);
        $fileName = 'BCA_TRANSFER_LIST_PKWT_' . str_replace(' ', '_', strtoupper($period->title)) . '.xlsx';

        return Excel::download(new \App\Exports\PkwtBcaPayrollExport($period), $fileName);
    }

    public function exportPdf($id)
    {
        $period = PkwtPayrollPeriod::with([
            'attendances.employee',
            'overtimes.employee',
            'riskAllowances.employee',
            'otherAllowances.employee',
            'periodTeams'
        ])->findOrFail($id);

        $selectedTeamIds = $period->periodTeams->pluck('team_id')->toArray();
        $employees = Employee::where('employment_type', 'PKWT')
            ->where(function ($q) use ($period, $selectedTeamIds) {
                $q->where(function($subQ) use ($selectedTeamIds) {
                    $subQ->where('status', 'Aktif')
                        ->whereIn('team_id', $selectedTeamIds);
                })
                ->orWhereHas('pkwtAttendances', function ($sub) use ($period) {
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
            $daysWorked = $period->attendances->where('employee_id', $employee->id)->count();

            $periodTeam = $period->periodTeams->where('team_id', $employee->team_id)->first();
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
            'attendances',
            'overtimes',
            'riskAllowances',
            'otherAllowances',
            'periodTeams'
        ])->findOrFail($id);

        $employee = Employee::where('employment_type', 'PKWT')->findOrFail($employeeId);

        $startDate = Carbon::parse($period->start_date);
        $endDate = Carbon::parse($period->end_date);
        $totalPeriodDays = $startDate->diffInDays($endDate) + 1;

        $daysWorked = $period->attendances->where('employee_id', $employee->id)->count();
        
        $periodTeam = $period->periodTeams->where('team_id', $employee->team_id)->first();
        $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);

        $daysAbsent = max(0, $workDays - $daysWorked);

        $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
        $tarif_harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
        $pokok = $daysWorked * $tarif_harian;

        $lembur = $period->overtimes->where('employee_id', $employee->id)->sum('amount');
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
            'risiko' => $risiko,
            'lain_lain' => $lain_lain,
            'bpjs_health' => $bpjs_health,
            'bpjs_tk' => $bpjs_tk,
            'pph21' => $pph21,
            'potongan' => $potongan,
            'total' => $total,
        ])->setPaper('a5', 'portrait');

        $fileName = 'SLIP_GAJI_' . str_replace(' ', '_', strtoupper($employee->name)) . '_' . str_replace(' ', '_', strtoupper($period->title)) . '.pdf';
        return $pdf->stream($fileName);
    }
    public function sendIndividualSlip($id, $employeeId)
    {
        $period = PkwtPayrollPeriod::with(['attendances', 'overtimes', 'riskAllowances', 'otherAllowances', 'periodTeams'])->findOrFail($id);
        $employee = Employee::where('employment_type', 'PKWT')->findOrFail($employeeId);

        if (empty($employee->email)) {
            return back()->with('error', 'Karyawan ' . $employee->name . ' tidak memiliki alamat email yang terdaftar. Harap perbarui data karyawan di menu Karyawan.');
        }

        $startDate = Carbon::parse($period->start_date);
        $endDate = Carbon::parse($period->end_date);
        $totalPeriodDays = $startDate->diffInDays($endDate) + 1;

        $daysWorked = $period->attendances->where('employee_id', $employee->id)->count();

        $periodTeam = $period->periodTeams->where('team_id', $employee->team_id)->first();
        $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);

        $daysAbsent = max(0, $workDays - $daysWorked);

        $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
        $tarif_harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
        $pokok = $daysWorked * $tarif_harian;

        $lembur = $period->overtimes->where('employee_id', $employee->id)->sum('amount');
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
            'risiko' => $risiko,
            'lain_lain' => $lain_lain,
            'bpjs_health' => $bpjs_health,
            'bpjs_tk' => $bpjs_tk,
            'pph21' => $pph21,
            'potongan' => $potongan,
            'total' => $total,
        ])->setPaper('a5', 'portrait');

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
        $period = PkwtPayrollPeriod::with(['attendances', 'overtimes', 'riskAllowances', 'otherAllowances', 'periodTeams'])->findOrFail($id);

        $selectedTeamIds = $period->periodTeams->pluck('team_id')->toArray();
        $employees = Employee::where('employment_type', 'PKWT')
            ->where(function ($q) use ($period, $selectedTeamIds) {
                $q->where(function($subQ) use ($selectedTeamIds) {
                    $subQ->where('status', 'Aktif')
                        ->whereIn('team_id', $selectedTeamIds);
                })
                ->orWhereHas('pkwtAttendances', function ($sub) use ($period) {
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

            $daysWorked = $period->attendances->where('employee_id', $employee->id)->count();

            $periodTeam = $period->periodTeams->where('team_id', $employee->team_id)->first();
            $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);

            $daysAbsent = max(0, $workDays - $daysWorked);

            $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
            $tarif_harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
            $pokok = $daysWorked * $tarif_harian;

            $lembur = $period->overtimes->where('employee_id', $employee->id)->sum('amount');
            $risiko = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
            $lain_lain = $period->otherAllowances->where('employee_id', $employee->id)->sum('amount');

            $bpjs_health = (int) ($employee->bpjs_health ?? 0);
            $bpjs_tk = (int) ($employee->bpjs_tk ?? 0);
            $pph21 = (int) ($employee->pph21 ?? 0);
            $potongan = $bpjs_health + $bpjs_tk + $pph21;

            $total = max(0, $pokok + $lembur + $risiko + $lain_lain - $potongan);

            if ($daysWorked > 0 || $lembur > 0 || $risiko > 0 || $lain_lain > 0) {
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
                    'risiko' => $risiko,
                    'lain_lain' => $lain_lain,
                    'bpjs_health' => $bpjs_health,
                    'bpjs_tk' => $bpjs_tk,
                    'pph21' => $pph21,
                    'potongan' => $potongan,
                    'total' => $total,
                ])->setPaper('a5', 'portrait');

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

