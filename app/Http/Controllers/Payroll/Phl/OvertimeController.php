<?php

namespace App\Http\Controllers\Payroll\Phl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhlPayrollPeriod;
use App\Models\PhlOvertime;
use App\Http\Requests\PhlPayroll\StorePhlOvertimeRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PhlOvertimeImport;

class OvertimeController extends Controller
{
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
}
