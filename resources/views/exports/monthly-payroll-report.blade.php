<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Gaji - {{ $selectedMonth }} {{ $selectedYear }}</title>
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
        <div class="report-title">LAPORAN REKAPITULASI GAJI BULANAN (MONTHLY REPORT)</div>
        <div class="period-info">Periode: {{ $selectedMonth }} {{ $selectedYear }}</div>
    </div>

    @php
        $pkwtShare = $totalPayroll > 0 ? round(($totalPkwtCost / $totalPayroll) * 100, 1) : 0;
        $phlShare = $totalPayroll > 0 ? round(($totalPhlCost / $totalPayroll) * 100, 1) : 0;
    @endphp
    <table class="meta-table">
        <tr>
            <td class="meta-label">Periode</td>
            <td style="width: 10px;">:</td>
            <td class="meta-value">{{ $selectedMonth }} {{ $selectedYear }}</td>
            <td class="meta-label" style="text-align: right; width: 150px;">Tanggal Cetak</td>
            <td style="width: 10px; text-align: center;">:</td>
            <td class="meta-value" style="text-align: right; width: 120px;">{{ \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Total Gaji Dibayar</td>
            <td>:</td>
            <td class="meta-value">Rp {{ number_format($totalPayroll, 0, ',', '.') }}</td>
            <td class="meta-label" style="text-align: right;">Dicetak Oleh</td>
            <td style="text-align: center;">:</td>
            <td class="meta-value" style="text-align: right;">{{ auth()->user()->name ?? 'Administrator' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Karyawan Terbayar</td>
            <td>:</td>
            <td class="meta-value">{{ $totalEmployees }} Orang ({{ $totalPkwtEmployees }} PKWT | {{ $totalPhlEmployees }} PHL)</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td class="meta-label">Total Biaya PKWT</td>
            <td>:</td>
            <td class="meta-value">Rp {{ number_format($totalPkwtCost, 0, ',', '.') }} ({{ $pkwtShare }}%)</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td class="meta-label">Total Biaya PHL</td>
            <td>:</td>
            <td class="meta-value">Rp {{ number_format($totalPhlCost, 0, ',', '.') }} ({{ $phlShare }}%)</td>
            <td colspan="3"></td>
        </tr>
    </table>

    <table class="payroll-table">
        <thead>
            <tr>
                <th style="text-align: left; width: 25%;">Kategori Biaya</th>
                <th class="text-right" style="width: 25%;">PKWT</th>
                <th class="text-right" style="width: 25%;">PHL</th>
                <th class="text-right" style="width: 25%;">Total Akumulasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach([
                ['label' => 'Gaji Pokok', 'pkwt' => $pkwtPokok, 'phl' => $phlPokok],
                ['label' => 'Lembur', 'pkwt' => $pkwtLembur, 'phl' => $phlLembur],
                ['label' => 'Tunjangan Risiko', 'pkwt' => $pkwtRisiko, 'phl' => $phlRisiko],
                ['label' => 'Tunjangan Lain-lain', 'pkwt' => $pkwtLain, 'phl' => 0],
                ['label' => 'Potongan BPJS & Pajak', 'pkwt' => -$pkwtPotongan, 'phl' => 0]
            ] as $row)
            <tr>
                <td class="font-bold">{{ $row['label'] }}</td>
                <td class="currency" style="{{ $row['pkwt'] < 0 ? 'color: #dc2626;' : '' }}">
                    {{ $row['pkwt'] < 0 ? '- Rp ' . number_format(abs($row['pkwt']), 0, ',', '.') : 'Rp ' . number_format($row['pkwt'], 0, ',', '.') }}
                </td>
                <td class="currency">
                    Rp {{ number_format($row['phl'], 0, ',', '.') }}
                </td>
                <td class="currency font-bold" style="color: #1e3a8a;">
                    @php
                        $accumulated = $row['pkwt'] + $row['phl'];
                    @endphp
                    {{ $accumulated < 0 ? '- Rp ' . number_format(abs($accumulated), 0, ',', '.') : 'Rp ' . number_format($accumulated, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td class="font-bold" style="text-transform: uppercase;">Grand Total Pengeluaran</td>
                <td class="currency">Rp {{ number_format($totalPkwtCost, 0, ',', '.') }}</td>
                <td class="currency">Rp {{ number_format($totalPhlCost, 0, ',', '.') }}</td>
                <td class="currency">Rp {{ number_format($totalPayroll, 0, ',', '.') }}</td>
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
