<!-- Tab: Slips -->
<div x-show="activeTab === 'slips'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" x-cloak>
    @if($period->status !== 'Locked')
        <!-- Premium Locked Placeholder -->
        <div class="flex flex-col items-center justify-center p-12 text-center bg-white dark:bg-white/[0.03] rounded-3xl border border-gray-200 dark:border-gray-800 shadow-sm min-h-[350px]">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-yellow-50 text-yellow-600 dark:bg-yellow-500/10 dark:text-yellow-500 mb-4 animate-pulse">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Payroll Belum Digenerate</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-md">Slip gaji untuk periode ini belum diterbitkan. Silakan lengkapi seluruh data absensi, lembur, dan tunjangan risiko karyawan, lalu klik tombol <strong class="text-brand-600 dark:text-brand-400">"Generate Payroll"</strong> pada tab Overview untuk menerbitkan slip gaji.</p>
        </div>
    @else
        <!-- Slips List Table -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Daftar Slip Gaji Terbit</h3>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
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
                    <form action="{{ route('payroll.phl.periods.slip.send-all', $period->id) }}" 
                          method="POST" 
                          class="w-full sm:w-auto" 
                          @submit="if(confirm('Apakah Anda yakin ingin mengirim email slip gaji ke seluruh karyawan aktif? Ini mungkin membutuhkan waktu beberapa saat.')) { emailSending = true; } else { $event.preventDefault(); }">
                        @csrf
                        <x-ui.button variant="outline" 
                                     type="submit" 
                                     ::disabled="emailSending"
                                     className="flex w-full justify-center items-center gap-2 text-xs py-2 px-4 text-brand-600 border-brand-100 hover:bg-brand-50 shrink-0">
                            <template x-if="!emailSending">
                                <span class="flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Kirim Semua Email
                                </span>
                            </template>
                            <template x-if="emailSending">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-brand-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Sedang mengirim...
                                </span>
                            </template>
                        </x-ui.button>
                    </form>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500">Karyawan</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 text-right">Gaji Bersih</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-medium text-sm">
                        <template x-for="item in paginatedSlips()" :key="item.employee_id">
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800 dark:text-white/90" x-text="item.employee_name"></p>
                                    <p class="text-xs text-gray-400" x-text="'ID. ' + item.employee_no_id"></p>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-brand-600 tabular-nums whitespace-nowrap" x-text="formatRupiah(item.total)"></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-0.5 text-[10px] font-bold text-green-700 dark:bg-green-500/10 dark:text-green-500 uppercase tracking-wider">Published</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="showSlipModal = true; selectedSlip = { 
                                                    period_id: {{ $period->id }},
                                                    employee_id: item.employee_id,
                                                    name: item.employee_name, 
                                                    nrp: item.employee_no_id, 
                                                    days_worked: item.days_worked,
                                                    salary_daily: item.salary_daily,
                                                    pokok: item.pokok, 
                                                    lembur: item.lembur, 
                                                    risiko: item.risiko, 
                                                    total: item.total,
                                                    type: 'phl',
                                                    period_title: '{{ $period->title }}',
                                                    team_name: item.team_name
                                                 }" 
                                                class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-brand-500 transition-colors"
                                                title="Lihat Slip Gaji">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <form :action="'/payroll/phl/periods/{{ $period->id }}/slip/send/' + item.employee_id" 
                                              method="POST" 
                                              class="inline"
                                              @submit="emailSending = true; emailSendingEmployeeId = item.employee_id">
                                            @csrf
                                            <button type="submit" 
                                                    :disabled="emailSending"
                                                    class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-brand-500 transition-colors flex items-center justify-center" 
                                                    title="Kirim ke Email">
                                                <template x-if="!(emailSending && emailSendingEmployeeId === item.employee_id)">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                </template>
                                                <template x-if="emailSending && emailSendingEmployeeId === item.employee_id">
                                                    <svg class="animate-spin h-5 w-5 text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </template>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>
 
                        <!-- Empty State (No records at all) -->
                        <tr x-show="slipsList.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                Tidak ada data karyawan PHL aktif.
                            </td>
                        </tr>
 
                        <!-- Search Empty State -->
                        <tr x-show="slipsList.length > 0 && filteredSlips().length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                Tidak ada karyawan yang cocok dengan pencarian "<span x-text="searchQuery" class="font-bold"></span>".
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
 
            <!-- Pagination Footer Controls -->
            <div x-show="slipsList.length > 0" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                    <div>
                        Menampilkan <span class="font-bold text-gray-700 dark:text-white" x-text="filteredSlips().length > 0 ? (slipsPage - 1) * slipsPerPage + 1 : 0"></span> 
                        sampai <span class="font-bold text-gray-700 dark:text-white" x-text="Math.min(slipsPage * slipsPerPage, filteredSlips().length)"></span> 
                        dari <span class="font-bold text-gray-700 dark:text-white" x-text="filteredSlips().length"></span> data
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span>Per halaman:</span>
                        <select x-model.number="slipsPerPage" @change="slipsPage = 1" class="h-8 rounded-lg border border-gray-200 bg-white px-2 py-0.5 text-xs font-semibold outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 transition-colors">
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
                            @click="slipsPage = Math.max(1, slipsPage - 1)" 
                            :disabled="slipsPage === 1"
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 hover:text-brand-500 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-800 dark:bg-transparent transition-colors shadow-theme-xs">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    
                    <span class="text-xs font-bold text-gray-600 dark:text-gray-400">
                        Halaman <span x-text="slipsPage"></span> dari <span x-text="slipsTotalPages()"></span>
                    </span>
                    
                    <button type="button" 
                            @click="slipsPage = Math.min(slipsTotalPages(), slipsPage + 1)" 
                            :disabled="slipsPage === slipsTotalPages()"
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 hover:text-brand-500 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-800 dark:bg-transparent transition-colors shadow-theme-xs">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
        </div>
    @endif
</div>
