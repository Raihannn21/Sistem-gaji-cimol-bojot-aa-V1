@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl" x-data="{ showImportModal: false }">
        <x-common.page-breadcrumb :pageName="$title" />

        <div class="space-y-6">
            <!-- Header Actions (Identical to Periods Template) -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Data Log Absensi</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Riwayat scan absensi harian karyawan PHL.</p>
                </div>
                <x-ui.button variant="primary" @click="showImportModal = true" className="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Excel
                </x-ui.button>
            </div>

            <!-- Table Card -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">KARYAWAN</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">TANGGAL</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">SCAN MASUK</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">SCAN PULANG</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">DURASI KERJA</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">KETERANGAN</th>
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
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-400">{{ now()->subDays($i)->format('d M Y') }}</td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-400 font-medium">08:00:12</td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-400 font-medium">17:05:45</td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-400">9 Jam 5 Menit</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-500/10 dark:text-green-500">Hadir</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button class="p-2 text-gray-500 hover:bg-gray-100 hover:text-brand-500 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-white/5" title="Edit">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button class="p-2 text-gray-500 hover:bg-gray-100 hover:text-red-500 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-white/5" title="Hapus">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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

        <!-- Modal Import (Standalone Component) -->
        <x-payroll.phl.import-modal />
    </div>
@endsection
