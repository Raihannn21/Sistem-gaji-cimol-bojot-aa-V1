@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl" x-data="{ 
        showModal: false, 
        showDetailModal: false,
        showEditRiskModal: false,
        selectedEmployee: {},
        selectedRiskDate: '',
        selectedRiskAmount: 0,
        selectedRiskNote: ''
    }">
        <div class="space-y-6">
            <!-- Header Actions (Standardized Title & Description) -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Tunjangan Risiko PHL</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manajemen pemberian tunjangan risiko untuk pekerjaan lapangan tertentu.</p>
                </div>
                <x-ui.button variant="primary" @click="showModal = true" className="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Input Tunjangan Baru
                </x-ui.button>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Penerima</p>
                    <h4 class="mt-2 text-xl font-bold text-gray-800 dark:text-white">32 Karyawan</h4>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Tunjangan</p>
                    <h4 class="mt-2 text-xl font-bold text-gray-800 dark:text-white">Rp 12.800.000</h4>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Periode Aktif</p>
                    <h4 class="mt-2 text-xl font-bold text-gray-800 dark:text-white">Juli 2025</h4>
                </div>
            </div>

            <!-- Table Card -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">KARYAWAN</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-center">TOTAL HARI</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">TOTAL TUNJANGAN</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @for ($i = 1; $i <= 5; $i++)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-white/5">
                                            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 dark:text-white">{{ ['Ahmad Fauzi', 'Budi Santoso', 'Citra Lestari', 'Dedi Kurniawan', 'Eka Putri'][$i-1] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">NRP. 100{{ $i }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-center font-bold text-gray-800 dark:text-white">10 Hari</td>
                                <td class="px-5 py-4 text-sm text-right font-bold text-brand-600 dark:text-brand-500">Rp 500.000</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center">
                                        <button @click="showDetailModal = true; selectedEmployee = { name: '{{ ['Ahmad Fauzi', 'Budi Santoso', 'Citra Lestari', 'Dedi Kurniawan', 'Eka Putri'][$i-1] }}', nrp: '100{{ $i }}' }" 
                                                class="p-2 text-gray-500 hover:bg-gray-100 hover:text-brand-500 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-white/5" title="Detail Tunjangan">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Components -->
        <x-payroll.phl.risk-allowance-modal />
        <x-payroll.phl.risk-allowance-detail-modal />
        <x-payroll.phl.risk-allowance-edit-modal />
    </div>
@endsection
