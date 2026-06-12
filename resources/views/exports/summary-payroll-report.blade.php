<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Gaji Tahunan - {{ $selectedYear }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 1.5cm;
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
            width: 33.33%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            vertical-align: top;
        }
        .stats-card-full {
            width: 100%;
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
        .text-center {
            text-align: center;
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
                Tanggal Cetak: {{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('D MMMM Y') }}<br>
                Dicetak Oleh: Administrator Sistem
            </td>
        </tr>
    </table>
    
    <div class="divider"></div>
    
    <!-- Report Title -->
    <div class="report-title-block">
        <h2 class="report-title">Laporan Rekapitulasi Gaji Tahunan</h2>
        <div class="report-period">Tahun Laporan: {{ $selectedYear }}</div>
    </div>
    
    <!-- Stats Overview Cards -->
    <table class="stats-table">
        <tr>
            <td class="stats-card">
                <div class="stats-label">Total Gaji Tahunan</div>
                <div class="stats-value">Rp {{ number_format($totalAnnualPayroll, 0, ',', '.') }}</div>
                <div class="stats-desc">Total pengeluaran PKWT & PHL</div>
            </td>
            <td class="stats-card">
                <div class="stats-label">Total Biaya PKWT</div>
                <div class="stats-value" style="color: #dc2626;">Rp {{ number_format($totalAnnualPkwtCost, 0, ',', '.') }}</div>
                <div class="stats-desc">Kontribusi {{ number_format($pkwtShare, 1, ',', '.') }}% dari total biaya</div>
            </td>
            <td class="stats-card">
                <div class="stats-label">Total Biaya PHL</div>
                <div class="stats-value" style="color: #10b981;">Rp {{ number_format($totalAnnualPhlCost, 0, ',', '.') }}</div>
                <div class="stats-desc">Kontribusi {{ number_format($phlShare, 1, ',', '.') }}% dari total biaya</div>
            </td>
        </tr>
    </table>
    
    <!-- Budget Distribution -->
    <div class="section-title">Distribusi Anggaran Gaji Tahunan</div>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
        <tr>
            <td style="width: 48%; padding-right: 2%; vertical-align: top;">
                <div class="portion-label" style="margin-bottom: 6px;">
                    Porsi Gaji PKWT: <span style="color: #dc2626; font-weight: bold;">{{ number_format($pkwtShare, 1, ',', '.') }}%</span> 
                    <span style="font-size: 7.5px; color: #94a3b8; font-weight: normal;">(Rp {{ number_format($totalAnnualPkwtCost, 0, ',', '.') }})</span>
                </div>
                <div class="portion-track">
                    <div class="portion-bar-pkwt" style="width: {{ $pkwtShare }}%"></div>
                </div>
            </td>
            <td style="width: 48%; padding-left: 2%; vertical-align: top;">
                <div class="portion-label" style="margin-bottom: 6px;">
                    Porsi Gaji PHL: <span style="color: #10b981; font-weight: bold;">{{ number_format($phlShare, 1, ',', '.') }}%</span>
                    <span style="font-size: 7.5px; color: #94a3b8; font-weight: normal;">(Rp {{ number_format($totalAnnualPhlCost, 0, ',', '.') }})</span>
                </div>
                <div class="portion-track">
                    <div class="portion-bar-phl" style="width: {{ $phlShare }}%"></div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Component Cost Breakdown -->
    <div class="section-title">Komposisi Internal Biaya Karyawan</div>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
        <tr>
            <td style="width: 48%; padding-right: 2%; vertical-align: top; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                <div class="font-bold" style="font-size: 8px; text-transform: uppercase; margin-bottom: 8px; color: #dc2626;">Biaya PKWT (Kontrak)</div>
                <div style="margin-bottom: 6px;">
                    <div style="font-size: 7.5px; color: #64748b;">Gaji Pokok &amp; Potongan: <strong>{{ number_format($pkwtPokokPercent, 1, ',', '.') }}%</strong></div>
                    <div class="portion-track" style="height: 4px; margin-top: 2px;"><div class="portion-bar-pkwt" style="width: {{ $pkwtPokokPercent }}%"></div></div>
                </div>
                <div>
                    <div style="font-size: 7.5px; color: #64748b;">Lembur &amp; Tunjangan: <strong>{{ number_format($pkwtLemburTunjanganPercent, 1, ',', '.') }}%</strong></div>
                    <div class="portion-track" style="height: 4px; margin-top: 2px;"><div class="portion-bar-pkwt" style="background-color: #10b981; width: {{ $pkwtLemburTunjanganPercent }}%"></div></div>
                </div>
            </td>
            <td style="width: 48%; padding-left: 2%; vertical-align: top; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                <div class="font-bold" style="font-size: 8px; text-transform: uppercase; margin-bottom: 8px; color: #10b981;">Biaya PHL (Harian Lepas)</div>
                <div style="margin-bottom: 6px;">
                    <div style="font-size: 7.5px; color: #64748b;">Gaji Harian Pokok: <strong>{{ number_format($phlPokokPercent, 1, ',', '.') }}%</strong></div>
                    <div class="portion-track" style="height: 4px; margin-top: 2px;"><div class="portion-bar-phl" style="width: {{ $phlPokokPercent }}%"></div></div>
                </div>
                <div>
                    <div style="font-size: 7.5px; color: #64748b;">Lembur &amp; Risiko: <strong>{{ number_format($phlLemburTunjanganPercent, 1, ',', '.') }}%</strong></div>
                    <div class="portion-track" style="height: 4px; margin-top: 2px;"><div class="portion-bar-phl" style="background-color: #f59e0b; width: {{ $phlLemburTunjanganPercent }}%"></div></div>
                </div>
            </td>
        </tr>
    </table>
    
    <!-- Detailed Breakdown Table -->
    <div class="section-title">Tabel Komparasi Penggajian Bulanan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="text-align: left;">Bulan</th>
                <th class="text-center">Jumlah PKWT</th>
                <th class="text-center">Jumlah PHL</th>
                <th class="text-right">Biaya PKWT</th>
                <th class="text-right">Biaya PHL</th>
                <th class="text-right">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($summaryData as $row)
            <tr>
                <td class="font-bold">{{ $row['month'] }}</td>
                <td class="text-center tabular-nums">
                    {{ $row['pkwt_count'] > 0 ? number_format($row['pkwt_count'], 0, ',', '.') : '-' }}
                </td>
                <td class="text-center tabular-nums">
                    {{ $row['phl_count'] > 0 ? number_format($row['phl_count'], 0, ',', '.') : '-' }}
                </td>
                <td class="text-right currency">
                    {{ $row['pkwt_cost'] > 0 ? 'Rp ' . number_format($row['pkwt_cost'], 0, ',', '.') : '-' }}
                </td>
                <td class="text-right currency">
                    {{ $row['phl_cost'] > 0 ? 'Rp ' . number_format($row['phl_cost'], 0, ',', '.') : '-' }}
                </td>
                <td class="text-right currency font-bold">
                    {{ $row['total_cost'] > 0 ? 'Rp ' . number_format($row['total_cost'], 0, ',', '.') : '-' }}
                </td>
            </tr>
            @endforeach
            <tr class="footer-row">
                <td class="font-bold" style="text-transform: uppercase;">Total Setahun</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-right currency font-black" style="color: #dc2626;">Rp {{ number_format($totalAnnualPkwtCost, 0, ',', '.') }}</td>
                <td class="text-right currency font-black" style="color: #10b981;">Rp {{ number_format($totalAnnualPhlCost, 0, ',', '.') }}</td>
                <td class="text-right currency font-black" style="font-size: 10px; color: #1e293b;">Rp {{ number_format($totalAnnualPayroll, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    
    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-title">Dibuat Oleh,</div>
                <div class="signature-name">Nama Staf</div>
                <div class="signature-role">Staff Finance & Payroll</div>
            </td>
            <td>
                <div class="signature-title">Diperiksa Oleh,</div>
                <div class="signature-name">Nama Staf</div>
                <div class="signature-role">HR Manager</div>
            </td>
            <td>
                <div class="signature-title">Disetujui Oleh,</div>
                <div class="signature-name">Nama Staf</div>
                <div class="signature-role">Direktur Utama</div>
            </td>
        </tr>
    </table>

</body>
</html>
