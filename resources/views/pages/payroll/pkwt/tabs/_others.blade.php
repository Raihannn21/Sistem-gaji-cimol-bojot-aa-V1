<!-- Tab: Others (Manual Allowances) -->
<div x-show="activeTab === 'others'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" x-cloak>
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Tunjangan Lain-lain</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Input manual untuk tunjangan khusus seperti THR, Bonus, atau insentif lainnya.</p>
        </div>
        @if($period->status !== 'Locked')
        <x-ui.button variant="primary" @click="showOthersModal = true" className="flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Input Tunjangan
        </x-ui.button>
        @endif
    </div>
    
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Daftar Tunjangan Lain-lain PKWT</h3>
            <div class="relative w-full sm:w-64 max-w-xs group">
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
        </div>
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider">Karyawan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider">Tunjangan Diberikan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-right">Total Nominal</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-medium text-sm">
                    <template x-for="item in paginatedOthers()" :key="item.employee_id">
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-800 dark:text-white/90" x-text="item.employee_name"></p>
                                <p class="text-xs text-gray-400" x-text="'ID. ' + item.employee_no_id"></p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex max-w-fit rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400" x-text="item.allowance_count + ' Tunjangan'">
                                    </span>
                                    <p class="text-xs text-gray-400 font-medium italic" x-text="item.types_list"></p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-brand-600 dark:text-brand-500 tabular-nums whitespace-nowrap" x-text="formatRupiah(item.total_amount)">
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="showOthersDetailModal = true; 
                                                selectedEmployee = { id: item.employee_id, name: item.employee_name, nrp: item.employee_no_id };
                                                selectedEmployeeOthers = item.detail_items;" 
                                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-brand-500 transition-colors"
                                        title="Lihat Detail Tunjangan">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                    
                    <!-- Empty State -->
                    <tr x-show="filteredOthers().length === 0">
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic" x-text="searchQuery ? 'Karyawan dengan nama atau ID tersebut tidak ditemukan di rekap tunjangan lain.' : 'Belum ada data tunjangan lain-lain diinput untuk periode ini.'">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer Controls -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                <div>
                    Menampilkan <span class="font-bold text-gray-700 dark:text-white" x-text="filteredOthers().length > 0 ? (othersPage - 1) * othersPerPage + 1 : 0"></span> 
                    sampai <span class="font-bold text-gray-700 dark:text-white" x-text="Math.min(othersPage * othersPerPage, filteredOthers().length)"></span> 
                    dari <span class="font-bold text-gray-700 dark:text-white" x-text="filteredOthers().length"></span> data
                </div>
                <div class="flex items-center gap-1.5">
                    <span>Per halaman:</span>
                    <select x-model.number="othersPerPage" @change="othersPage = 1" class="h-8 rounded-lg border border-gray-200 bg-white px-2 py-0.5 text-xs font-semibold outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 transition-colors">
                        <option value="5" class="dark:bg-gray-900">5</option>
                        <option value="10" class="dark:bg-gray-900">10</option>
                        <option value="15" class="dark:bg-gray-900">15</option>
                        <option value="25" class="dark:bg-gray-900">25</option>
                        <option value="50" class="dark:bg-gray-900">50</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-3">
                <button type="button" 
                        @click="othersPage = Math.max(1, othersPage - 1)" 
                        :disabled="othersPage === 1"
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 hover:text-brand-500 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-800 dark:bg-transparent transition-colors shadow-theme-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                
                <span class="text-xs font-bold text-gray-600 dark:text-gray-400">
                    Halaman <span x-text="othersPage"></span> dari <span x-text="othersTotalPages()"></span>
                </span>
                
                <button type="button" 
                        @click="othersPage = Math.min(othersTotalPages(), othersPage + 1)" 
                        :disabled="othersPage === othersTotalPages()"
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 hover:text-brand-500 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-800 dark:bg-transparent transition-colors shadow-theme-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>
