@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl">

        <!-- Header Action Section -->
        <div class="mb-6 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Rekapitulasi PKWT & PHL</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Komparasi data penggajian antara status PKWT dan PHL Tahun <span class="text-brand-600 font-bold">{{ $selectedYear }}</span></p>
            </div>
            
            <!-- Year Selector Form -->
            <form action="{{ route('reports.summary') }}" method="GET" id="yearFilterForm">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tahun Laporan</span>
                    <select name="year" onchange="document.getElementById('yearFilterForm').submit()"
                        class="block w-36 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-white/[0.03] dark:text-white">
                        @foreach ($years as $yr)
                            <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-8">
            <!-- Summary Comparison Table -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wide">Tabel Komparasi Tahunan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                                <th class="px-6 py-4 text-xs font-black uppercase text-gray-500 tracking-widest">Bulan</th>
                                <th class="px-6 py-4 text-xs font-black uppercase text-gray-500 tracking-widest text-center">Jumlah PKWT</th>
                                <th class="px-6 py-4 text-xs font-black uppercase text-gray-500 tracking-widest text-center">Jumlah PHL</th>
                                <th class="px-6 py-4 text-xs font-black uppercase text-gray-500 tracking-widest text-right">Biaya PKWT</th>
                                <th class="px-6 py-4 text-xs font-black uppercase text-gray-500 tracking-widest text-right">Biaya PHL</th>
                                <th class="px-6 py-4 text-xs font-black uppercase text-gray-500 tracking-widest text-right">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-medium text-sm">
                            @foreach ($summaryData as $row)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-800 dark:text-white/90">{{ $row['month'] }}</td>
                                <td class="px-6 py-4 text-center tabular-nums text-gray-700 dark:text-gray-300 font-semibold">
                                    {{ $row['pkwt_count'] > 0 ? number_format($row['pkwt_count'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center tabular-nums text-gray-700 dark:text-gray-300 font-semibold">
                                    {{ $row['phl_count'] > 0 ? number_format($row['phl_count'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right tabular-nums text-brand-600 font-bold">
                                    {{ $row['pkwt_cost'] > 0 ? 'Rp ' . number_format($row['pkwt_cost'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right tabular-nums text-brand-600 font-bold">
                                    {{ $row['phl_cost'] > 0 ? 'Rp ' . number_format($row['phl_cost'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right font-black text-gray-900 dark:text-white tabular-nums">
                                    {{ $row['total_cost'] > 0 ? 'Rp ' . number_format($row['total_cost'], 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                            @endforeach
                            <!-- Total Annual row -->
                            <tr class="bg-gray-50/40 dark:bg-white/[0.01] font-black border-t-2 border-gray-200 dark:border-gray-800">
                                <td class="px-6 py-4 text-gray-900 dark:text-white uppercase tracking-wider text-xs">Total Setahun</td>
                                <td class="px-6 py-4 text-center tabular-nums text-gray-900 dark:text-white">-</td>
                                <td class="px-6 py-4 text-center tabular-nums text-gray-900 dark:text-white">-</td>
                                <td class="px-6 py-4 text-right tabular-nums text-brand-700 dark:text-brand-400">Rp {{ number_format($totalAnnualPkwtCost, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right tabular-nums text-brand-700 dark:text-brand-400">Rp {{ number_format($totalAnnualPhlCost, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right text-gray-950 dark:text-white tabular-nums text-base">Rp {{ number_format($totalAnnualPayroll, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Distribution Insight -->
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <!-- PKWT Distribution -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="mb-6 flex items-center justify-between">
                        <h4 class="text-xs font-bold text-gray-800 dark:text-white uppercase tracking-wide">Komposisi Biaya PKWT</h4>
                        <span class="text-[10px] font-bold text-brand-600 italic">{{ number_format($pkwtShare, 1, ',', '.') }}% dari Total Payroll</span>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-bold uppercase text-gray-500">
                                <span>Gaji Pokok & Potongan</span>
                                <span class="text-gray-800 dark:text-white">{{ number_format($pkwtPokokPercent, 1, ',', '.') }}%</span>
                            </div>
                            <div class="h-1.5 w-full bg-gray-100 rounded-full dark:bg-white/5 overflow-hidden">
                                <div class="h-full bg-brand-500 rounded-full" style="width: {{ $pkwtPokokPercent }}%"></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-bold uppercase text-gray-500">
                                <span>Lembur & Tunjangan</span>
                                <span class="text-gray-800 dark:text-white">{{ number_format($pkwtLemburTunjanganPercent, 1, ',', '.') }}%</span>
                            </div>
                            <div class="h-1.5 w-full bg-gray-100 rounded-full dark:bg-white/5 overflow-hidden">
                                <div class="h-full rounded-full" style="background-color: #10b981; width: {{ $pkwtLemburTunjanganPercent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PHL Distribution -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="mb-6 flex items-center justify-between">
                        <h4 class="text-xs font-bold text-gray-800 dark:text-white uppercase tracking-wide">Komposisi Biaya PHL</h4>
                        <span class="text-[10px] font-bold text-brand-600 italic">{{ number_format($phlShare, 1, ',', '.') }}% dari Total Payroll</span>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-bold uppercase text-gray-500">
                                <span>Gaji Harian Pokok</span>
                                <span class="text-gray-800 dark:text-white">{{ number_format($phlPokokPercent, 1, ',', '.') }}%</span>
                            </div>
                            <div class="h-1.5 w-full bg-gray-100 rounded-full dark:bg-white/5 overflow-hidden">
                                <div class="h-full bg-brand-500 rounded-full" style="width: {{ $phlPokokPercent }}%"></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-bold uppercase text-gray-500">
                                <span>Lembur & Risiko</span>
                                <span class="text-gray-800 dark:text-white">{{ number_format($phlLemburTunjanganPercent, 1, ',', '.') }}%</span>
                            </div>
                            <div class="h-1.5 w-full bg-gray-100 rounded-full dark:bg-white/5 overflow-hidden">
                                <div class="h-full rounded-full" style="background-color: #10b981; width: {{ $phlLemburTunjanganPercent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
