<?php

namespace App\Http\Controllers\Payroll\Pkwt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PkwtPayrollPeriod;
use App\Models\PkwtRiskAllowance;
use App\Models\PkwtOtherAllowance;
use App\Http\Requests\PkwtPayroll\StorePkwtRiskRequest;
use App\Http\Requests\PkwtPayroll\StorePkwtOtherAllowanceRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PkwtRiskImport;

class AllowanceController extends Controller
{
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
}
