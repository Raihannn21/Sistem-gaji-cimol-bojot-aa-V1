<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\PhlAttendance;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class PhlAttendanceImport implements ToModel, WithHeadingRow
{
    protected $periodId;

    public function __construct($periodId)
    {
        $this->periodId = $periodId;
    }

    public function model(array $row)
    {
        Log::info('Raw Row: ', $row);
        $empNo = $row['emp_no'] ?? $row['no_id'] ?? null;
        Log::info('EmpNo resolved as: ' . $empNo);
        if (!$empNo)
            return null;

        $employee = Employee::where('emp_no', $empNo)
            ->orWhere('no_id', $empNo)
            ->first();

        if (!$employee) {
            Log::warning("Employee with ID {$empNo} not found during attendance import.");
            return null;
        }

        try {
            $date = $this->transformDate($row['tanggal'] ?? null);
            if (!$date)
                return null;

            $scanIn = $this->transformTime($row['scan_masuk'] ?? $row['scan_masuk'] ?? null);
            $scanOut = $this->transformTime($row['scan_pulang'] ?? $row['scan_pulang'] ?? null);
            $duration = 0;
            if ($scanIn && $scanOut) {
                $start = Carbon::parse($scanIn);
                $end = Carbon::parse($scanOut);

                if ($end->gt($start)) {
                    $diffInMinutes = $end->diffInMinutes($start);
                    $hours = round($diffInMinutes / 60, 2);
                    $duration = min($hours, 8);
                }
            }

            $existing = PhlAttendance::where('phl_payroll_period_id', $this->periodId)
                ->where('employee_id', $employee->id)
                ->where('date', $date)
                ->first();

            if ($existing) {
                $existing->update([
                    'scan_in' => $scanIn,
                    'scan_out' => $scanOut,
                    'duration' => $duration,
                ]);
                return null;
            }

            return new PhlAttendance([
                'phl_payroll_period_id' => $this->periodId,
                'employee_id' => $employee->id,
                'date' => $date,
                'scan_in' => $scanIn,
                'scan_out' => $scanOut,
                'duration' => $duration,
            ]);

        } catch (\Exception $e) {
            Log::error("Error importing attendance row: " . $e->getMessage());
            return null;
        }
    }

    private function transformDate($value)
    {
        if (!$value)
            return null;

        try {
            if (is_numeric($value)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
            }
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', trim($value))) {
                return Carbon::createFromFormat('d/m/Y', trim($value))->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error("Date parsing error in attendance import for value '{$value}': " . $e->getMessage());
            return null;
        }
    }

    private function transformTime($value)
    {
        if (!$value)
            return null;

        try {
            if (is_numeric($value)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('H:i:s');
            }
            return Carbon::parse($value)->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}
