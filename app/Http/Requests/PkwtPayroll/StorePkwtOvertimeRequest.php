<?php

namespace App\Http\Requests\PkwtPayroll;

use Illuminate\Foundation\Http\FormRequest;

class StorePkwtOvertimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $mergeData = [];

        if ($this->has('rate')) {
            $mergeData['rate'] = preg_replace('/\D/', '', $this->rate);
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

        // Set amount automatically
        $hours = (float) ($this->hours ?? 0);
        $rate = (float) ($this->rate ?? 0);
        $this->merge([
            'amount' => $hours * $rate
        ]);
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
            'rate' => 'required|numeric|min:0',
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
            'rate.required' => 'Masukkan nominal per jam.',
            'rate.numeric' => 'Nominal per jam harus berupa angka.',
            'rate.min' => 'Nominal per jam tidak boleh bernilai negatif.',
            'amount.required' => 'Nominal lembur harus dihitung.',
            'amount.numeric' => 'Nominal lembur harus berupa angka.',
            'amount.min' => 'Nominal lembur tidak boleh bernilai negatif.',
            'note.max' => 'Keterangan lembur tidak boleh melebihi 255 karakter.',
        ];
    }
}
