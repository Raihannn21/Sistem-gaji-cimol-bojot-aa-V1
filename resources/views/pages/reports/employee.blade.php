@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6" x-data="{ 
        searchQuery: '',
        selectedEmployee: null,
        employees: [
            { id: 1, name: 'Ahmad Fauzi', nrp: '1001', dept: 'Security', status: 'PHL', email: 'fauzi@example.com' },
            { id: 2, name: 'Budi Santoso', nrp: '2001', dept: 'Production', status: 'PKWT', email: 'budi@example.com' },
            { id: 3, name: 'Siti Aminah', nrp: '2002', dept: 'Production', status: 'PKWT', email: 'siti@example.com' }
        ]
    }">
        <x-common.page-breadcrumb :pageName="$title" />

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Sidebar: Search & List -->
            <div class="lg:col-span-1 space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight italic mb-6">Cari Karyawan</h3>
                    
                    <!-- Search Input -->
                    <div class="relative mb-6">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" x-model="searchQuery" placeholder="Nama atau NRP..." class="w-full rounded-xl border border-gray-100 bg-gray-50/50 py-3 pl-11 pr-4 text-xs font-bold text-gray-700 outline-none focus:border-brand-500 focus:bg-white dark:border-gray-800 dark:bg-white/[0.02] dark:text-white transition-all">
                    </div>

                    <!-- Employee List -->
                    <div class="space-y-2 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                        <template x-for="emp in employees.filter(e => e.name.toLowerCase().includes(searchQuery.toLowerCase()) || e.nrp.includes(searchQuery))" :key="emp.id">
                            <div @click="selectedEmployee = emp" 
                                 :class="selectedEmployee?.id === emp.id ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10' : 'border-gray-50 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.02]'"
                                 class="flex items-center gap-4 p-3 rounded-xl border transition-all cursor-pointer group">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 font-black text-gray-400 dark:bg-gray-800 group-hover:bg-brand-100 group-hover:text-brand-600 transition-colors" x-text="emp.name.charAt(0)"></div>
                                <div>
                                    <p class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-tight" x-text="emp.name"></p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest" x-text="'NRP. ' + emp.nrp + ' • ' + emp.status"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Main Content: Detailed Report -->
            <div class="lg:col-span-2">
                <template x-if="selectedEmployee">
                    <div class="space-y-6">
                        <!-- Employee Summary Header -->
                        <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-6">
                                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-brand-600 text-3xl font-black text-white italic shadow-xl shadow-brand-500/20" x-text="selectedEmployee.name.charAt(0)"></div>
                                    <div>
                                        <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight uppercase italic" x-text="selectedEmployee.name"></h2>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="rounded-full bg-brand-50 px-3 py-1 text-[10px] font-black text-brand-600 uppercase italic dark:bg-brand-500/10" x-text="selectedEmployee.dept"></span>
                                            <span class="rounded-full bg-blue-50 px-3 py-1 text-[10px] font-black text-blue-600 uppercase italic dark:bg-blue-500/10" x-text="selectedEmployee.status"></span>
                                            <span class="rounded-full bg-gray-100 px-3 py-1 text-[10px] font-black text-gray-500 uppercase italic dark:bg-gray-800" x-text="selectedEmployee.nrp"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <x-ui.button variant="outline" className="flex items-center gap-2 text-xs py-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        Print Bio
                                    </x-ui.button>
                                </div>
                            </div>
                        </div>

                        <!-- Payroll History -->
                        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight italic">Riwayat Penggajian (6 Bulan Terakhir)</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                                            <th class="px-6 py-4 text-xs font-black uppercase text-gray-500 tracking-widest">Periode</th>
                                            <th class="px-6 py-4 text-xs font-black uppercase text-gray-500 tracking-widest text-right">Kehadiran</th>
                                            <th class="px-6 py-4 text-xs font-black uppercase text-gray-500 tracking-widest text-right">Total Gaji</th>
                                            <th class="px-6 py-4 text-xs font-black uppercase text-gray-500 tracking-widest text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-medium text-sm">
                                        <template x-for="m in ['Juli', 'Juni', 'Mei', 'April', 'Maret', 'Februari']" :key="m">
                                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                                <td class="px-6 py-4 font-bold text-gray-800 dark:text-white/90" x-text="m + ' 2025'"></td>
                                                <td class="px-6 py-4 text-right tabular-nums">22 Hari</td>
                                                <td class="px-6 py-4 text-right font-black text-brand-600 tabular-nums">Rp 5.200.000</td>
                                                <td class="px-6 py-4 text-center">
                                                    <button class="text-gray-400 hover:text-brand-500 transition-colors">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="!selectedEmployee">
                    <div class="flex flex-col items-center justify-center h-[600px] rounded-2xl border-2 border-dashed border-gray-100 dark:border-gray-800">
                        <div class="h-24 w-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 dark:bg-white/5">
                            <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h3 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-tight italic">Pilih Karyawan</h3>
                        <p class="text-sm text-gray-400 font-medium mt-2">Silahkan pilih karyawan di sisi kiri untuk melihat laporan detail.</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; }
    </style>
@endsection
