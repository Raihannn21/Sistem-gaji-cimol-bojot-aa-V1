@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl" x-data="{ 
        selectedYear: '2025'
    }">

        <!-- Header Action Section -->
        <div class="mb-6 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Rekapitulasi PKWT & PHL</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Komparasi data penggajian antara status PKWT dan PHL Tahun <span class="text-brand-600 font-semibold" x-text="selectedYear"></span></p>
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
                            @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli'] as $month)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-800 dark:text-white/90">{{ $month }}</td>
                                <td class="px-6 py-4 text-center tabular-nums">156</td>
                                <td class="px-6 py-4 text-center tabular-nums">92</td>
                                <td class="px-6 py-4 text-right tabular-nums text-brand-600 font-bold">968.460.000</td>
                                <td class="px-6 py-4 text-right tabular-nums text-brand-600 font-bold">289.700.000</td>
                                <td class="px-6 py-4 text-right font-black text-gray-900 dark:text-white tabular-nums">1.258.160.000</td>
                            </tr>
                            @endforeach
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
                        <span class="text-[10px] font-bold text-brand-600 italic">81.5% dari Total Payroll</span>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-bold uppercase text-gray-500">
                                <span>Gaji Pokok</span>
                                <span class="text-gray-800 dark:text-white">72.5%</span>
                            </div>
                            <div class="h-1.5 w-full bg-gray-100 rounded-full dark:bg-white/5 overflow-hidden">
                                <div class="h-full bg-brand-500 rounded-full" style="width: 72.5%"></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-bold uppercase text-gray-500">
                                <span>Lembur & Tunjangan</span>
                                <span class="text-gray-800 dark:text-white">27.5%</span>
                            </div>
                            <div class="h-1.5 w-full bg-gray-100 rounded-full dark:bg-white/5 overflow-hidden">
                                <div class="h-full bg-orange-500 rounded-full" style="width: 27.5%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PHL Distribution -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="mb-6 flex items-center justify-between">
                        <h4 class="text-xs font-bold text-gray-800 dark:text-white uppercase tracking-wide">Komposisi Biaya PHL</h4>
                        <span class="text-[10px] font-bold text-brand-600 italic">18.5% dari Total Payroll</span>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-bold uppercase text-gray-500">
                                <span>Gaji Harian</span>
                                <span class="text-gray-800 dark:text-white">79.4%</span>
                            </div>
                            <div class="h-1.5 w-full bg-gray-100 rounded-full dark:bg-white/5 overflow-hidden">
                                <div class="h-full bg-brand-500 rounded-full" style="width: 79.4%"></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-bold uppercase text-gray-500">
                                <span>Lembur</span>
                                <span class="text-gray-800 dark:text-white">20.6%</span>
                            </div>
                            <div class="h-1.5 w-full bg-gray-100 rounded-full dark:bg-white/5 overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full" style="width: 20.6%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

