@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl" x-data="{ 
        showModal: false,
        periods: [
            { id: 1, month: 'Juli', year: '2025', status: 'Open', total_employees: 45, total_amount: 'Rp 210.500.000', created_at: '2025-07-01' },
            { id: 2, month: 'Juni', year: '2025', status: 'Locked', total_employees: 45, total_amount: 'Rp 208.750.000', created_at: '2025-06-01' },
            { id: 3, month: 'Mei', year: '2025', status: 'Locked', total_employees: 42, total_amount: 'Rp 195.200.000', created_at: '2025-05-01' }
        ]
    }">
        <div class="space-y-6">
            <!-- Header Actions -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Daftar Periode Gaji PKWT</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manajemen periode bulanan untuk penggajian karyawan PKWT (Kontrak).</p>
                </div>
                <x-ui.button variant="primary" @click="showModal = true" className="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buka Periode Baru
                </x-ui.button>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-500/10 text-brand-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Periode Aktif</p>
                            <h4 class="text-lg font-bold text-gray-800 dark:text-white">Juli 2025</h4>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-500/10 text-green-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Terbayar (PKWT YTD)</p>
                            <h4 class="text-lg font-bold text-gray-800 dark:text-white">Rp 1.450.000.000</h4>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-500/10 text-brand-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Rata-rata PKWT/Bulan</p>
                            <h4 class="text-lg font-bold text-gray-800 dark:text-white">44 Karyawan</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Periods Table -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Bulan & Tahun</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Jumlah Karyawan</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Pengeluaran</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <template x-for="period in periods" :key="period.id">
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-white/5">
                                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-800 dark:text-white" x-text="period.month + ' ' + period.year"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'Dibuat: ' + period.created_at"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium" 
                                              :class="period.status === 'Open' ? 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-500' : 'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-gray-400'"
                                              x-text="period.status"></span>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-400" x-text="period.total_employees + ' Karyawan'"></td>
                                    <td class="px-5 py-4 text-sm font-semibold text-gray-800 dark:text-white" x-text="period.total_amount"></td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a :href="'/payroll/pkwt/periods/' + period.id" class="p-2 text-gray-500 hover:bg-gray-100 hover:text-brand-500 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-white/5" title="Detail">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <template x-if="period.status === 'Open'">
                                                <button class="p-2 text-gray-500 hover:bg-gray-100 hover:text-red-500 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-white/5" title="Kunci Periode">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                </button>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Component -->
        <x-payroll.phl.period-modal />
    </div>
@endsection

