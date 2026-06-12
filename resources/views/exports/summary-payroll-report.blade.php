<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Gaji Tahunan - {{ $selectedYear }}</title>
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
        <div class="report-title">LAPORAN REKAPITULASI GAJI TAHUNAN (SUMMARY REPORT)</div>
        <div class="period-info">Tahun Laporan: {{ $selectedYear }}</div>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Tahun Laporan</td>
            <td style="width: 10px;">:</td>
            <td class="meta-value">{{ $selectedYear }}</td>
            <td class="meta-label" style="text-align: right; width: 150px;">Tanggal Cetak</td>
            <td style="width: 10px; text-align: center;">:</td>
            <td class="meta-value" style="text-align: right; width: 120px;">{{ \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Total Gaji Setahun</td>
            <td>:</td>
            <td class="meta-value">Rp {{ number_format($totalAnnualPayroll, 0, ',', '.') }}</td>
            <td class="meta-label" style="text-align: right;">Dicetak Oleh</td>
            <td style="text-align: center;">:</td>
            <td class="meta-value" style="text-align: right;">{{ auth()->user()->name ?? 'Administrator' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Total Biaya PKWT</td>
            <td>:</td>
            <td class="meta-value">Rp {{ number_format($totalAnnualPkwtCost, 0, ',', '.') }} ({{ number_format($pkwtShare, 1, ',', '.') }}%)</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td class="meta-label">Total Biaya PHL</td>
            <td>:</td>
            <td class="meta-value">Rp {{ number_format($totalAnnualPhlCost, 0, ',', '.') }} ({{ number_format($phlShare, 1, ',', '.') }}%)</td>
            <td colspan="3"></td>
        </tr>
    </table>

    <table class="payroll-table">
        <thead>
            <tr>
                <th style="text-align: left; width: 15%;">Bulan</th>
                <th class="text-center" style="width: 15%;">Jumlah PKWT</th>
                <th class="text-center" style="width: 15%;">Jumlah PHL</th>
                <th class="text-right" style="width: 18%;">Biaya PKWT</th>
                <th class="text-right" style="width: 18%;">Biaya PHL</th>
                <th class="text-right" style="width: 19%;">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($summaryData as $row)
            <tr>
                <td class="font-bold">{{ $row['month'] }}</td>
                <td class="text-center">
                    {{ $row['pkwt_count'] > 0 ? number_format($row['pkwt_count'], 0, ',', '.') : '-' }}
                </td>
                <td class="text-center">
                    {{ $row['phl_count'] > 0 ? number_format($row['phl_count'], 0, ',', '.') : '-' }}
                </td>
                <td class="currency">
                    {{ $row['pkwt_cost'] > 0 ? 'Rp ' . number_format($row['pkwt_cost'], 0, ',', '.') : '-' }}
                </td>
                <td class="currency">
                    {{ $row['phl_cost'] > 0 ? 'Rp ' . number_format($row['phl_cost'], 0, ',', '.') : '-' }}
                </td>
                <td class="currency font-bold" style="color: #1e3a8a;">
                    {{ $row['total_cost'] > 0 ? 'Rp ' . number_format($row['total_cost'], 0, ',', '.') : '-' }}
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td class="font-bold" style="text-transform: uppercase;">Total Setahun</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="currency">Rp {{ number_format($totalAnnualPkwtCost, 0, ',', '.') }}</td>
                <td class="currency">Rp {{ number_format($totalAnnualPhlCost, 0, ',', '.') }}</td>
                <td class="currency">Rp {{ number_format($totalAnnualPayroll, 0, ',', '.') }}</td>
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
