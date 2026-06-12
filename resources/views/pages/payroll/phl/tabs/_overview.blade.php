<!-- Tab: Overview -->
@php
    $employeesWithAttendance = $employees->filter(function ($emp) use ($period) {
        return $period->attendances->where('employee_id', $emp->id)->count() > 0;
    })->count();
    $readiness = $employees->count() > 0 ? round(($employeesWithAttendance / $employees->count()) * 100) : 0;

    $totalOvertimeAmount = $period->overtimes->sum('amount');
    $totalRiskAmount = $period->riskAllowances->sum('amount');
    $totalEstimation = $employees->sum(function ($employee) use ($period) {
        $employeeAttendances = $period->attendances->where('employee_id', $employee->id);
        $daysWorked = $employeeAttendances->where('duration', '>', 0)->count();
        return $daysWorked * $employee->salary_daily;
    }) + $totalOvertimeAmount + $totalRiskAmount;
@endphp
<div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
    class="space-y-6" x-cloak>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div
            class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Karyawan</p>
                    <h4 class="mt-1 text-xl font-bold text-gray-800 dark:text-white/90">{{ $employees->count() }} <span
                            class="text-xs font-medium text-gray-400">Orang</span></h4>
                </div>
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div
            class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kesiapan Data</p>
                    <h4 class="mt-1 text-xl font-bold text-gray-800 dark:text-white/90">{{ $readiness }}% <span
                            class="text-xs font-medium {{ $readiness == 100 ? 'text-green-500' : 'text-yellow-500' }}">{{ $readiness == 100 ? 'Ready' : 'Pending' }}</span>
                    </h4>
                </div>
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div
            class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Estimasi Gaji</p>
                    <h4 class="mt-1 text-xl font-bold text-brand-600 dark:text-brand-500">Rp
                        {{ number_format($totalEstimation, 0, ',', '.') }}
                    </h4>
                </div>
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div
            class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Rangkuman Kalkulasi Gaji</h3>
                @if($period->status === 'Locked')
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2 py-0.5 text-[10px] font-bold text-green-700 dark:bg-green-500/10 dark:text-green-500 uppercase tracking-wider">Locked</span>
                @else
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full bg-yellow-50 px-2 py-0.5 text-[10px] font-bold text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-500 uppercase tracking-wider">Draft</span>
                @endif
            </div>
            <div class="relative w-full sm:w-64 max-w-xs group">
                <span
                    class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 flex items-center justify-center pointer-events-none"
                    style="left: 14px;">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" x-model.debounce.150ms="searchQuery" placeholder="Cari nama atau ID..."
                    class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50/50 pr-4 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent dark:text-white dark:focus:border-brand-500 transition-colors"
                    style="padding-left: 2.75rem;">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-left"
                            style="text-align: left;">Karyawan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center"
                            style="text-align: center;">Total Masuk</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center"
                            style="text-align: center;">Pokok</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center"
                            style="text-align: center;">Lembur</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center"
                            style="text-align: center;">Risiko</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-right"
                            style="text-align: right;">Total Bersih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <template x-for="item in paginatedOverview()" :key="item.id">
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                            <td class="px-6 py-4" style="text-align: left;">
                                <p class="text-sm font-bold text-gray-800 dark:text-white/90" x-text="item.name"></p>
                                <p class="text-xs text-gray-400" x-text="'ID. ' + item.no_id"></p>
                            </td>
                            <td class="px-6 py-4 text-sm text-center font-bold text-gray-700 dark:text-gray-300"
                                style="text-align: center;" x-text="item.days_worked + ' Hari'">
                            </td>
                            <td class="px-6 py-4 text-sm text-center text-gray-600 dark:text-gray-400 tabular-nums whitespace-nowrap"
                                style="text-align: center;" x-text="formatRupiah(item.pokok)">
                            </td>
                            <td class="px-6 py-4 text-sm text-center text-gray-600 dark:text-gray-400 tabular-nums whitespace-nowrap"
                                style="text-align: center;" x-text="formatRupiah(item.lembur)">
                            </td>
                            <td class="px-6 py-4 text-sm text-center text-gray-600 dark:text-gray-400 tabular-nums whitespace-nowrap"
                                style="text-align: center;" x-text="formatRupiah(item.risiko)">
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-brand-600 dark:text-brand-500 tabular-nums whitespace-nowrap"
                                style="text-align: right;" x-text="formatRupiah(item.total)">
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <tr x-show="filteredOverview().length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                            Karyawan dengan nama atau ID tersebut tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer Controls -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                <div>
                    Menampilkan <span class="font-bold text-gray-700 dark:text-white" x-text="filteredOverview().length > 0 ? (overviewPage - 1) * overviewPerPage + 1 : 0"></span> 
                    sampai <span class="font-bold text-gray-700 dark:text-white" x-text="Math.min(overviewPage * overviewPerPage, filteredOverview().length)"></span> 
                    dari <span class="font-bold text-gray-700 dark:text-white" x-text="filteredOverview().length"></span> data
                </div>
                <div class="flex items-center gap-1.5">
                    <span>Per halaman:</span>
                    <select x-model.number="overviewPerPage" @change="overviewPage = 1" class="h-8 rounded-lg border border-gray-200 bg-white px-2 py-0.5 text-xs font-semibold outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 transition-colors">
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
                        @click="overviewPage = Math.max(1, overviewPage - 1)" 
                        :disabled="overviewPage === 1"
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 hover:text-brand-500 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-800 dark:bg-transparent transition-colors shadow-theme-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                
                <span class="text-xs font-bold text-gray-600 dark:text-gray-400">
                    Halaman <span x-text="overviewPage"></span> dari <span x-text="overviewTotalPages()"></span>
                </span>
                
                <button type="button" 
                        @click="overviewPage = Math.min(overviewTotalPages(), overviewPage + 1)" 
                        :disabled="overviewPage === overviewTotalPages()"
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 hover:text-brand-500 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-800 dark:bg-transparent transition-colors shadow-theme-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>