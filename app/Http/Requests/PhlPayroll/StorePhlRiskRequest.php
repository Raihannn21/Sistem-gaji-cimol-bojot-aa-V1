<?php

namespace App\Http\Requests\PhlPayroll;

use Illuminate\Foundation\Http\FormRequest;

class StorePhlRiskRequest extends FormRequest
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
        $mergeData = [];

        if ($this->has('amount')) {
            $mergeData['amount'] = preg_replace('/\D/', '', $this->amount);
        }

        if ($this->has('risk_date') && !empty($this->risk_date)) {
            try {
                $mergeData['risk_date'] = \Carbon\Carbon::createFromFormat('d-m-Y', $this->risk_date)->format('Y-m-d');
            } catch (\Exception $e) {
            }
        }

        if (!empty($mergeData)) {
            $this->merge($mergeData);
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
            'risk_date' => 'required|date',
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
            'risk_date.required' => 'Pilih tanggal tunjangan risiko terlebih dahulu.',
            'risk_date.date' => 'Format tanggal tunjangan risiko tidak valid.',
            'amount.required' => 'Masukkan nominal tunjangan risiko.',
            'amount.numeric' => 'Nominal tunjangan risiko harus berupa angka.',
            'amount.min' => 'Nominal tunjangan risiko tidak boleh bernilai negatif.',
            'note.max' => 'Keterangan tidak boleh melebihi 255 karakter.',
        ];
    }
}
