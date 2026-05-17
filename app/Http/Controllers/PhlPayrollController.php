<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PhlPayrollPeriod;
use App\Models\Employee;
use App\Models\PhlOvertime;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PhlAttendanceImport;

class PhlPayrollController extends Controller
{
    public function index()
    {
        $periods = PhlPayrollPeriod::orderBy('start_date', 'desc')->get();
        $phlEmployeeCount = Employee::where('employment_type', 'PHL')
            ->where('status', 'Aktif')
            ->count();

        return view('pages.payroll.phl.periods', [
            'title' => 'Periode Gaji PHL',
            'periods' => $periods,
            'phlEmployeeCount' => $phlEmployeeCount
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date_range' => 'required|string',
        ]);

        $dates = explode(' to ', $request->date_range);

        $startDate = trim($dates[0]);
        $endDate = isset($dates[1]) ? trim($dates[1]) : $startDate;

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
        $period = PhlPayrollPeriod::with(['attendances.employee', 'overtimes.employee'])->findOrFail($id);
        $employees = Employee::where('employment_type', 'PHL')
            ->where('status', 'Aktif')
            ->get();

        return view('pages.payroll.phl.period-detail', [
            'title' => 'Detail Periode Gaji PHL',
            'period' => $period,
            'employees' => $employees
        ]);
    }

    public function importAttendance(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file.mimes' => 'Format file harus .xlsx atau .xls.',
        ]);

        try {
            $array = Excel::toArray(new PhlAttendanceImport($id), $request->file('file'));
            if (empty($array) || empty($array[0])) {
                return redirect()->back()->with('error', 'File Excel terbaca kosong. Jika ini file dari mesin absen (berformat .xls), silakan buka file tersebut di aplikasi Excel lalu pilih "Save As" ke format .xlsx (Excel Workbook) dan coba import kembali file yang baru.');
            }

            Excel::import(new PhlAttendanceImport($id), $request->file('file'));

            return redirect()->back()->with('success', 'Data absensi berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function storeOvertime(Request $request, $id)
    {
        $period = PhlPayrollPeriod::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'overtime_date' => 'required|date',
            'hours' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        try {
            $existing = PhlOvertime::where('phl_payroll_period_id', $period->id)
                ->where('employee_id', $request->employee_id)
                ->where('date', $request->overtime_date)
                ->first();

            if ($existing) {
                return redirect()->back()->with('error', 'Karyawan tersebut sudah memiliki data lembur terdaftar pada tanggal tersebut.');
            }

            PhlOvertime::create([
                'phl_payroll_period_id' => $period->id,
                'employee_id' => $request->employee_id,
                'date' => $request->overtime_date,
                'hours' => $request->hours,
                'amount' => $request->amount,
                'note' => $request->note,
            ]);

            return redirect()->back()->with('success', 'Data lembur berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroyOvertime($id, $overtimeId)
    {
        try {
            $overtime = PhlOvertime::where('phl_payroll_period_id', $id)->findOrFail($overtimeId);
            $overtime->delete();

            return redirect()->back()->with('success', 'Data lembur berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data lembur: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $period = PhlPayrollPeriod::findOrFail($id);
        $period->delete();

        return redirect()->route('payroll.phl.periods')->with('success', 'Periode gaji berhasil dihapus.');
    }
}
