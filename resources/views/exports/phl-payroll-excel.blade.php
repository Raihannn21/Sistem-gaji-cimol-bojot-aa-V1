<table>
    <thead>
        <!-- Title Block -->
        <tr>
            <th colspan="11" style="font-size: 16px; font-weight: bold; text-align: left;">CIMOL BOJOT AA</th>
        </tr>
        <tr>
            <th colspan="11" style="font-size: 14px; font-weight: bold; text-align: left; color: #1e3a8a;">LAPORAN REKAPITULASI PENGGAJIAN HARIAN LEPAS (PHL)</th>
        </tr>
        <tr>
            <th colspan="11" style="font-size: 11px; text-align: left; color: #475569;">Periode: {{ $period->title }} ({{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }})</th>
        </tr>
        <tr>
            <th colspan="11"></th>
        </tr>
        <!-- Metadata Block -->
        <tr>
            <th style="font-weight: bold; text-align: left;">Status Periode</th>
            <th colspan="2" style="text-align: left;">: {{ $period->status === 'Locked' ? 'TERKUNCI / SUDAH DIBAYAR' : 'TERBUKA / DRAFT' }}</th>
            <th colspan="4"></th>
            <th style="font-weight: bold; text-align: right;">Tanggal Cetak</th>
            <th colspan="3" style="text-align: left;">: {{ date('d-m-Y H:i') }}</th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: left;">Total Karyawan</th>
            <th colspan="2" style="text-align: left;">: {{ count($rows) }} Orang</th>
            <th colspan="4"></th>
            <th style="font-weight: bold; text-align: right;">Dicetak Oleh</th>
            <th colspan="3" style="text-align: left;">: {{ auth()->user()->name ?? 'Administrator' }}</th>
        </tr>
        <tr>
            <th colspan="11"></th>
        </tr>
        <!-- Table Headers -->
        <tr>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">No</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">NRP</th>
            <th style="font-weight: bold; text-align: left; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Nama Karyawan</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Hadir (Hari)</th>
            <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Gaji Pokok / Hari</th>
            <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Total Gaji Pokok</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Lembur (Jam)</th>
            <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Nominal Lembur</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Risiko (Hari)</th>
            <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Nominal Risiko</th>
            <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Take Home Pay (Total)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $grandPokok = 0;
            $grandOvertimeHours = 0;
            $grandOvertimeAmount = 0;
            $grandRiskDays = 0;
            $grandRiskAmount = 0;
            $grandTakeHomePay = 0;
            $no = 1;
        @endphp
        @foreach($rows as $row)
            @php
                $grandPokok += $row['gaji_pokok'];
                $grandOvertimeHours += $row['overtime_hours'];
                $grandOvertimeAmount += $row['overtime_amount'];
                $grandRiskDays += $row['risk_days'];
                $grandRiskAmount += $row['risk_amount'];
                $grandTakeHomePay += $row['take_home_pay'];
            @endphp
            <tr>
                <td style="text-align: center; border: 1px solid #000000;">{{ $no++ }}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['employee']->emp_no }}</td>
                <td style="font-weight: bold; border: 1px solid #000000;">{{ $row['employee']->name }}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['days_worked'] }}</td>
                <td style="text-align: right; border: 1px solid #000000;">Rp {{ number_format($row['salary_daily'], 0, ',', '.') }}</td>
                <td style="text-align: right; font-weight: bold; border: 1px solid #000000;">Rp {{ number_format($row['gaji_pokok'], 0, ',', '.') }}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['overtime_hours'] }}</td>
                <td style="text-align: right; border: 1px solid #000000;">Rp {{ number_format($row['overtime_amount'], 0, ',', '.') }}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['risk_days'] }}</td>
                <td style="text-align: right; border: 1px solid #000000;">Rp {{ number_format($row['risk_amount'], 0, ',', '.') }}</td>
                <td style="text-align: right; font-weight: bold; border: 1px solid #000000; background-color: #f8fafc; color: #1e3a8a;">Rp {{ number_format($row['take_home_pay'], 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <!-- Grand Total Row -->
        <tr style="background-color: #f1f5f9; font-weight: bold;">
            <td colspan="3" style="text-align: center; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9;">TOTAL KESELURUHAN (GRAND TOTAL)</td>
            <td style="text-align: center; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9;">{{ $period->attendances->where('duration', '>', 0)->count() }}</td>
            <td style="border: 1px solid #000000; background-color: #f1f5f9; text-align: center;">-</td>
            <td style="text-align: right; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9;">Rp {{ number_format($grandPokok, 0, ',', '.') }}</td>
            <td style="text-align: center; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9;">{{ $grandOvertimeHours }}</td>
            <td style="text-align: right; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9;">Rp {{ number_format($grandOvertimeAmount, 0, ',', '.') }}</td>
            <td style="text-align: center; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9;">{{ $grandRiskDays }}</td>
            <td style="text-align: right; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9;">Rp {{ number_format($grandRiskAmount, 0, ',', '.') }}</td>
            <td style="text-align: right; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9; color: #1e3a8a;">Rp {{ number_format($grandTakeHomePay, 0, ',', '.') }}</td>
        </tr>
        <!-- Empty Spacer Rows -->
        <tr>
            <td colspan="11"></td>
        </tr>
        <tr>
            <td colspan="11"></td>
        </tr>
        <!-- Signature Block -->
        <tr>
            <td colspan="3" style="text-align: center; font-weight: bold;">Bandung, {{ date('d M Y') }}</td>
            <td colspan="5"></td>
            <td colspan="3" style="text-align: center; font-weight: bold;">Disetujui Oleh,</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center;">Dibuat Oleh,</td>
            <td colspan="5"></td>
            <td colspan="3" style="text-align: center;">Pimpinan AA</td>
        </tr>
        <tr style="height: 40px;">
            <td colspan="11" style="height: 40px;"></td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ auth()->user()->name ?? 'Administrator' }}</td>
            <td colspan="5"></td>
            <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">Owner Cimol Bojot AA</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; color: #64748b; font-size: 10px;">HRD / Payroll Officer</td>
            <td colspan="5"></td>
            <td colspan="3" style="text-align: center; color: #64748b; font-size: 10px;">Direktur / Owner</td>
        </tr>
    </tbody>
</table>
