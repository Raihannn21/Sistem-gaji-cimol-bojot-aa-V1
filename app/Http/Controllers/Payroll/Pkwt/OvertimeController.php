<?php

namespace App\Http\Controllers\Payroll\Pkwt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PkwtPayrollPeriod;
use App\Models\PkwtOvertime;
use App\Http\Requests\PkwtPayroll\StorePkwtOvertimeRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PkwtOvertimeImport;

class OvertimeController extends Controller
{
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
            return redirect()->route('payroll.pkwt.periods.show', [$id, 'tab' => 'overtime'])->with('error', 'Periode payroll ini sudah dikunci and tidak dapat diubah lagi.');
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
}
