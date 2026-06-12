<table>
    <!-- Title Block -->
    <tr>
        <th colspan="6" style="font-size: 16px; font-weight: bold; text-align: left;">CIMOL BOJOT AA</th>
    </tr>
    <tr>
        <th colspan="6" style="font-size: 14px; font-weight: bold; text-align: left; color: #1e3a8a;">LAPORAN REKAPITULASI GAJI TAHUNAN (SUMMARY PKWT & PHL)</th>
    </tr>
    <tr>
        <th colspan="6" style="font-size: 11px; text-align: left; color: #475569;">Tahun Laporan: {{ $selectedYear }}</th>
    </tr>
    <tr>
        <th colspan="6"></th>
    </tr>
    
    <!-- Metadata Block -->
    <tr>
        <th colspan="3" style="font-weight: bold; text-align: left;">Total Pengeluaran Setahun</th>
        <th style="text-align: left;">: Rp {{ number_format($totalAnnualPayroll, 0, ',', '.') }}</th>
        <th style="font-weight: bold; text-align: right;">Tanggal Cetak</th>
        <th style="text-align: left;">: {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i') }}</th>
    </tr>
    <tr>
        <th colspan="3" style="font-weight: bold; text-align: left;">Total Biaya PKWT</th>
        <th style="text-align: left;">: Rp {{ number_format($totalAnnualPkwtCost, 0, ',', '.') }} ({{ number_format($pkwtShare, 1, ',', '.') }}%)</th>
        <th style="font-weight: bold; text-align: right;">Dicetak Oleh</th>
        <th style="text-align: left;">: Administrator Sistem</th>
    </tr>
    <tr>
        <th colspan="3" style="font-weight: bold; text-align: left;">Total Biaya PHL</th>
        <th style="text-align: left;">: Rp {{ number_format($totalAnnualPhlCost, 0, ',', '.') }} ({{ number_format($phlShare, 1, ',', '.') }}%)</th>
        <th colspan="2"></th>
    </tr>
    <tr>
        <th colspan="6"></th>
    </tr>
    
    <!-- Distribution Section -->
    <tr style="background-color: #f8fafc;">
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f1f5f9;">Komposisi Biaya</th>
        <th colspan="2" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f1f5f9;">PKWT</th>
        <th colspan="3" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f1f5f9;">PHL</th>
    </tr>
    <tr>
        <td style="border: 1px solid #e2e8f0; font-weight: bold;">Gaji Pokok / Harian</td>
        <td colspan="2" style="border: 1px solid #e2e8f0; text-align: right;">{{ number_format($pkwtPokokPercent, 1, ',', '.') }}%</td>
        <td colspan="3" style="border: 1px solid #e2e8f0; text-align: right;">{{ number_format($phlPokokPercent, 1, ',', '.') }}%</td>
    </tr>
    <tr>
        <td style="border: 1px solid #e2e8f0; font-weight: bold;">Lembur &amp; Tunjangan</td>
        <td colspan="2" style="border: 1px solid #e2e8f0; text-align: right;">{{ number_format($pkwtLemburTunjanganPercent, 1, ',', '.') }}%</td>
        <td colspan="3" style="border: 1px solid #e2e8f0; text-align: right;">{{ number_format($phlLemburTunjanganPercent, 1, ',', '.') }}%</td>
    </tr>
    
    <tr>
        <th colspan="6"></th>
    </tr>
    
    <!-- Main Data Headers -->
    <tr>
        <th style="font-weight: bold; text-align: left; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Bulan</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Jumlah PKWT</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Jumlah PHL</th>
        <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Biaya PKWT</th>
        <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Biaya PHL</th>
        <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Total Biaya</th>
    </tr>

    <!-- Main Data Rows -->
    @foreach ($summaryData as $row)
        <tr>
            <td style="font-weight: bold; border: 1px solid #000000;">{{ $row['month'] }}</td>
            <td style="text-align: center; border: 1px solid #000000;">
                {{ $row['pkwt_count'] > 0 ? $row['pkwt_count'] : '-' }}
            </td>
            <td style="text-align: center; border: 1px solid #000000;">
                {{ $row['phl_count'] > 0 ? $row['phl_count'] : '-' }}
            </td>
            <td style="text-align: right; border: 1px solid #000000;">
                {{ $row['pkwt_cost'] > 0 ? 'Rp ' . number_format($row['pkwt_cost'], 0, ',', '.') : '-' }}
            </td>
            <td style="text-align: right; border: 1px solid #000000;">
                {{ $row['phl_cost'] > 0 ? 'Rp ' . number_format($row['phl_cost'], 0, ',', '.') : '-' }}
            </td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000000;">
                {{ $row['total_cost'] > 0 ? 'Rp ' . number_format($row['total_cost'], 0, ',', '.') : '-' }}
            </td>
        </tr>
    @endforeach
    
    <!-- Grand Total Row -->
    <tr style="background-color: #f1f5f9; font-weight: bold;">
        <td style="font-weight: bold; text-align: left; border: 1px solid #000000; background-color: #f1f5f9; text-transform: uppercase;">Total Setahun</td>
        <td style="text-align: center; font-weight: bold; border: 1px solid #000000; background-color: #f1f5f9;">-</td>
        <td style="text-align: center; font-weight: bold; border: 1px solid #000000; background-color: #f1f5f9;">-</td>
        <td style="text-align: right; font-weight: bold; border: 1px solid #000000; background-color: #f1f5f9; color: #1e3a8a;">
            Rp {{ number_format($totalAnnualPkwtCost, 0, ',', '.') }}
        </td>
        <td style="text-align: right; font-weight: bold; border: 1px solid #000000; background-color: #f1f5f9; color: #10b981;">
            Rp {{ number_format($totalAnnualPhlCost, 0, ',', '.') }}
        </td>
        <td style="text-align: right; font-weight: bold; border: 1px solid #000000; background-color: #f1f5f9; color: #1e293b; font-size: 11px;">
            Rp {{ number_format($totalAnnualPayroll, 0, ',', '.') }}
        </td>
    </tr>
    
    <!-- Empty Spacer Rows -->
    <tr>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
    
    <!-- Signature Section -->
    <tr>
        <td colspan="2" style="text-align: center; font-weight: bold; font-size: 10px;">Dibuat Oleh,</td>
        <td colspan="2" style="text-align: center; font-weight: bold; font-size: 10px;">Diperiksa Oleh,</td>
        <td colspan="2" style="text-align: center; font-weight: bold; font-size: 10px;">Disetujui Oleh,</td>
    </tr>
    <tr>
        <td colspan="6" style="height: 40px;"></td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">Nama Staf</td>
        <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">Nama Staf</td>
        <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">Nama Staf</td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center; font-size: 9px; color: #64748b;">Staff Finance &amp; Payroll</td>
        <td colspan="2" style="text-align: center; font-size: 9px; color: #64748b;">HR Manager</td>
        <td colspan="2" style="text-align: center; font-size: 9px; color: #64748b;">Direktur Utama</td>
    </tr>
</table>
