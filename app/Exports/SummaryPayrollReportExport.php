<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class SummaryPayrollReportExport implements FromView, WithTitle, WithColumnWidths
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.summary-payroll-report-excel', $this->data);
    }

    public function title(): string
    {
        return 'REKAP_TAHUNAN_' . $this->data['selectedYear'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 18,
            'C' => 18,
            'D' => 25,
            'E' => 25,
            'F' => 25,
        ];
    }
}
