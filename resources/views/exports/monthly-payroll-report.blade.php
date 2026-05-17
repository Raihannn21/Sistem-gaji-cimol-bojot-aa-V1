<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Gaji - {{ $selectedMonth }} {{ $selectedYear }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        
        /* Header Block */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-title-container {
            width: 50%;
        }
        .company-name {
            font-size: 16px;
            font-weight: 800;
            color: #dc2626;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .company-sub {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 2px 0 0 0;
            font-weight: bold;
        }
        .report-meta {
            text-align: right;
            font-size: 8.5px;
            color: #64748b;
            font-weight: 500;
        }
        
        .divider {
            border-bottom: 2px solid #1e293b;
            margin: 10px 0;
        }
        
        /* Title & Subtitle */
        .report-title-block {
            text-align: center;
            margin: 15px 0 20px 0;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .report-period {
            font-size: 9.5px;
            color: #475569;
            font-weight: 600;
            margin-top: 4px;
            text-transform: uppercase;
        }
        
        /* Stats Cards Block (Grid using Table) */
        .stats-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-bottom: 25px;
        }
        .stats-card {
            width: 25%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            vertical-align: top;
        }
        .stats-label {
            font-size: 7.5px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .stats-value {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin: 2px 0;
        }
        .stats-desc {
            font-size: 7px;
            color: #94a3b8;
            font-weight: 500;
            margin-top: 4px;
        }
        
        /* Main Section Container */
        .section-title {
            font-size: 9px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.75px;
            margin-bottom: 8px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
        }
        
        /* Table Styling */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .data-table th {
            background-color: #f1f5f9;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            color: #334155;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            padding: 8px 10px;
        }
        .data-table td {
            padding: 8px 10px;
            font-size: 9px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .font-black {
            font-weight: 900;
        }
        .currency {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }
        
        .footer-row {
            background-color: #f8fafc;
            border-top: 2px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
        }
        .footer-row td {
            font-weight: bold;
            color: #0f172a;
        }
        
        /* Distribution Portions */
        .portion-container {
            margin-bottom: 25px;
            width: 100%;
        }
        .portion-row {
            width: 100%;
            margin-bottom: 8px;
        }
        .portion-label {
            font-size: 8.5px;
            font-weight: bold;
            color: #475569;
            margin-bottom: 4px;
        }
        .portion-track {
            height: 8px;
            width: 100%;
            background-color: #f1f5f9;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }
        .portion-bar-pkwt {
            height: 100%;
            background-color: #dc2626;
            border-radius: 4px;
        }
        .portion-bar-phl {
            height: 100%;
            background-color: #10b981;
            border-radius: 4px;
        }
        
        /* Signatures Block */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        .signature-title {
            font-size: 8.5px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 55px;
        }
        .signature-name {
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
            text-decoration: underline;
        }
        .signature-role {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    <!-- Header Block -->
    <table class="header-table">
        <tr>
            <td class="logo-title-container">
                <h1 class="company-name">Cimol Bojot AA</h1>
                <p class="company-sub">Sistem Manajemen Penggajian Karyawan</p>
            </td>
            <td class="report-meta">
                Tanggal Cetak: {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                Dicetak Oleh: Administrator Sistem
            </td>
        </tr>
    </table>
    
    <div class="divider"></div>
    
    <!-- Report Title -->
    <div class="report-title-block">
        <h2 class="report-title">Laporan Rekapitulasi Gaji Bulanan</h2>
        <div class="report-period">Periode: {{ $selectedMonth }} {{ $selectedYear }}</div>
    </div>
    
    <!-- Stats Overview Cards -->
    <table class="stats-table">
        <tr>
            <td class="stats-card">
                <div class="stats-label">Total Payout Gaji</div>
                <div class="stats-value">Rp {{ number_format($totalPayroll, 0, ',', '.') }}</div>
                <div class="stats-desc">Total pengeluaran PHL & PKWT</div>
            </td>
            <td class="stats-card">
                <div class="stats-label">Karyawan Terbayar</div>
                <div class="stats-value">{{ $totalEmployees }} Orang</div>
                <div class="stats-desc">{{ $totalPkwtEmployees }} PKWT | {{ $totalPhlEmployees }} PHL</div>
            </td>
            <td class="stats-card">
                <div class="stats-label">Biaya Lembur</div>
                <div class="stats-value">Rp {{ number_format($totalLembur, 0, ',', '.') }}</div>
                <div class="stats-desc">Biaya lembur bulan ini</div>
            </td>
            <td class="stats-card">
                <div class="stats-label">Total Potongan</div>
                <div class="stats-value" style="color: #dc2626;">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</div>
                <div class="stats-desc">BPJS & PPh21 Karyawan PKWT</div>
            </td>
        </tr>
    </table>
    
    <!-- Budget Distribution -->
    <div class="section-title">Distribusi Anggaran Gaji</div>
    @php
        $pkwtShare = $totalPayroll > 0 ? round(($totalPkwtCost / $totalPayroll) * 100, 1) : 0;
        $phlShare = $totalPayroll > 0 ? round(($totalPhlCost / $totalPayroll) * 100, 1) : 0;
    @endphp
    
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
        <tr>
            <td style="width: 48%; padding-right: 2%; vertical-align: top;">
                <div class="portion-label" style="margin-bottom: 6px;">
                    Porsi Gaji PKWT: <span style="color: #dc2626; font-weight: bold;">{{ $pkwtShare }}%</span> 
                    <span style="font-size: 7.5px; color: #94a3b8; font-weight: normal;">(Rp {{ number_format($totalPkwtCost, 0, ',', '.') }})</span>
                </div>
                <div class="portion-track">
                    <div class="portion-bar-pkwt" style="width: {{ $pkwtShare }}%"></div>
                </div>
            </td>
            <td style="width: 48%; padding-left: 2%; vertical-align: top;">
                <div class="portion-label" style="margin-bottom: 6px;">
                    Porsi Gaji PHL: <span style="color: #10b981; font-weight: bold;">{{ $phlShare }}%</span>
                    <span style="font-size: 7.5px; color: #94a3b8; font-weight: normal;">(Rp {{ number_format($totalPhlCost, 0, ',', '.') }})</span>
                </div>
                <div class="portion-track">
                    <div class="portion-bar-phl" style="width: {{ $phlShare }}%"></div>
                </div>
            </td>
        </tr>
    </table>
    
    <!-- Detailed Breakdown Table -->
    <div class="section-title">Rincian Kategori Pengeluaran</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="text-align: left;">Kategori Biaya</th>
                <th class="text-right">PKWT</th>
                <th class="text-right">PHL</th>
                <th class="text-right">Total Akumulasi</th>
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
                <td class="text-right currency {{ $row['pkwt'] < 0 ? 'potongan-amount' : '' }}" style="{{ $row['pkwt'] < 0 ? 'color: #dc2626;' : '' }}">
                    {{ $row['pkwt'] < 0 ? '- Rp ' . number_format(abs($row['pkwt']), 0, ',', '.') : 'Rp ' . number_format($row['pkwt'], 0, ',', '.') }}
                </td>
                <td class="text-right currency">
                    Rp {{ number_format($row['phl'], 0, ',', '.') }}
                </td>
                <td class="text-right currency font-bold">
                    @php
                        $accumulated = $row['pkwt'] + $row['phl'];
                    @endphp
                    {{ $accumulated < 0 ? '- Rp ' . number_format(abs($accumulated), 0, ',', '.') : 'Rp ' . number_format($accumulated, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
            <tr class="footer-row">
                <td class="font-bold" style="text-transform: uppercase;">Grand Total Pengeluaran</td>
                <td class="text-right currency font-black" style="color: #dc2626;">Rp {{ number_format($totalPkwtCost, 0, ',', '.') }}</td>
                <td class="text-right currency font-black" style="color: #10b981;">Rp {{ number_format($totalPhlCost, 0, ',', '.') }}</td>
                <td class="text-right currency font-black" style="font-size: 10px; color: #1e293b;">Rp {{ number_format($totalPayroll, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    
    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-title">Dibuat Oleh,</div>
                <div class="signature-name">Bella Lestari</div>
                <div class="signature-role">Staff Finance & Payroll</div>
            </td>
            <td>
                <div class="signature-title">Diperiksa Oleh,</div>
                <div class="signature-name">Raihan Syahbana</div>
                <div class="signature-role">HR Manager</div>
            </td>
            <td>
                <div class="signature-title">Disetujui Oleh,</div>
                <div class="signature-name">Ahmad Anwar</div>
                <div class="signature-role">Direktur Utama</div>
            </td>
        </tr>
    </table>

</body>
</html>
