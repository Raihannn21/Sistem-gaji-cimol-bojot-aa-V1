<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $employee->name }}</title>
    <style>
        @page {
            size: a5 landscape;
            margin: 0.6cm 0.8cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #000000;
            font-size: 8.5px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        
        /* Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .logo-td {
            width: 48px;
            vertical-align: top;
        }
        .logo-img {
            height: 42px;
            width: auto;
        }
        .company-td {
            vertical-align: top;
            padding-left: 8px;
        }
        .company-name {
            font-size: 11px;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
        }
        .company-address {
            font-size: 7px;
            color: #333333;
            margin: 0;
            line-height: 1.2;
        }
        .title-td {
            text-align: right;
            vertical-align: top;
        }
        .slip-title {
            font-size: 13px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .slip-period {
            font-size: 8px;
            font-weight: bold;
            margin: 2px 0 0 0;
        }

        /* Divider */
        .double-divider {
            border-top: 1px solid #000000;
            border-bottom: 1px solid #000000;
            height: 2px;
            margin: 4px 0;
        }
        .single-divider {
            border-top: 1px solid #000000;
            margin: 4px 0;
        }
        .double-divider-bottom {
            border-top: 1px solid #000000;
            border-bottom: 1px solid #000000;
            height: 2px;
            margin: 4px 0;
        }

        /* Metadata Table */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .meta-table td {
            padding: 1.5px 0;
            font-size: 8.5px;
            vertical-align: top;
        }
        .meta-label {
            width: 90px;
        }
        .meta-colon {
            width: 10px;
            text-align: center;
        }
        .meta-val {
            font-weight: bold;
        }

        /* Side by Side Breakdown */
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .column-td {
            width: 50%;
            vertical-align: top;
        }
        .left-column {
            border-right: 1px solid #000000;
            padding-right: 12px;
        }
        .right-column {
            padding-left: 12px;
        }

        .section-title {
            font-weight: bold;
            font-size: 9px;
            padding-bottom: 4px;
            border-bottom: 1px solid #000000;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
        }
        .item-table td {
            padding: 2.5px 0;
            font-size: 8.5px;
            vertical-align: top;
        }
        .item-name {
            font-weight: bold;
        }
        .sub-item-name {
            padding-left: 10px;
        }
        .item-dots {
            text-align: right;
            padding-right: 4px;
        }
        .item-val {
            text-align: right;
            width: 75px;
            font-weight: bold;
        }

        /* Totals Block */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
            margin-bottom: 4px;
        }
        .totals-table td {
            font-weight: bold;
            font-size: 9px;
            padding: 4px 0;
        }

        /* Gaji Bersih Box */
        .gaji-bersih-container {
            border: 1px solid #000000;
            background-color: #f1f5f9;
            padding: 5px 8px;
            margin-bottom: 6px;
        }
        .gaji-bersih-table {
            width: 100%;
            border-collapse: collapse;
        }
        .gaji-bersih-table td {
            font-weight: bold;
            font-size: 11px;
        }

        /* Signatures Section */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            font-size: 8px;
            vertical-align: top;
        }
        .signature-space {
            height: 35px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td class="logo-td">
                @if(file_exists(public_path('images/logo/crk_logo.jpeg')))
                    <img src="{{ public_path('images/logo/crk_logo.jpeg') }}" class="logo-img" alt="Logo">
                @else
                    <div style="font-weight: bold; font-size: 12px; color: #e03a3a;">CRK GROUP</div>
                @endif
            </td>
            <td class="company-td">
                <h2 class="company-name">PT. CITARASA KULINER INDONESIA</h2>
                <p class="company-address">Head office: Jalan Dalem Kaum 76A, Regol, Kota Bandung 40251 Indonesia</p>
                <p class="company-address">Factory: Jalan Pasir Impun Mandalajati Kota Bandung 40194 Indonesia</p>
                <p class="company-address">Telp. 0877-2983-7101</p>
            </td>
            <td class="title-td">
                <h1 class="slip-title">SLIP GAJI</h1>
                <p class="slip-period">
                    BULAN {{ strtoupper($period->start_date->locale('id')->translatedFormat('F')) }}<br>
                    <span style="font-size: 6.5px; font-weight: normal; text-transform: none; display: block; margin-top: 2px;">
                        ({{ $period->start_date->locale('id')->translatedFormat('d M Y') }} - {{ $period->end_date->locale('id')->translatedFormat('d M Y') }})
                    </span>
                </p>
            </td>
        </tr>
    </table>

    <!-- Double Border Separator -->
    <div class="double-divider"></div>

    <!-- Employee Details Table -->
    <table class="meta-table">
        <tr>
            <td style="width: 50%;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td class="meta-label">Nama</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $employee->name }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Departemen</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $team_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Jabatan</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $employee->employment_type }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; padding-left: 12px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td class="meta-label">Target Hari Kerja</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $total_days }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Hari Kerja</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $days_worked }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Jam Lembur</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $overtime_hours ?? 0 }} Jam</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Divider Separator -->
    <div class="single-divider"></div>

    <!-- Breakdown Section -->
    <table class="breakdown-table">
        <tr>
            <!-- Left Column: Pendapatan -->
            <td class="column-td left-column">
                <div class="section-title">PENDAPATAN</div>
                <table class="item-table">
                    <tr>
                        <td class="item-name">Gaji Pokok</td>
                        <td class="item-dots">:</td>
                        <td class="item-val">Rp. {{ number_format($salary_monthly, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">Tunjangan</td>
                        <td class="item-dots"></td>
                        <td class="item-val"></td>
                    </tr>
                    <tr>
                        <td class="sub-item-name">Kehadiran</td>
                        <td class="item-dots">:</td>
                        <td class="item-val">Rp. {{ number_format($employee->attendance_allowance ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="sub-item-name">Risiko</td>
                        <td class="item-dots">:</td>
                        <td class="item-val">Rp. {{ number_format($risiko, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">Bonus/THR/Penghasilan Tidak Teratur</td>
                        <td class="item-dots">:</td>
                        <td class="item-val">Rp. {{ number_format($lain_lain, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">Uang Lembur</td>
                        <td class="item-dots">:</td>
                        <td class="item-val">Rp. {{ number_format($lembur, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>

            <!-- Right Column: Potongan -->
            <td class="column-td right-column">
                <div class="section-title">POTONGAN</div>
                <table class="item-table">
                    <tr>
                        <td class="item-name">BPJS TK</td>
                        <td class="item-dots">:</td>
                        <td class="item-val">Rp. {{ number_format($bpjs_tk, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">BPJS Kesehatan</td>
                        <td class="item-dots">:</td>
                        <td class="item-val">Rp. {{ number_format($bpjs_health, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">Kehadiran (Absensi/Prorate)</td>
                        <td class="item-dots">:</td>
                        <td class="item-val">Rp. {{ number_format($days_absent * $tarif_harian, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">Objek Pajak PPh 21</td>
                        <td class="item-dots">:</td>
                        <td class="item-val">Rp. {{ number_format($pph21, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Double Border Separator -->
    <div class="double-divider"></div>

    <!-- Totals Table -->
    <table class="totals-table">
        <tr>
            <td style="width: 50%; padding-right: 12px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: left;">JUMLAH PENDAPATAN</td>
                        <td style="text-align: right; width: 100px;">Rp. {{ number_format($salary_monthly + ($employee->attendance_allowance ?? 0) + $risiko + $lain_lain + $lembur, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; padding-left: 12px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: left;">JUMLAH POTONGAN</td>
                        <td style="text-align: right; width: 100px;">Rp. {{ number_format($bpjs_tk + $bpjs_health + ($days_absent * $tarif_harian) + $pph21, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="double-divider-bottom" style="margin-top: -2px;"></div>

    <!-- Gaji Bersih Box -->
    <div class="gaji-bersih-container">
        <table class="gaji-bersih-table">
            <tr>
                <td style="text-align: left; width: 50%;">GAJI BERSIH</td>
                <td style="text-align: right; width: 50%;">Rp. {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- Signatures Table -->
    <table class="signature-table">
        <tr>
            <td style="text-align: left; padding-left: 20px;">
            </td>
            <td style="text-align: right; padding-right: 20px;">
                <div>Bandung, {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d M Y') }}</div>
                <div style="margin-top: 1px;">Bagian Keuangan,</div>
                <div class="signature-space"></div>
                <div class="signature-name" style="text-decoration: none; font-weight: bold;">PT. CITARASA KULINER INDONESIA</div>
            </td>
        </tr>
    </table>

</body>
</html>
