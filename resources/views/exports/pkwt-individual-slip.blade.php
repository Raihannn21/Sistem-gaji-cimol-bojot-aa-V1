<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji PKWT - {{ $employee->name }}</title>
    <style>
        @page {
            size: a5 portrait;
            margin: 1.2cm 1.5cm 1.2cm 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        
        /* Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-img {
            height: 38px;
            width: auto;
        }
        .company-name {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            text-align: right;
        }
        .company-sub {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 2px 0 0 0;
            text-align: right;
            font-weight: bold;
        }

        /* Divider Line */
        .divider {
            border-bottom: 2px solid #0f172a;
            margin: 8px 0;
        }
        
        /* Slip Title & Period */
        .slip-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            text-align: center;
        }
        .period-title {
            font-size: 8px;
            color: #475569;
            text-align: center;
            margin: 2px 0 12px 0;
            font-weight: 600;
        }

        /* Employee Info Block (Clean Left-Right Table) */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 3px 0;
            font-size: 9.5px;
            vertical-align: top;
        }
        .info-label {
            color: #64748b;
            font-weight: 500;
            width: 70px;
        }
        .info-colon {
            color: #64748b;
            width: 12px;
            text-align: center;
        }
        .info-value {
            font-weight: bold;
            color: #0f172a;
        }

        /* Salary Breakdown Table */
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .salary-table th {
            background-color: #f1f5f9;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            color: #0f172a;
            font-weight: bold;
            font-size: 8.5px;
            text-transform: uppercase;
            padding: 6px 8px;
        }
        .salary-table td {
            padding: 6px 8px;
            vertical-align: top;
            font-size: 9.5px;
            border-bottom: 1px solid #f1f5f9;
        }
        .category-row {
            background-color: #f8fafc;
            font-weight: bold;
            font-size: 8.5px;
            color: #475569;
            text-transform: uppercase;
            padding: 4px 8px !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        .item-desc {
            font-weight: bold;
            color: #0f172a;
        }
        .item-sub {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 1.5px;
        }
        .item-amount {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
            font-family: 'Courier New', Courier, monospace;
        }
        .potongan-amount {
            color: #dc2626;
        }

        /* Grand Total Row */
        .total-row td {
            border-top: 1px solid #0f172a;
            padding: 8px;
            vertical-align: middle;
        }
        .total-label {
            font-size: 9px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .total-amount {
            font-size: 12px;
            font-weight: 800;
            color: #000000;
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
        }
        .double-border {
            border-bottom: 3px double #0f172a;
        }

        /* Signatures Section */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            font-size: 9px;
            color: #334155;
            vertical-align: top;
        }
        .signature-space {
            height: 45px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
        .signature-title {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 1px;
        }

        /* Footer Disclaimer */
        .footnote {
            margin-top: 30px;
            font-size: 7px;
            color: #94a3b8;
            text-align: center;
            font-style: italic;
            border-top: 1px dashed #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="text-align: left;">
                @if(file_exists(public_path('images/logo/logo-cimol-bojot-aa.png')))
                    <img src="{{ public_path('images/logo/logo-cimol-bojot-aa.png') }}" class="logo-img" alt="Logo">
                @else
                    <div style="font-weight: 900; font-size: 16px; color: #dc2626;">CIMOL BOJOT AA</div>
                @endif
            </td>
            <td style="text-align: right;">
                <h2 class="company-name">Cimol Bojot AA</h2>
                <p class="company-sub">Sistem Payroll Kontrak (PKWT)</p>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <h1 class="slip-title">SLIP GAJI KARYAWAN</h1>
    <p class="period-title">Periode: {{ $period->title }}</p>

    <!-- Employee Details -->
    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <table style="width: 100%;">
                    <tr>
                        <td class="info-label">Nama</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">{{ $employee->name }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%;">
                <table style="width: 100%;">
                    <tr>
                        <td class="info-label">Klasifikasi</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">Karyawan Kontrak (PKWT)</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Salary Calculation Table -->
    <table class="salary-table">
        <thead>
            <tr>
                <th style="text-align: left; width: 70%;">Deskripsi Rincian Upah</th>
                <th style="text-align: right; width: 30%;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <!-- I. PENERIMAAN -->
            <tr>
                <td colspan="2" class="category-row">I. Penerimaan / Penghasilan</td>
            </tr>
            <tr>
                <td>
                    <span class="item-desc">Gaji Pokok & Tunjangan Bulanan</span>
                    <div class="item-sub">Gaji Bulanan Penuh ({{ $total_days }} Hari Kerja Efektif Tim)</div>
                </td>
                <td class="item-amount">Rp {{ number_format($salary_monthly + $employee->attendance_allowance, 0, ',', '.') }}</td>
            </tr>
            @if($lembur > 0)
            <tr>
                <td>
                    <span class="item-desc">Upah Lembur</span>
                    <div class="item-sub">Kalkulasi jam lembur tervalidasi sistem</div>
                </td>
                <td class="item-amount">Rp {{ number_format($lembur, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($risiko > 0)
            <tr>
                <td>
                    <span class="item-desc">Tunjangan Risiko</span>
                    <div class="item-sub">Tunjangan operasional risiko lapangan</div>
                </td>
                <td class="item-amount">Rp {{ number_format($risiko, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($lain_lain > 0)
            <tr>
                <td>
                    <span class="item-desc">Tunjangan Lainnya</span>
                    <div class="item-sub">Tunjangan operasional lainnya</div>
                </td>
                <td class="item-amount">Rp {{ number_format($lain_lain, 0, ',', '.') }}</td>
            </tr>
            @endif

            <!-- II. POTONGAN -->
            <tr>
                <td colspan="2" class="category-row">II. Potongan Gaji</td>
            </tr>
            @if($days_absent > 0)
            <tr>
                <td>
                    <span class="item-desc" style="color: #dc2626;">Potongan Tidak Hadir</span>
                    <div class="item-sub">{{ $days_absent }} Hari Absen x Rp {{ number_format($tarif_harian, 0, ',', '.') }}</div>
                </td>
                <td class="item-amount potongan-amount">-Rp {{ number_format($days_absent * $tarif_harian, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($bpjs_health > 0)
            <tr>
                <td>
                    <span class="item-desc">BPJS Kesehatan</span>
                    <div class="item-sub">Iuran Jaminan Kesehatan</div>
                </td>
                <td class="item-amount potongan-amount">-Rp {{ number_format($bpjs_health, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($bpjs_tk > 0)
            <tr>
                <td>
                    <span class="item-desc">BPJS Ketenagakerjaan</span>
                    <div class="item-sub">Iuran Jaminan Sosial</div>
                </td>
                <td class="item-amount potongan-amount">-Rp {{ number_format($bpjs_tk, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($pph21 > 0)
            <tr>
                <td>
                    <span class="item-desc">PPh 21</span>
                    <div class="item-sub">Potongan Pajak Penghasilan</div>
                </td>
                <td class="item-amount potongan-amount">-Rp {{ number_format($pph21, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($potongan <= 0 && $days_absent <= 0)
            <tr>
                <td>
                    <span class="item-desc">Potongan Terdaftar</span>
                    <div class="item-sub">Tidak ada pemotongan terdaftar</div>
                </td>
                <td class="item-amount">Rp 0</td>
            </tr>
            @endif

            <!-- GRAND TOTAL -->
            <tr class="total-row">
                <td class="total-label">TOTAL GAJI BERSIH (TAKE HOME PAY)</td>
                <td class="total-amount">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    
    <div class="double-border" style="margin-top: -21px; margin-bottom: 25px;"></div>

    <!-- Signatures Section -->
    <table class="signature-table">
        <tr>
            <td>
                <div>Penerima Upah,</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $employee->name }}</div>
                <div class="signature-title">Karyawan PKWT</div>
            </td>
            <td>
                <div>Bandung, {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d M Y') }}</div>
                <div style="margin-top: 2px;">Bagian Keuangan,</div>
                <div class="signature-space"></div>
                <div class="signature-name">PT. CIMOL BOJOT AA</div>
                <div class="signature-title">Authorized Signature</div>
            </td>
        </tr>
    </table>

    <!-- Footnote -->
    <div class="footnote">
        Slip gaji ini dihasilkan secara elektronik dan merupakan bukti pembayaran resmi yang sah.
    </div>

</body>
</html>
