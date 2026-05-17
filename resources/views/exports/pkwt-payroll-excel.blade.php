<table>
    <thead>
        <!-- Title Block -->
        <tr>
            <th colspan="12" style="font-size: 16px; font-weight: bold; text-align: left;">CIMOL BOJOT AA</th>
        </tr>
        <tr>
            <th colspan="12" style="font-size: 14px; font-weight: bold; text-align: left; color: #1e3a8a;">LAPORAN REKAPITULASI PENGGAJIAN KARYAWAN KONTRAK (PKWT)</th>
        </tr>
        <tr>
            <th colspan="12" style="font-size: 11px; text-align: left; color: #475569;">Periode: {{ $period->title }} ({{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }})</th>
        </tr>
        <tr>
            <th colspan="12"></th>
        </tr>
        <!-- Metadata Block -->
        <tr>
            <th style="font-weight: bold; text-align: left;">Status Periode</th>
            <th colspan="2" style="text-align: left;">: {{ $period->status === 'Locked' ? 'TERKUNCI / SUDAH DIBAYAR' : 'TERBUKA / DRAFT' }}</th>
            <th colspan="5"></th>
            <th style="font-weight: bold; text-align: right;">Tanggal Cetak</th>
            <th colspan="3" style="text-align: left;">: {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i') }}</th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: left;">Total Karyawan</th>
            <th colspan="2" style="text-align: left;">: {{ count($rows) }} Orang</th>
            <th colspan="5"></th>
            <th style="font-weight: bold; text-align: right;">Dicetak Oleh</th>
            <th colspan="3" style="text-align: left;">: {{ auth()->user()->name ?? 'Administrator' }}</th>
        </tr>
        <tr>
            <th colspan="12"></th>
        </tr>
        <!-- Table Headers -->
        <tr>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">No</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">NRP</th>
            <th style="font-weight: bold; text-align: left; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Nama Karyawan</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Hadir (Hari)</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Absen (Hari)</th>
            <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Tarif Harian</th>
            <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Gaji Pokok Didapat</th>
            <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Lembur</th>
            <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Risiko</th>
            <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Lain-lain</th>
            <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Potongan</th>
            <th style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">Total Bersih</th>
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
                <td style="text-align: center; border: 1px solid #000000;">{{ $no++ }}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['employee']->emp_no }}</td>
                <td style="font-weight: bold; border: 1px solid #000000;">{{ $row['employee']->name }}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['days_worked'] }} Hari</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['days_absent'] }} Hari</td>
                <td style="text-align: right; border: 1px solid #000000;">Rp {{ number_format($row['tarif_harian'], 0, ',', '.') }}</td>
                <td style="text-align: right; font-weight: bold; border: 1px solid #000000;">Rp {{ number_format($row['gaji_pokok_didapat'], 0, ',', '.') }}</td>
                <td style="text-align: right; border: 1px solid #000000;">Rp {{ number_format($row['lembur'], 0, ',', '.') }}</td>
                <td style="text-align: right; border: 1px solid #000000;">Rp {{ number_format($row['risiko'], 0, ',', '.') }}</td>
                <td style="text-align: right; border: 1px solid #000000;">Rp {{ number_format($row['lain_lain'], 0, ',', '.') }}</td>
                <td style="text-align: right; border: 1px solid #000000; color: #dc2626;">Rp {{ number_format($row['potongan'], 0, ',', '.') }}</td>
                <td style="text-align: right; font-weight: bold; border: 1px solid #000000; background-color: #f8fafc; color: #1e3a8a;">Rp {{ number_format($row['total_bersih'], 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <!-- Grand Total Row -->
        <tr style="background-color: #f1f5f9; font-weight: bold;">
            <td colspan="3" style="text-align: center; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9;">TOTAL KESELURUHAN (GRAND TOTAL)</td>
            <td style="text-align: center; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9;">{{ $grandWorked }} Hari</td>
            <td style="text-align: center; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9;">{{ $grandAbsent }} Hari</td>
            <td style="border: 1px solid #000000; background-color: #f1f5f9; text-align: center;">-</td>
            <td style="text-align: right; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9;">Rp {{ number_format($grandPokok, 0, ',', '.') }}</td>
            <td style="text-align: right; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9;">Rp {{ number_format($grandLembur, 0, ',', '.') }}</td>
            <td style="text-align: right; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9;">Rp {{ number_format($grandRisiko, 0, ',', '.') }}</td>
            <td style="text-align: right; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9;">Rp {{ number_format($grandLain, 0, ',', '.') }}</td>
            <td style="text-align: right; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9; color: #dc2626;">Rp {{ number_format($grandPotongan, 0, ',', '.') }}</td>
            <td style="text-align: right; border: 1px solid #000000; font-weight: bold; background-color: #f1f5f9; color: #1e3a8a;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
        </tr>
        <!-- Empty Spacer Rows -->
        <tr>
            <td colspan="12"></td>
        </tr>
        <tr>
            <td colspan="12"></td>
        </tr>
        <!-- Signature Block -->
        <tr>
            <td colspan="3" style="text-align: center; font-weight: bold;">Bandung, {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d M Y') }}</td>
            <td colspan="6"></td>
            <td colspan="3" style="text-align: center; font-weight: bold;">Disetujui Oleh,</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center;">Dibuat Oleh,</td>
            <td colspan="6"></td>
            <td colspan="3" style="text-align: center;">Pimpinan AA</td>
        </tr>
        <tr style="height: 40px;">
            <td colspan="12" style="height: 40px;"></td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ auth()->user()->name ?? 'Administrator' }}</td>
            <td colspan="6"></td>
            <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">Owner Cimol Bojot AA</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; color: #64748b; font-size: 10px;">HRD / Payroll Officer</td>
            <td colspan="6"></td>
            <td colspan="3" style="text-align: center; color: #64748b; font-size: 10px;">Direktur / Owner</td>
        </tr>
    </tbody>
</table>
