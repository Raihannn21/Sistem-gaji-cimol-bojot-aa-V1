<?php

namespace App\Http\Requests\PkwtPayroll;

use Illuminate\Foundation\Http\FormRequest;

class StorePkwtOtherAllowanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $this->merge([
                'amount' => preg_replace('/\D/', '', $this->amount),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'allowance_type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ];
    }

    /**
     * Custom messages for validation errors in Indonesian.
     */
    public function messages(): array
    {
        return [
            'employee_id.required' => 'Pilih karyawan terlebih dahulu.',
            'employee_id.exists' => 'Karyawan yang dipilih tidak terdaftar di sistem.',
            'allowance_type.required' => 'Masukkan jenis tunjangan.',
            'allowance_type.max' => 'Jenis tunjangan tidak boleh melebihi 100 karakter.',
            'amount.required' => 'Masukkan nominal tunjangan.',
            'amount.numeric' => 'Nominal tunjangan harus berupa angka.',
            'amount.min' => 'Nominal tunjangan tidak boleh bernilai negatif.',
            'note.max' => 'Catatan tidak boleh melebihi 255 karakter.',
        ];
    }
}
