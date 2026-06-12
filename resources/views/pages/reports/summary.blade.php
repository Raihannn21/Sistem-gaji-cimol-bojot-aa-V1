@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl">

        <!-- Header Action Section -->
        <div class="mb-6 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Rekapitulasi PKWT & PHL</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Komparasi data penggajian antara status PKWT dan PHL Tahun <span class="text-brand-600 font-bold">{{ $selectedYear }}</span></p>
            </div>
            
            <div class="flex flex-wrap items-center gap-4">
                <!-- Year Selector Form -->
                <form action="{{ route('reports.summary') }}" method="GET" id="yearFilterForm">
                    <input type="hidden" name="year" id="yearFilterInput" value="{{ $selectedYear }}">
                    <div class="flex items-center gap-3" x-data="{ open: false, selected: '{{ $selectedYear }}' }" @click.away="open = false">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tahun Laporan</span>
                        <div class="relative bg-transparent w-36">
                            <button type="button" @click="open = !open"
                                class="flex h-11 w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-750 shadow-sm focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-white/[0.03] dark:text-white cursor-pointer text-left">
                                <span x-text="selected"></span>
                                <svg class="stroke-current text-gray-550 dark:text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            
                            <div x-show="open" x-transition x-cloak
                                 class="absolute left-0 mt-2 w-full rounded-xl border border-gray-150 bg-white p-2 shadow-xl dark:border-gray-800 dark:bg-gray-900 z-50">
                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                    @foreach ($years as $yr)
                                        <button type="button" 
                                            @click="selected = '{{ $yr }}'; open = false; document.getElementById('yearFilterInput').value = '{{ $yr }}'; document.getElementById('yearFilterForm').submit();"
                                            class="flex w-full items-center rounded-lg px-3 py-2 text-sm text-gray-750 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 transition-colors"
                                            :class="selected == '{{ $yr }}' ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-500 font-bold' : ''">
                                            {{ $yr }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Export Dropdown Menu -->
                <div class="relative" x-data="{ showExportDropdown: false }" @click.away="showExportDropdown = false">
                    <button type="button" @click="showExportDropdown = !showExportDropdown" 
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 text-xs font-bold text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white dark:hover:bg-white/5 shadow-theme-xs whitespace-nowrap">
                        <svg class="h-4 w-4 text-gray-550 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export Report
                    </button>
                    
                    <div x-show="showExportDropdown" x-transition x-cloak 
                         class="absolute right-0 mt-2 w-48 rounded-xl border border-gray-150 bg-white p-2 shadow-xl dark:border-gray-800 dark:bg-gray-900 z-50">
                        <a href="{{ route('reports.summary.export-pdf', ['year' => $selectedYear]) }}" 
                           target="_blank"
                           class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-xs font-bold text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Export to PDF
                        </a>
                        <a href="{{ route('reports.summary.export-excel', ['year' => $selectedYear]) }}" 
                           class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-xs font-bold text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export to Excel
                        </a>
                    </div>
                </div>
            </div>
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
