<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'no_id' => ['required', 'string', 'max:50', 'unique:employees,no_id'],
            'nik' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'location' => ['nullable', 'string', 'max:100'],
            'employment_type' => ['nullable', 'in:PHL,PKWT'],
            'jabatan' => ['nullable', 'in:PHL,PKWT'],
            'salary' => ['nullable'],
            'salary_daily' => ['nullable', 'numeric'],
            'salary_monthly' => ['nullable', 'numeric'],
            'risk_allowance' => ['nullable'],
            'attendance_allowance' => ['nullable', 'numeric'],
            'bpjs_health' => ['nullable'],
            'bpjs_tk' => ['nullable'],
            'pph21' => ['nullable'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'no_id.required' => 'No. ID wajib diisi.',
            'no_id.unique' => 'No. ID sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
        ];
    }
}
