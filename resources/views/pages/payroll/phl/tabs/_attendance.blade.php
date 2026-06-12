<!-- Tab: Attendance -->
<div x-show="activeTab === 'attendance'" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
    class="space-y-6" x-cloak>
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div
            class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Data Absensi Periode Ini</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola data kehadiran harian karyawan PHL.
                </p>
            </div>

            <div class="flex items-center gap-4">
                <!-- Input Pencarian -->
                <div class="relative w-64 max-w-xs group">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 flex items-center justify-center pointer-events-none" style="left: 14px;">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" 
                           x-model.debounce.150ms="searchQuery" 
                           placeholder="Cari nama atau ID..." 
                           class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50/50 pr-4 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent dark:text-white dark:focus:border-brand-500 transition-colors"
                           style="padding-left: 2.75rem;">
                </div>

                @if($period->status !== 'Locked')
                    <x-ui.button variant="outline" @click="showAttendanceImportModal = true"
                        className="flex items-center gap-2 text-xs py-2 h-10">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Import Excel
                    </x-ui.button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider">Karyawan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center">
                            Tanggal</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center">Scan Masuk</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center">Scan Pulang</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center">Terlambat</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center">Pulang Cepat</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center">Durasi</th>
                        @if($period->status !== 'Locked')
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <template x-for="item in paginatedAttendance()" :key="item.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400 font-bold text-sm" x-text="item.employee_name.charAt(0)">
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-white" x-text="item.employee_name"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'ID. ' + item.employee_no_id"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-700 dark:text-gray-300" x-text="item.date_formatted"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-200" x-text="item.scan_in || '-'"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-200" x-text="item.scan_out || '-'"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-semibold" :class="item.late_time && item.late_time !== '-' ? 'text-red-500 font-bold' : 'text-gray-400 dark:text-gray-500'" x-text="item.late_time || '-'"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-semibold" :class="item.early_time && item.early_time !== '-' ? 'text-yellow-600 dark:text-yellow-450 font-bold' : 'text-gray-400 dark:text-gray-500'" x-text="item.early_time || '-'"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold" :class="item.duration >= 8 ? 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400' : 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400'" x-text="item.duration + ' Jam'"></span>
                            </td>
                            @if($period->status !== 'Locked')
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Edit Button -->
                                        <button type="button" @click="selectedAttendance = {
                                                        id: item.id,
                                                        employee_name: item.employee_name,
                                                        date: item.date_formatted,
                                                        scan_in: item.scan_in,
                                                        scan_out: item.scan_out,
                                                        late_time: item.late_time,
                                                        early_time: item.early_time
                                                    }; showEditAttendanceModal = true;"
                                            class="p-1.5 text-gray-400 hover:text-brand-500 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800"
                                            title="Edit Kehadiran">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <button type="button" @click="$dispatch('open-delete-modal', { 
                                                        url: '/payroll/phl/periods/{{ $period->id }}/attendance/' + item.id,
                                                        message: 'Apakah Anda yakin ingin menghapus data absensi karyawan ' + item.employee_name + ' pada tanggal ' + item.date_formatted + '?'
                                                    })"
                                            class="p-1.5 text-gray-400 hover:text-red-500 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800"
                                            title="Hapus Kehadiran">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    </template>

                    <!-- Empty State (No records at all) -->
                    <tr x-show="attendanceList.length === 0">
                        <td colspan="{{ $period->status !== 'Locked' ? 8 : 7 }}" class="px-6 py-32 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 text-gray-300 dark:bg-white/5">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <h4 class="text-base font-bold text-gray-800 dark:text-white/90">Belum Ada Data Absensi</h4>
                                <p class="mx-auto mt-1 max-w-xs text-sm text-gray-500 dark:text-gray-400">Silakan import file Excel absensi untuk memproses data kehadiran.</p>
                            </div>
                        </td>
                    </tr>

                    <!-- Search Empty State -->
                    <tr x-show="attendanceList.length > 0 && filteredAttendance().length === 0">
                        <td colspan="{{ $period->status !== 'Locked' ? 8 : 7 }}" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                            Tidak ada karyawan dengan nama atau ID "<span x-text="searchQuery" class="font-bold"></span>" ditemukan dalam absensi.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer Controls -->
        <div x-show="attendanceList.length > 0" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            <div class="text-xs text-gray-500 dark:text-gray-400">
                Menampilkan <span class="font-bold text-gray-700 dark:text-white" x-text="filteredAttendance().length > 0 ? (attendancePage - 1) * attendancePerPage + 1 : 0"></span> 
                sampai <span class="font-bold text-gray-700 dark:text-white" x-text="Math.min(attendancePage * attendancePerPage, filteredAttendance().length)"></span> 
                dari <span class="font-bold text-gray-700 dark:text-white" x-text="filteredAttendance().length"></span> data
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-3">
                <button type="button" 
                        @click="attendancePage = Math.max(1, attendancePage - 1)" 
                        :disabled="attendancePage === 1"
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 hover:text-brand-500 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-800 dark:bg-transparent transition-colors shadow-theme-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                
                <span class="text-xs font-bold text-gray-600 dark:text-gray-400">
                    Halaman <span x-text="attendancePage"></span> dari <span x-text="attendanceTotalPages()"></span>
                </span>
                
                <button type="button" 
                        @click="attendancePage = Math.min(attendanceTotalPages(), attendancePage + 1)" 
                        :disabled="attendancePage === attendanceTotalPages()"
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 hover:text-brand-500 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-800 dark:bg-transparent transition-colors shadow-theme-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>