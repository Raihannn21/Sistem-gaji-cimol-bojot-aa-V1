<table>
    <!-- Title Block -->
    <tr>
        <th colspan="4" style="font-size: 16px; font-weight: bold; text-align: left;">CIMOL BOJOT AA</th>
    </tr>
    <tr>
        <th colspan="4" style="font-size: 14px; font-weight: bold; text-align: left; color: #1e3a8a;">LAPORAN REKAPITULASI GAJI BULANAN (REKAP SUMMARY)</th>
    </tr>
    <tr>
        <th colspan="4" style="font-size: 11px; text-align: left; color: #475569;">Periode: {{ $selectedMonth }} {{ $selectedYear }}</th>
    </tr>
    <tr>
        <th colspan="4"></th>
    </tr>
    
    <!-- Metadata Block -->
    <tr>
        <th style="font-weight: bold; text-align: left;">Total Karyawan Terbayar</th>
        <th style="text-align: left;">: {{ $totalEmployees }} Orang ({{ $totalPkwtEmployees }} PKWT | {{ $totalPhlEmployees }} PHL)</th>
        <th style="font-weight: bold; text-align: right;">Tanggal Cetak</th>
        <th style="text-align: left;">: {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i') }}</th>
    </tr>
    <tr>
        <th style="font-weight: bold; text-align: left;">Total Pengeluaran Gaji</th>
        <th style="text-align: left;">: Rp {{ number_format($totalPayroll, 0, ',', '.') }}</th>
        <th style="font-weight: bold; text-align: right;">Dicetak Oleh</th>
        <th style="text-align: left;">: Administrator Sistem</th>
    </tr>
    <tr>
        <th colspan="4"></th>
    </tr>
    
    <!-- Section: Overview Cards inside Excel -->
    <tr style="background-color: #f8fafc;">
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f1f5f9;">KPI Rekapitulasi</th>
        <th colspan="3" style="font-weight: bold; text-align: left; border: 1px solid #000000; background-color: #f1f5f9;">Nilai Akumulasi Bulan Ini</th>
    </tr>
    <tr>
        <th style="border: 1px solid #e2e8f0; font-weight: bold; text-align: left;">Total Payout Gaji</th>
        <th colspan="3" style="border: 1px solid #e2e8f0; font-weight: bold; text-align: left;">Rp {{ number_format($totalPayroll, 0, ',', '.') }}</th>
    </tr>
    <tr>
        <th style="border: 1px solid #e2e8f0; font-weight: bold; text-align: left;">Karyawan Terbayar</th>
        <th colspan="3" style="border: 1px solid #e2e8f0; text-align: left;">{{ $totalEmployees }} Orang</th>
    </tr>
    <tr>
        <th style="border: 1px solid #e2e8f0; font-weight: bold; text-align: left;">Biaya Lembur</th>
        <th colspan="3" style="border: 1px solid #e2e8f0; text-align: left;">Rp {{ number_format($totalLembur, 0, ',', '.') }}</th>
    </tr>
    <tr>
        <th style="border: 1px solid #e2e8f0; font-weight: bold; text-align: left;">Total Potongan</th>
        <th colspan="3" style="border: 1px solid #e2e8f0; color: #dc2626; text-align: left;">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</th>
    </tr>
    <tr>
        <th colspan="4"></th>
    </tr>
    
    <!-- Main Data Headers -->
    <tr>
        <th style="font-weight: bold; text-align: left; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Kategori Biaya</th>
        <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">PKWT</th>
        <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">PHL</th>
        <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Total Akumulasi</th>
    </tr>

    <!-- Main Data Rows -->
    @foreach([
        ['label' => 'Gaji Pokok', 'pkwt' => $pkwtPokok, 'phl' => $phlPokok],
        ['label' => 'Lembur', 'pkwt' => $pkwtLembur, 'phl' => $phlLembur],
        ['label' => 'Tunjangan Risiko', 'pkwt' => $pkwtRisiko, 'phl' => $phlRisiko],
        ['label' => 'Tunjangan Lain-lain', 'pkwt' => $pkwtLain, 'phl' => 0],
        ['label' => 'Potongan BPJS & Pajak', 'pkwt' => -$pkwtPotongan, 'phl' => 0]
    ] as $row)
        @php
            $accumulated = $row['pkwt'] + $row['phl'];
        @endphp
        <tr>
            <td style="font-weight: bold; border: 1px solid #000000;">{{ $row['label'] }}</td>
            <td style="text-align: right; border: 1px solid #000000; {{ $row['pkwt'] < 0 ? 'color: #dc2626;' : '' }}">
                {{ $row['pkwt'] < 0 ? '- Rp ' . number_format(abs($row['pkwt']), 0, ',', '.') : 'Rp ' . number_format($row['pkwt'], 0, ',', '.') }}
            </td>
            <td style="text-align: right; border: 1px solid #000000;">
                Rp {{ number_format($row['phl'], 0, ',', '.') }}
            </td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000000;">
                {{ $accumulated < 0 ? '- Rp ' . number_format(abs($accumulated), 0, ',', '.') : 'Rp ' . number_format($accumulated, 0, ',', '.') }}
            </td>
        </tr>
    @endforeach
    
    <!-- Grand Total Row -->
    <tr style="background-color: #f1f5f9; font-weight: bold;">
        <td style="font-weight: bold; text-align: left; border: 1px solid #000000; background-color: #f1f5f9; text-transform: uppercase;">Grand Total Pengeluaran</td>
        <td style="text-align: right; font-weight: bold; border: 1px solid #000000; background-color: #f1f5f9; color: #dc2626;">
            Rp {{ number_format($totalPkwtCost, 0, ',', '.') }}
        </td>
        <td style="text-align: right; font-weight: bold; border: 1px solid #000000; background-color: #f1f5f9; color: #10b981;">
            Rp {{ number_format($totalPhlCost, 0, ',', '.') }}
        </td>
        <td style="text-align: right; font-weight: bold; border: 1px solid #000000; background-color: #f1f5f9; color: #1e293b;">
            Rp {{ number_format($totalPayroll, 0, ',', '.') }}
        </td>
    </tr>
    
    <!-- Empty Spacer Rows -->
    <tr>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td colspan="4"></td>
    </tr>
    
    <!-- Signature Section -->
    <tr>
        <td style="text-align: center; font-weight: bold; font-size: 10px;">Dibuat Oleh,</td>
        <td style="text-align: center; font-weight: bold; font-size: 10px;">Diperiksa Oleh,</td>
        <td colspan="2" style="text-align: center; font-weight: bold; font-size: 10px;">Disetujui Oleh,</td>
    </tr>
    <tr>
        <td colspan="4" style="height: 40px;"></td>
    </tr>
    <tr>
        <td style="text-align: center; font-weight: bold; text-decoration: underline;">Nama Staf</td>
        <td style="text-align: center; font-weight: bold; text-decoration: underline;">Nama Staf</td>
        <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">Nama Staf</td>
    </tr>
    <tr>
        <td style="text-align: center; font-size: 9px; color: #64748b;">Staff Finance &amp; Payroll</td>
        <td style="text-align: center; font-size: 9px; color: #64748b;">HR Manager</td>
        <td colspan="2" style="text-align: center; font-size: 9px; color: #64748b;">Direktur Utama</td>
    </tr>
</table>
