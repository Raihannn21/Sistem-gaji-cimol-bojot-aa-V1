<?php

namespace App\Http\Controllers\Payroll\Phl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhlPayrollPeriod;
use App\Models\PhlRiskAllowance;
use App\Http\Requests\PhlPayroll\StorePhlRiskRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PhlRiskImport;

class AllowanceController extends Controller
{
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
}
