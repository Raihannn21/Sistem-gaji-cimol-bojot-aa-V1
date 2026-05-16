<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'emp_no' => ['required', 'string', 'max:50', 'unique:employees,emp_no,' . $employeeId],
            'no_id' => ['required', 'string', 'max:50', 'unique:employees,no_id,' . $employeeId],
            'nik' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'team' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:100'],
            'employment_type' => ['nullable', 'in:PHL,PKWT'],
            'jabatan' => ['nullable', 'in:PHL,PKWT'],
            'salary' => ['nullable', 'string'],
            'salary_daily' => ['nullable', 'numeric'],
            'salary_monthly' => ['nullable', 'numeric'],
            'risk_allowance' => ['nullable', 'string'],
            'bpjs_health' => ['nullable', 'string'],
            'bpjs_tk' => ['nullable', 'string'],
            'pph21' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'emp_no.required' => 'Emp No wajib diisi.',
            'emp_no.unique' => 'Emp No sudah terdaftar.',
            'no_id.required' => 'No. ID wajib diisi.',
            'no_id.unique' => 'No. ID sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
        ];
    }
}
