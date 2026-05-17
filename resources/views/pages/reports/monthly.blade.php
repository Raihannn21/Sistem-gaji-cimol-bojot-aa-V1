@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl" x-data="{ 
        selectedMonth: '{{ $selectedMonth }}', 
        selectedYear: '{{ $selectedYear }}',
        showExportDropdown: false,
        showMonthDropdown: false,
        showYearDropdown: false
    }">

        <!-- Header Action Section -->
        <div class="mb-6 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Rekapitulasi Gaji Bulanan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Visualisasi rincian pengeluaran payroll periode <span class="text-brand-600 font-semibold" x-text="selectedMonth + ' ' + selectedYear"></span></p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Month/Year Picker -->
                <div class="flex items-center rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] shadow-sm h-11 overflow-visible">
                    <div class="flex items-center px-3 border-r border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-white/[0.02] h-full">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    
                    <!-- Month Selector -->
                    <div class="relative h-full" @click.away="showMonthDropdown = false">
                        <button @click="showMonthDropdown = !showMonthDropdown" class="flex items-center gap-2 px-4 h-full text-xs font-bold text-gray-700 dark:text-gray-300 hover:text-brand-600 transition-colors group">
                            <span x-text="selectedMonth"></span>
                            <svg class="h-3 w-3 text-gray-400 group-hover:text-brand-500 transition-colors" :class="showMonthDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        
                        <div x-show="showMonthDropdown" x-transition x-cloak class="absolute left-0 mt-1 w-40 max-h-60 overflow-y-auto rounded-xl border border-gray-100 bg-white p-2 shadow-xl dark:border-gray-800 dark:bg-gray-900 z-[60] custom-scrollbar">
                            @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $m)
                                <button @click="selectedMonth = '{{ $m }}'; window.location.href = '?month={{ $m }}&year=' + selectedYear; showMonthDropdown = false" class="flex w-full items-center px-3 py-2 text-left text-xs font-bold rounded-lg transition-colors" :class="selectedMonth === '{{ $m }}' ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.03]'">
                                    {{ $m }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="h-4 w-px bg-gray-200 dark:bg-gray-700"></div>

                    <!-- Year Selector -->
                    <div class="relative h-full" @click.away="showYearDropdown = false">
                        <button @click="showYearDropdown = !showYearDropdown" class="flex items-center gap-2 px-4 h-full text-xs font-bold text-gray-700 dark:text-gray-300 hover:text-brand-600 transition-colors group">
                            <span x-text="selectedYear"></span>
                            <svg class="h-3 w-3 text-gray-400 group-hover:text-brand-500 transition-colors" :class="showYearDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        
                        <div x-show="showYearDropdown" x-transition x-cloak class="absolute left-0 mt-1 w-32 rounded-xl border border-gray-100 bg-white p-2 shadow-xl dark:border-gray-800 dark:bg-gray-900 z-[60]">
                            @foreach (['2024', '2025', '2026', '2027', '2028'] as $y)
                                <button @click="selectedYear = '{{ $y }}'; window.location.href = '?month=' + selectedMonth + '&year={{ $y }}'; showYearDropdown = false" class="flex w-full items-center px-3 py-2 text-left text-xs font-bold rounded-lg transition-colors" :class="selectedYear === '{{ $y }}' ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.03]'">
                                    {{ $y }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Export Button (Clean & Modern) -->
                <div class="relative" @click.away="showExportDropdown = false">
                    <x-ui.button variant="primary" @click="showExportDropdown = !showExportDropdown" className="flex items-center gap-2 shadow-lg shadow-brand-500/20">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Ekspor Laporan
                    </x-ui.button>
                    
                    <div x-show="showExportDropdown" x-transition x-cloak class="absolute right-0 mt-2 w-56 rounded-xl border border-gray-100 bg-white p-2 shadow-xl dark:border-gray-800 dark:bg-gray-900 z-50">
                        <a href="{{ route('reports.monthly.export-pdf', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" target="_blank" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-xs font-bold text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.03] transition-colors">
                            <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Ekspor ke PDF
                        </a>
                        <a href="{{ route('reports.monthly.export-excel', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-xs font-bold text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.03] transition-colors border-t border-gray-50 dark:border-gray-800 mt-1 pt-2">
                            <svg class="h-4 w-4 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Ekspor ke Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview: Horizontal Scrollable Row -->
        <div class="flex flex-row flex-nowrap gap-4 w-full overflow-x-auto pb-4 custom-scrollbar mb-8">
            <!-- Total Payroll -->
            <div class="flex-1 min-w-[280px] rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16V15m0 1v-8"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Total Payout Gaji</p>
                        <h4 class="mt-0.5 text-lg font-bold text-gray-800 dark:text-white/90 tabular-nums">Rp {{ number_format($totalPayroll, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <div class="mt-4 text-[10px] font-bold text-gray-400 italic">
                    Total pengeluaran PHL & PKWT
                </div>
            </div>

            <!-- Active Employees -->
            <div class="flex-1 min-w-[280px] rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Karyawan Terbayar</p>
                        <h4 class="mt-0.5 text-lg font-bold text-gray-800 dark:text-white/90 tabular-nums">{{ $totalEmployees }} Orang</h4>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-brand-500"></span>
                        <span class="text-[10px] font-bold text-gray-500 uppercase">{{ $totalPkwtEmployees }} PKWT</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                        <span class="text-[10px] font-bold text-gray-500 uppercase">{{ $totalPhlEmployees }} PHL</span>
                    </div>
                </div>
            </div>

            <!-- Total Overtime -->
            <div class="flex-1 min-w-[280px] rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-yellow-50 text-yellow-600 dark:bg-yellow-500/10 dark:text-yellow-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Biaya Lembur</p>
                        <h4 class="mt-0.5 text-lg font-bold text-gray-800 dark:text-white/90 tabular-nums">Rp {{ number_format($totalLembur, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <div class="mt-4 text-[10px] font-bold text-gray-400 italic">
                    Total biaya lembur bulan ini
                </div>
            </div>

            <!-- Total Deductions -->
            <div class="flex-1 min-w-[280px] rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Total Potongan</p>
                        <h4 class="mt-0.5 text-lg font-bold text-gray-800 dark:text-white/90 tabular-nums">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <p class="mt-4 text-[10px] font-bold text-gray-400 italic">Termasuk BPJS & PPh21 PKWT</p>
            </div>
        </div>


        <!-- Bottom Section: Detailed Table & Health Check Sidebar -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Detailed Table Section -->
            <div>
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wide">Rincian Per Kategori</h3>
                        <span class="text-[10px] font-bold text-green-600 uppercase bg-green-50 px-2 py-1 rounded-lg">Data Terverifikasi</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                                    <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400 tracking-wide">Kategori Biaya</th>
                                    <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400 tracking-wide text-right">PKWT</th>
                                    <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400 tracking-wide text-right">PHL</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach([
                                    ['label' => 'Gaji Pokok', 'pkwt' => $pkwtPokok, 'phl' => $phlPokok],
                                    ['label' => 'Lembur', 'pkwt' => $pkwtLembur, 'phl' => $phlLembur],
                                    ['label' => 'Tunjangan Risiko', 'pkwt' => $pkwtRisiko, 'phl' => $phlRisiko],
                                    ['label' => 'Tunjangan Lain-lain', 'pkwt' => $pkwtLain, 'phl' => 0],
                                    ['label' => 'Potongan BPJS & Pajak', 'pkwt' => -$pkwtPotongan, 'phl' => 0]
                                ] as $row)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-gray-300">{{ $row['label'] }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-bold {{ $row['pkwt'] < 0 ? 'text-red-500' : 'text-gray-800 dark:text-white' }} tabular-nums">
                                        {{ $row['pkwt'] < 0 ? '- Rp ' . number_format(abs($row['pkwt']), 0, ',', '.') : 'Rp ' . number_format($row['pkwt'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-bold {{ $row['phl'] < 0 ? 'text-red-500' : 'text-gray-800 dark:text-white' }} tabular-nums">
                                        {{ $row['phl'] < 0 ? '- Rp ' . number_format(abs($row['phl']), 0, ',', '.') : 'Rp ' . number_format($row['phl'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50/80 dark:bg-white/[0.03]">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-800 dark:text-white uppercase">Grand Total</td>
                                    <td class="px-6 py-4 text-right text-sm font-black text-brand-600 dark:text-brand-500 tabular-nums">Rp {{ number_format($totalPkwtCost, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-black text-brand-600 dark:text-brand-500 tabular-nums">Rp {{ number_format($totalPhlCost, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar Section (Health Check & Connected Periods) -->
            <div class="space-y-6">
                <!-- Payroll Distribution Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="mb-6">
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Distribusi Anggaran Gaji</h4>
                    </div>
                    <div class="space-y-6">
                        <div>
                            @php
                                $pkwtShare = $totalPayroll > 0 ? round(($totalPkwtCost / $totalPayroll) * 100, 1) : 0;
                                $phlShare = $totalPayroll > 0 ? round(($totalPhlCost / $totalPayroll) * 100, 1) : 0;
                            @endphp
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-400">Porsi Gaji PKWT</span>
                                <span class="text-xl font-bold text-brand-600 dark:text-brand-500">{{ $pkwtShare }}%</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-full rounded-full bg-brand-500 transition-all duration-1000 shadow-sm shadow-brand-500/20" style="width: {{ $pkwtShare }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-400">Porsi Gaji PHL</span>
                                <span class="text-xl font-bold text-green-600 dark:text-green-500">{{ $phlShare }}%</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-full rounded-full bg-green-500 transition-all duration-1000 shadow-sm shadow-green-500/20" style="width: {{ $phlShare }}%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 border-t border-gray-50 pt-6 dark:border-gray-800">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Rerata Gaji / Org</p>
                                <p class="mt-1 text-sm font-bold text-gray-800 dark:text-white">Rp {{ number_format($totalEmployees > 0 ? ($totalPayroll / $totalEmployees) : 0, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Total Payroll</p>
                                <p class="mt-1 text-sm font-bold text-green-600">Rp {{ number_format($totalPayroll, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Connected Payroll Periods -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <h4 class="mb-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Periode Payroll Terkait ({{ $phlPeriods->count() + $pkwtPeriods->count() }})</h4>
                    <div class="space-y-3 max-h-[350px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach ($pkwtPeriods as $period)
                            <a href="{{ route('payroll.pkwt.periods.show', $period->id) }}" class="group flex items-center justify-between rounded-xl border border-gray-100 p-4 transition-all hover:border-brand-500 hover:bg-gray-50/50 dark:border-gray-800 dark:hover:bg-white/[0.02]">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-500/10">
                                        <span class="text-xs font-bold text-brand-600">PKWT</span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800 dark:text-white truncate max-w-[160px]">{{ $period->title }}</p>
                                        <p class="mt-0.5 text-[9px] font-medium text-gray-400 uppercase">{{ $period->start_date->format('d M') }} - {{ $period->end_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[9px] font-bold {{ $period->status === 'Locked' ? 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-500' : 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-500' }} uppercase tracking-wider">{{ $period->status }}</span>
                            </a>
                        @endforeach

                        @foreach ($phlPeriods as $period)
                            <a href="{{ route('payroll.phl.periods.show', $period->id) }}" class="group flex items-center justify-between rounded-xl border border-gray-100 p-4 transition-all hover:border-brand-500 hover:bg-gray-50/50 dark:border-gray-800 dark:hover:bg-white/[0.02]">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-500/10">
                                        <span class="text-xs font-bold text-orange-600">PHL</span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800 dark:text-white truncate max-w-[160px]">{{ $period->title }}</p>
                                        <p class="mt-0.5 text-[9px] font-medium text-gray-400 uppercase">{{ $period->start_date->format('d M') }} - {{ $period->end_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[9px] font-bold {{ $period->status === 'Locked' ? 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-500' : 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-500' }} uppercase tracking-wider">{{ $period->status }}</span>
                            </a>
                        @endforeach

                        @if ($phlPeriods->isEmpty() && $pkwtPeriods->isEmpty())
                            <div class="text-center py-6 text-xs text-gray-400 italic">
                                Tidak ada periode payroll aktif di bulan ini.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
