<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penggajian PHL - {{ $period->title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-container {
            margin-bottom: 20px;
            border-bottom: 3px double #1e3a8a;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #475569;
            margin-top: 5px;
        }
        .period-info {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .meta-label {
            color: #64748b;
            width: 120px;
            font-weight: bold;
        }
        .meta-value {
            font-weight: bold;
            color: #334155;
        }
        .payroll-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .payroll-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 8px 6px;
            border: 1px solid #1e3a8a;
            text-align: center;
        }
        .payroll-table td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .payroll-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .currency {
            text-align: right;
            font-family: monospace;
            font-size: 10px;
        }
        .total-row {
            background-color: #f1f5f9 !important;
            font-weight: bold;
        }
        .total-row td {
            border-top: 2px solid #1e3a8a;
            border-bottom: 2px solid #1e3a8a;
            color: #1e3a8a;
        }
        .signature-container {
            margin-top: 50px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            padding-bottom: 70px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #1e3a8a;
        }
        .signature-title {
            color: #64748b;
            font-size: 10px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <div class="company-name">Cimol Bojot AA</div>
        <div class="report-title">LAPORAN REKAPITULASI PENGGAJIAN HARIAN LEPAS (PHL)</div>
        <div class="period-info">Periode: {{ $period->title }} ({{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }})</div>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Status Periode</td>
            <td style="width: 10px;">:</td>
            <td class="meta-value">
                <span style="padding: 2px 8px; background-color: {{ $period->status === 'Locked' ? '#dcfce7' : '#fef9c3' }}; color: {{ $period->status === 'Locked' ? '#166534' : '#854d0e' }}; border-radius: 4px; font-size: 10px;">
                    {{ $period->status === 'Locked' ? 'TERKUNCI / SUDAH DIBAYAR' : 'TERBUKA / DRAFT' }}
                </span>
            </td>
            <td class="meta-label" style="text-align: right; width: 150px;">Tanggal Cetak</td>
            <td style="width: 10px; text-align: center;">:</td>
            <td class="meta-value" style="text-align: right; width: 120px;">{{ date('d-m-Y H:i') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Total Karyawan</td>
            <td>:</td>
            <td class="meta-value">{{ count($rows) }} Orang</td>
            <td class="meta-label" style="text-align: right;">Dicetak Oleh</td>
            <td style="text-align: center;">:</td>
            <td class="meta-value" style="text-align: right;">{{ auth()->user()->name ?? 'Administrator' }}</td>
        </tr>
    </table>

    <table class="payroll-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 10%;">NRP</th>
                <th>Nama Karyawan</th>
                <th style="width: 7%;">Hadir<br>(Hari)</th>
                <th style="width: 10%;">Gaji Pokok / Hari</th>
                <th style="width: 12%;">Total Gaji Pokok</th>
                <th style="width: 7%;">Lembur<br>(Jam)</th>
                <th style="width: 11%;">Nominal Lembur</th>
                <th style="width: 7%;">Risiko<br>(Hari)</th>
                <th style="width: 11%;">Nominal Risiko</th>
                <th style="width: 14%;">Take Home Pay (Total)</th>
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
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ $row['employee']->emp_no }}</td>
                    <td class="font-bold">{{ $row['employee']->name }}</td>
                    <td class="text-center">{{ $row['days_worked'] }}</td>
                    <td class="currency">Rp {{ number_format($row['salary_daily'], 0, ',', '.') }}</td>
                    <td class="currency font-bold">Rp {{ number_format($row['gaji_pokok'], 0, ',', '.') }}</td>
                    <td class="text-center">{{ $row['overtime_hours'] }}</td>
                    <td class="currency">Rp {{ number_format($row['overtime_amount'], 0, ',', '.') }}</td>
                    <td class="text-center">{{ $row['risk_days'] }}</td>
                    <td class="currency">Rp {{ number_format($row['risk_amount'], 0, ',', '.') }}</td>
                    <td class="currency font-bold" style="color: #1e3a8a; background-color: #f8fafc;">Rp {{ number_format($row['take_home_pay'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" class="text-center">TOTAL KESELURUHAN (GRAND TOTAL)</td>
                <td class="text-center">{{ $period->attendances->where('duration', '>', 0)->count() }}</td>
                <td>-</td>
                <td class="currency">Rp {{ number_format($grandPokok, 0, ',', '.') }}</td>
                <td class="text-center">{{ $grandOvertimeHours }}</td>
                <td class="currency">Rp {{ number_format($grandOvertimeAmount, 0, ',', '.') }}</td>
                <td class="text-center">{{ $grandRiskDays }}</td>
                <td class="currency">Rp {{ number_format($grandRiskAmount, 0, ',', '.') }}</td>
                <td class="currency">Rp {{ number_format($grandTakeHomePay, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="signature-container">
        <table class="signature-table">
            <tr>
                <td>
                    <div>Bandung, {{ date('d M Y') }}</div>
                    <div style="margin-top: 5px;">Dibuat Oleh,</div>
                    <div style="margin-top: 60px;" class="signature-name">{{ auth()->user()->name ?? 'Administrator' }}</div>
                    <div class="signature-title">HRD / Payroll Officer</div>
                </td>
                <td>
                    <div style="visibility: hidden;">Spacer Date</div>
                    <div style="margin-top: 5px;">Disetujui Oleh,</div>
                    <div style="margin-top: 60px;" class="signature-name">Pimpinan AA</div>
                    <div class="signature-title">Direktur / Owner</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
