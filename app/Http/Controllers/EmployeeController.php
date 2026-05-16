<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->query('search'));

        $employees = Employee::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('emp_no', 'like', "%{$search}%")
                      ->orWhere('no_id', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get()
            ->map(fn(Employee $employee) => [
                'id' => $employee->id,
                'name' => $employee->name,
                'emp_no' => $employee->emp_no,
                'id_no' => $employee->no_id,
                'nik' => $employee->nik,
                'role' => $employee->employment_type,
                'status' => $employee->status,
                'team' => $employee->team,
                'location' => $employee->location,
                'salary' => $this->formatAmount($employee->salary),
                'risk_allowance' => $this->formatAmount($employee->risk_daily_amount),
                'bpjs_health' => $this->formatAmount($employee->bpjs_health),
                'bpjs_tk' => $this->formatAmount($employee->bpjs_tk),
                'pph21' => $this->formatAmount($employee->pph21),
                'bank_name' => $employee->bank_name,
                'bank_account' => $employee->bank_account,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'completeness_percentage' => $employee->completeness_percentage,
                'completeness_color' => $employee->completeness_color,
            ]);

        $stats = [
            'total' => $employees->count(),
            'aktif' => $employees->where('status', 'Aktif')->count(),
            'resign' => $employees->where('status', 'Resign')->count(),
            'sphk' => $employees->where('status', 'SPHK')->count(),
        ];

        return view('pages.employees.index', [
            'title' => 'Data Karyawan',
            'employees' => $employees,
            'stats' => $stats,
        ]);
    }
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $employmentType = $data['employment_type'] ?? $data['jabatan'] ?? null;

        Employee::create($this->mapEmployeeData($data, $employmentType));

        return redirect()->route('employees.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Karyawan berhasil ditambahkan.',
            ])
            ->with('toast_type', 'success')
            ->with('toast_message', 'Karyawan berhasil ditambahkan.');
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $data = $request->validated();
        $employmentType = $data['employment_type'] ?? $data['jabatan'] ?? $employee->employment_type;

        $employee->update($this->mapEmployeeData($data, $employmentType));

        return redirect()->route('employees.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Data karyawan berhasil diperbarui.',
            ])
            ->with('toast_type', 'success')
            ->with('toast_message', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Karyawan berhasil dihapus.',
            ])
            ->with('toast_type', 'success')
            ->with('toast_message', 'Karyawan berhasil dihapus.');
    }

    private function mapEmployeeData(array $data, ?string $employmentType): array
    {
        $salary = $this->normalizeCurrency($data['salary'] ?? null);
        $riskAllowance = $this->normalizeCurrency($data['risk_allowance'] ?? null);

        return [
            'emp_no' => $data['emp_no'] ?? null,
            'no_id' => $data['no_id'] ?? null,
            'nik' => $data['nik'] ?? null,
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'team' => $data['team'] ?? null,
            'location' => $data['location'] ?? null,
            'employment_type' => $employmentType,
            'status' => $data['status'] ?? 'Aktif',
            'salary_daily' => $employmentType === 'PHL' ? $salary : $this->normalizeDecimal($data['salary_daily'] ?? null),
            'salary_monthly' => $employmentType === 'PKWT' ? $salary : $this->normalizeDecimal($data['salary_monthly'] ?? null),
            'risk_daily_amount' => $riskAllowance,
            'bpjs_health' => $this->normalizeCurrency($data['bpjs_health'] ?? null),
            'bpjs_tk' => $this->normalizeCurrency($data['bpjs_tk'] ?? null),
            'pph21' => $this->normalizeCurrency($data['pph21'] ?? null),
            'bank_name' => $data['bank_name'] ?? null,
            'bank_account' => $data['bank_account'] ?? null,
        ];
    }

    private function normalizeCurrency(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = preg_replace('/\D/', '', $value);

        return $normalized !== '' ? $normalized : null;
    }

    private function normalizeDecimal($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_string($value) ? $value : (string) $value;
    }

    private function formatAmount($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (string) ((int) round((float) $value)) : null;
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file.mimes' => 'Format file harus .xlsx atau .xls.',
        ]);

        try {
            $defaultJabatan = $request->input('jabatan');
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\EmployeesImport($defaultJabatan), $request->file('file'));
            
            return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('employees.index')->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}
