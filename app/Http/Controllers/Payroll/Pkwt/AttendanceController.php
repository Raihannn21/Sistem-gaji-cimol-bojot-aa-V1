<?php

namespace App\Http\Controllers\Payroll\Pkwt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PkwtPayrollPeriod;
use App\Models\PkwtAttendance;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PkwtAttendanceImport;
use Carbon\Carbon;

class AttendanceController extends Controller
{
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
}
