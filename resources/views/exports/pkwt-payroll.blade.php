<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penggajian PKWT - {{ $period->title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 10px;
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
            font-size: 8px;
            padding: 8px 4px;
            border: 1px solid #1e3a8a;
            text-align: center;
        }
        .payroll-table td {
            padding: 5px;
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
            font-size: 9px;
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
            margin-top: 40px;
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
            padding-bottom: 60px;
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
        <div class="report-title">LAPORAN REKAPITULASI PENGGAJIAN KARYAWAN KONTRAK (PKWT)</div>
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
            <td class="meta-value" style="text-align: right; width: 120px;">{{ \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i') }}</td>
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
                <th style="width: 3%;">No</th>
                <th style="width: 8%;">NRP</th>
                <th>Nama Karyawan</th>
                <th style="width: 6%;">Hadir<br>(Hari)</th>
                <th style="width: 6%;">Absen<br>(Hari)</th>
                <th style="width: 10%;">Tarif Harian</th>
                <th style="width: 12%;">Gaji Pokok Didapat</th>
                <th style="width: 9%;">Lembur</th>
                <th style="width: 9%;">Risiko</th>
                <th style="width: 9%;">Lain-lain</th>
                <th style="width: 9%;">Potongan</th>
                <th style="width: 12%;">Total Bersih</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandWorked = 0;
                $grandAbsent = 0;
                $grandPokok = 0;
                $grandLembur = 0;
                $grandRisiko = 0;
                $grandLain = 0;
                $grandPotongan = 0;
                $grandTotal = 0;
                $no = 1;
            @endphp
            @foreach($rows as $row)
                @php
                    $grandWorked += $row['days_worked'];
                    $grandAbsent += $row['days_absent'];
                    $grandPokok += $row['gaji_pokok_didapat'];
                    $grandLembur += $row['lembur'];
                    $grandRisiko += $row['risiko'];
                    $grandLain += $row['lain_lain'];
                    $grandPotongan += $row['potongan'];
                    $grandTotal += $row['total_bersih'];
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ $row['employee']->emp_no }}</td>
                    <td class="font-bold">{{ $row['employee']->name }}</td>
                    <td class="text-center">{{ $row['days_worked'] }} H</td>
                    <td class="text-center">{{ $row['days_absent'] }} H</td>
                    <td class="currency">Rp {{ number_format($row['tarif_harian'], 0, ',', '.') }}</td>
                    <td class="currency font-bold">Rp {{ number_format($row['gaji_pokok_didapat'], 0, ',', '.') }}</td>
                    <td class="currency">Rp {{ number_format($row['lembur'], 0, ',', '.') }}</td>
                    <td class="currency">Rp {{ number_format($row['risiko'], 0, ',', '.') }}</td>
                    <td class="currency">Rp {{ number_format($row['lain_lain'], 0, ',', '.') }}</td>
                    <td class="currency" style="color: #dc2626;">Rp {{ number_format($row['potongan'], 0, ',', '.') }}</td>
                    <td class="currency font-bold" style="color: #1e3a8a; background-color: #f8fafc;">Rp {{ number_format($row['total_bersih'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" class="text-center">TOTAL KESELURUHAN (GRAND TOTAL)</td>
                <td class="text-center">{{ $grandWorked }} H</td>
                <td class="text-center">{{ $grandAbsent }} H</td>
                <td>-</td>
                <td class="currency">Rp {{ number_format($grandPokok, 0, ',', '.') }}</td>
                <td class="currency">Rp {{ number_format($grandLembur, 0, ',', '.') }}</td>
                <td class="currency">Rp {{ number_format($grandRisiko, 0, ',', '.') }}</td>
                <td class="currency">Rp {{ number_format($grandLain, 0, ',', '.') }}</td>
                <td class="currency" style="color: #dc2626;">Rp {{ number_format($grandPotongan, 0, ',', '.') }}</td>
                <td class="currency">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="signature-container">
        <table class="signature-table">
            <tr>
                <td>
                    <div>Bandung, {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d M Y') }}</div>
                    <div style="margin-top: 5px;">Dibuat Oleh,</div>
                    <div style="margin-top: 50px;" class="signature-name">{{ auth()->user()->name ?? 'Administrator' }}</div>
                    <div class="signature-title">HRD / Payroll Officer</div>
                </td>
                <td>
                    <div style="visibility: hidden;">Spacer Date</div>
                    <div style="margin-top: 5px;">Disetujui Oleh,</div>
                    <div style="margin-top: 50px;" class="signature-name">Pimpinan AA</div>
                    <div class="signature-title">Direktur / Owner</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
