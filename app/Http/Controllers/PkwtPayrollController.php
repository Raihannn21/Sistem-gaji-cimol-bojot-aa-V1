<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PkwtPayrollPeriod;
use App\Models\PkwtAttendance;
use App\Models\PkwtOvertime;
use App\Models\PkwtRiskAllowance;
use App\Models\PkwtOtherAllowance;
use App\Http\Requests\PkwtPayroll\StorePkwtOvertimeRequest;
use App\Http\Requests\PkwtPayroll\StorePkwtRiskRequest;
use App\Http\Requests\PkwtPayroll\StorePkwtOtherAllowanceRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PkwtAttendanceImport;
use Illuminate\Http\Request;

class PkwtPayrollController extends Controller
{
    public function index()
    {
        $periods = PkwtPayrollPeriod::with(['overtimes', 'riskAllowances', 'otherAllowances'])
            ->orderBy('start_date', 'desc')
            ->get();
            
        $pkwtEmployeeCount = Employee::where('employment_type', 'PKWT')
            ->where('status', 'Aktif')
            ->count();

        $currentYear = date('Y');
        $ytdPaid = 0;
        
        foreach ($periods as $period) {
            $totalPokok = Employee::where('employment_type', 'PKWT')->where('status', 'Aktif')->sum('salary_monthly');
            $totalOvertime = $period->overtimes->sum('amount');
            $totalRisk = $period->riskAllowances->sum('amount');
            $totalOthers = $period->otherAllowances->sum('amount');
            
            $totalDeductions = Employee::where('employment_type', 'PKWT')
                ->where('status', 'Aktif')
                ->get()
                ->sum(function ($emp) {
                    return ($emp->bpjs_health ?? 0) + ($emp->bpjs_tk ?? 0) + ($emp->pph21 ?? 0);
                });
                
            $periodTotal = max(0, $totalPokok + $totalOvertime + $totalRisk + $totalOthers - $totalDeductions);
            
            $period->total_expenditure = $periodTotal;
            $period->total_employees = $pkwtEmployeeCount;
            
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

        $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
        $endDate = isset($dates[1]) 
            ? \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->format('Y-m-d') 
            : $startDate;

        $period = PkwtPayrollPeriod::create([
            'title' => $request->title,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'Open',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('payroll.pkwt.periods.show', $period->id)
            ->with('success', 'Periode gaji PKWT baru berhasil dibuka.');
    }

    public function show($id)
    {
        $period = PkwtPayrollPeriod::with([
            'attendances.employee', 
            'overtimes.employee', 
            'riskAllowances.employee',
            'otherAllowances.employee'
        ])->findOrFail($id);
        
        // Sort attendances so they don't jump around when updated
        $period->setRelation('attendances', $period->attendances->sortBy([
            ['employee.name', 'asc'],
            ['date', 'asc']
        ]));
        
        $employees = Employee::where('employment_type', 'PKWT')
            ->where('status', 'Aktif')
            ->get();

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

            Excel::import(new PkwtAttendanceImport($id), $request->file('file'));

            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'attendance'])->with('success', 'Data absensi PKWT berhasil diimport.');
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
        ]);

        try {
            $attendance = PkwtAttendance::where('pkwt_payroll_period_id', $id)->findOrFail($attendanceId);

            $scanIn = $request->scan_in ? \Carbon\Carbon::parse($request->scan_in)->format('H:i:s') : null;
            $scanOut = $request->scan_out ? \Carbon\Carbon::parse($request->scan_out)->format('H:i:s') : null;

            $duration = 0;
            if ($scanIn && $scanOut) {
                $start = \Carbon\Carbon::parse($scanIn);
                $end = \Carbon\Carbon::parse($scanOut);

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
            'otherAllowances.employee'
        ])->findOrFail($id);

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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pkwt-payroll', [
            'period' => $period,
            'rows' => $rows
        ])->setPaper('a4', 'landscape');

        $fileName = 'REKAP_PAYROLL_PKWT_' . str_replace(' ', '_', strtoupper($period->title)) . '.pdf';
        return $pdf->download($fileName);
    }
}
