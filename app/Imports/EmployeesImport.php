<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Str;

class EmployeesImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    protected $defaultJabatan;

    public function __construct($defaultJabatan = null)
    {
        $this->defaultJabatan = $defaultJabatan;
    }

    public function model(array $row)
    {
        $data = [];
        foreach ($row as $key => $value) {
            $data[Str::slug($key, '_')] = $value;
        }

        $type = $this->defaultJabatan ?: ($data['jabatan'] ?? $data['role'] ?? $data['employment_type'] ?? 'PHL');
        $type = strtoupper($type);
        if (!in_array($type, ['PHL', 'PKWT']))
            $type = 'PHL';

        $status = $data['status'] ?? $data['kondisi'] ?? 'Aktif';
        $status = Str::title($status);
        if (!in_array($status, ['Aktif', 'Resign', 'SPHK']))
            $status = 'Aktif';

        $salaryAmount = $this->cleanNumber($data['salary'] ?? $data['gaji_pokok'] ?? $data['gaji'] ?? $data['gaji_pokok_harian'] ?? $data['gaji_pokok_bulanan'] ?? null);
        $riskAmount = $this->cleanNumber($data['risk_daily_amount'] ?? $data['tunjangan_risiko'] ?? $data['risk_allowance'] ?? null);

        $bpjsHealth = $this->cleanNumber($data['bpjs_health'] ?? $data['bpjs_kesehatan'] ?? null);
        $bpjsTk = $this->cleanNumber($data['bpjs_tk'] ?? $data['bpjs_ketenagakerjaan'] ?? null);
        $pph21 = $this->cleanNumber($data['pph21'] ?? $data['pajak'] ?? $data['pph_21'] ?? null);

        $teamName = $data['team'] ?? $data['tim'] ?? $data['nomor_tim'] ?? null;
        $teamId = null;
        if ($teamName !== null && trim($teamName) !== '') {
            $team = \App\Models\Team::firstOrCreate(['name' => trim($teamName)]);
            $teamId = $team->id;
        }

        $attendanceAllowance = $this->cleanNumber($data['attendance_allowance'] ?? $data['tunjangan_kehadiran'] ?? null);

        return new Employee([
            'name' => $data['nama'] ?? $data['name'] ?? $data['nama_lengkap'] ?? $data['employee_name'],
            'no_id' => $data['no_id'] ?? $data['id_no'] ?? $data['nomor_id'] ?? $data['id_karyawan'],
            'nik' => $data['nik'] ?? $data['nomor_induk_kependudukan'] ?? null,
            'email' => $data['email'] ?? $data['surel'] ?? null,
            'phone' => $data['phone'] ?? $data['telepon'] ?? $data['hp'] ?? $data['no_hp'] ?? null,
            'team_id' => $teamId,
            'location' => $data['location'] ?? $data['lokasi'] ?? $data['penempatan'] ?? null,
            'employment_type' => $type,
            'status' => $status,
            'salary_daily' => ($type === 'PHL') ? $salaryAmount : null,
            'salary_monthly' => ($type === 'PKWT') ? $salaryAmount : null,
            'risk_daily_amount' => $riskAmount,
            'attendance_allowance' => ($type === 'PKWT') ? $attendanceAllowance : null,
            'bpjs_health' => ($type === 'PKWT') ? $bpjsHealth : null,
            'bpjs_tk' => ($type === 'PKWT') ? $bpjsTk : null,
            'pph21' => ($type === 'PKWT') ? $pph21 : null,
            'bank_name' => $data['bank_name'] ?? $data['nama_bank'] ?? $data['bank'] ?? null,
            'bank_account' => $data['bank_account'] ?? $data['nomor_rekening'] ?? $data['no_rek'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.name' => ['required_without_all:*.nama,*.nama_lengkap,*.employee_name'],
            '*.no_id' => ['required_without_all:*.id_no,*.nomor_id,*.id_karyawan'],
        ];
    }

    private function cleanNumber($value)
    {
        if ($value === null || $value === '')
            return null;
        if (is_numeric($value))
            return (float) $value;
        $clean = preg_replace('/[^0-9.]/', '', str_replace(',', '.', $value));
        return $clean !== '' ? (float) $clean : null;
    }
}
