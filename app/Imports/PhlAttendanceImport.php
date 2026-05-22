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
    public $importedCount = 0;
    public $skippedCount = 0;
    public $skippedEmployees = [];

    public function __construct($periodId)
    {
        $this->periodId = $periodId;
    }

    public function model(array $row)
    {
        $noId = $row['no_id'] ?? $row['id_no'] ?? $row['emp_no'] ?? null;
        if (!$noId) {
            $this->skippedCount++;
            return null;
        }

        $employee = Employee::where('no_id', $noId)->first();

        if (!$employee) {
            Log::warning("Employee with ID {$noId} not found during attendance import.");
            $this->skippedCount++;
            $this->skippedEmployees[] = $noId;
            return null;
        }

        try {
            $date = $this->transformDate($row['tanggal'] ?? null);
            if (!$date) {
                $this->skippedCount++;
                return null;
            }

            $scanIn = $this->transformTime($row['scan_masuk'] ?? $row['scan_masuk'] ?? null);
            $scanOut = $this->transformTime($row['scan_pulang'] ?? $row['scan_pulang'] ?? null);
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
                $this->importedCount++;
                return null;
            }

            $this->importedCount++;
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
            $this->skippedCount++;
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
