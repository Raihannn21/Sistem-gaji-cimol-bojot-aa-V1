<?php

namespace App\Http\Requests\PhlPayroll;

use Illuminate\Foundation\Http\FormRequest;

class StorePhlOvertimeRequest extends FormRequest
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

        if ($this->has('overtime_date') && !empty($this->overtime_date)) {
            try {
                $mergeData['overtime_date'] = \Carbon\Carbon::createFromFormat('d-m-Y', $this->overtime_date)->format('Y-m-d');
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
            'overtime_date' => 'required|date',
            'hours' => 'required|integer|min:1',
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
            'overtime_date.required' => 'Pilih tanggal lembur terlebih dahulu.',
            'overtime_date.date' => 'Format tanggal lembur tidak valid.',
            'hours.required' => 'Masukkan jumlah jam lembur.',
            'hours.integer' => 'Jumlah jam harus berupa angka bulat.',
            'hours.min' => 'Jumlah jam minimal adalah 1 jam.',
            'amount.required' => 'Masukkan nominal uang lembur.',
            'amount.numeric' => 'Nominal lembur harus berupa angka.',
            'amount.min' => 'Nominal lembur tidak boleh bernilai negatif.',
            'note.max' => 'Keterangan lembur tidak boleh melebihi 255 karakter.',
        ];
    }
}
