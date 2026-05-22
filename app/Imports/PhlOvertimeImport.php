<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\PhlOvertime;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PhlOvertimeImport implements ToModel, WithHeadingRow
{
    protected $periodId;
    public $importedCount = 0;
    public $skippedCount = 0;
    public $skippedEmployees = [];

    protected $period;

    public function __construct($periodId)
    {
        $this->periodId = $periodId;
        $this->period = \App\Models\PhlPayrollPeriod::find($periodId);
    }

    public function model(array $row)
    {
        // Normalize keys
        $data = [];
        foreach ($row as $key => $value) {
            $data[Str::slug($key, '_')] = $value;
        }

        $noId = $data['no_id'] ?? $data['id_no'] ?? $data['nomor_id'] ?? $data['id_karyawan'] ?? null;
        if (!$noId) {
            $this->skippedCount++;
            return null;
        }

        $employee = Employee::where('no_id', $noId)
            ->where('employment_type', 'PHL')
            ->first();

        if (!$employee) {
            Log::warning("Employee with ID {$noId} not found or not PHL during overtime import.");
            $this->skippedCount++;
            $this->skippedEmployees[] = $noId;
            return null;
        }

        try {
            $dateVal = $data['tanggal_dd_mm_yyyy'] ?? $data['tanggal'] ?? $data['date'] ?? null;
            $date = $this->transformDate($dateVal);
            if (!$date) {
                $this->skippedCount++;
                return null;
            }

            if ($this->period) {
                $startDate = Carbon::parse($this->period->start_date)->format('Y-m-d');
                $endDate = Carbon::parse($this->period->end_date)->format('Y-m-d');
                if ($date < $startDate || $date > $endDate) {
                    Log::warning("Date {$date} is out of period range [{$startDate} - {$endDate}] for employee ID {$noId}.");
                    $this->skippedCount++;
                    $this->skippedEmployees[] = $noId . " (Tanggal luar periode)";
                    return null;
                }
            }

            $hours = $this->cleanNumber($data['jumlah_jam'] ?? $data['hours'] ?? 0);
            $rate = $this->cleanNumber($data['nominal_per_jam'] ?? $data['rate'] ?? $data['nominal'] ?? 0);
            $amount = $hours * $rate;

            if ($hours <= 0 || $amount <= 0) {
                $this->skippedCount++;
                return null;
            }

            $note = $data['keterangan'] ?? $data['note'] ?? $data['detail'] ?? null;

            $existing = PhlOvertime::where('phl_payroll_period_id', $this->periodId)
                ->where('employee_id', $employee->id)
                ->where('date', $date)
                ->first();

            if ($existing) {
                $existing->update([
                    'hours' => $hours,
                    'amount' => $amount,
                    'note' => $note,
                ]);
                $this->importedCount++;
                return null;
            }

            $this->importedCount++;
            return new PhlOvertime([
                'phl_payroll_period_id' => $this->periodId,
                'employee_id' => $employee->id,
                'date' => $date,
                'hours' => $hours,
                'amount' => $amount,
                'note' => $note,
            ]);

        } catch (\Exception $e) {
            Log::error("Error importing PHL overtime row: " . $e->getMessage());
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
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', trim($value))) {
                return Carbon::createFromFormat('d-m-Y', trim($value))->format('Y-m-d');
            }
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', trim($value))) {
                return Carbon::createFromFormat('d/m/Y', trim($value))->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error("Date parsing error in PHL overtime import for value '{$value}': " . $e->getMessage());
            return null;
        }
    }

    private function cleanNumber($value)
    {
        if ($value === null || $value === '')
            return 0;
        if (is_numeric($value))
            return (float) $value;
        $clean = preg_replace('/[^0-9.]/', '', str_replace(',', '.', $value));
        return $clean !== '' ? (float) $clean : 0;
    }
}
