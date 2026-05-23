<!-- Tab: Overview -->
@php
    $employeesWithAttendance = $employees->filter(function ($emp) use ($period) {
        return $period->attendances->where('employee_id', $emp->id)->count() > 0;
    })->count();
    $readiness = $employees->count() > 0 ? round(($employeesWithAttendance / $employees->count()) * 100) : 0;

    $totalOvertimeAmount = $period->overtimes->sum('amount');
    $totalRiskAmount = $period->riskAllowances->sum('amount');
    $totalOthersAmount = $period->otherAllowances->sum('amount');
    $startDate = \Carbon\Carbon::parse($period->start_date);
    $endDate = \Carbon\Carbon::parse($period->end_date);
    $totalPeriodDays = $startDate->diffInDays($endDate) + 1;

    $totalBasicAmount = $employees->sum(function ($employee) use ($period, $totalPeriodDays) {
        $daysWorked = $period->attendances->where('employee_id', $employee->id)->count();
        $periodTeam = $period->periodTeams->where('team_id', $employee->team_id)->first();
        $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);
        $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
        $harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
        return $daysWorked * $harian;
    });

    $totalDeductions = $employees->sum(function ($emp) {
        return ($emp->bpjs_health ?? 0) + ($emp->bpjs_tk ?? 0) + ($emp->pph21 ?? 0);
    });

    $totalEstimation = max(0, $totalBasicAmount + $totalOvertimeAmount + $totalRiskAmount + $totalOthersAmount - $totalDeductions);
@endphp

<div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
    class="space-y-6" x-cloak>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div
            class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Karyawan PKWT</p>
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
                    <p class="text-sm text-gray-500 dark:text-gray-400">Estimasi Total Gaji</p>
                    <h4 class="mt-1 text-xl font-bold text-brand-600 dark:text-brand-500">Rp
                        {{ number_format($totalEstimation, 0, ',', '.') }}</h4>
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
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Rangkuman Penggajian PKWT (Pro-rata Kehadiran)</h3>
                @if($period->status === 'Locked')
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2 py-0.5 text-[10px] font-bold text-green-700 dark:bg-green-500/10 dark:text-green-500 uppercase tracking-wider">Locked</span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-50 px-2 py-0.5 text-[10px] font-bold text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-500 uppercase tracking-wider">Draft Mode</span>
                @endif
            </div>
            <div class="relative w-full sm:w-64 max-w-xs group">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 flex items-center justify-center pointer-events-none" style="left: 14px;">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Cari nama atau ID..." 
                       class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50/50 pr-4 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent dark:text-white dark:focus:border-brand-500 transition-colors"
                       style="padding-left: 2.75rem;">
            </div>
        </div>
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-left">
                            Karyawan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center">Hadir
                        </th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center">Absen
                        </th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-right">Tarif
                            Harian</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-right">Gaji
                            Pokok Didapat</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-right">Lembur
                        </th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-right">Risiko
                        </th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-right">
                            Lain-lain</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-right">
                            Potongan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-right">Total
                            Bersih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($employees as $employee)
                        @php
                            $daysWorked = $period->attendances->where('employee_id', $employee->id)->count();
                            
                            $periodTeam = $period->periodTeams->where('team_id', $employee->team_id)->first();
                            $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);

                            $daysAbsent = max(0, $workDays - $daysWorked);

                            $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
                            $harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
                            $pokok = $daysWorked * $harian;

                            $lembur = $period->overtimes->where('employee_id', $employee->id)->sum('amount');
                            $risiko = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
                            $tunjanganLain = $period->otherAllowances->where('employee_id', $employee->id)->sum('amount');

                            $potongan = ($employee->bpjs_health ?? 0) + ($employee->bpjs_tk ?? 0) + ($employee->pph21 ?? 0);
                            $total = max(0, $pokok + $lembur + $risiko + $tunjanganLain - $potongan);
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors overview-row"
                            x-show="!searchQuery || '{{ strtolower(addslashes($employee->name)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($employee->no_id)) }}'.includes(searchQuery.toLowerCase())">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-800 dark:text-white/90">{{ $employee->name }}</p>
                                <p class="text-xs text-gray-400">ID. {{ $employee->no_id }}</p>
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-center font-bold text-green-600 dark:text-green-400 tabular-nums">
                                {{ $daysWorked }} Hari
                            </td>
                            <td class="px-6 py-4 text-sm text-center font-bold text-red-500 dark:text-red-400 tabular-nums">
                                {{ $daysAbsent }} Hari
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600 dark:text-gray-400 tabular-nums whitespace-nowrap">
                                Rp {{ number_format($harian, 0, ',', '.') }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right text-gray-600 dark:text-gray-400 tabular-nums font-semibold whitespace-nowrap">
                                Rp {{ number_format($pokok, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600 dark:text-gray-400 tabular-nums whitespace-nowrap">
                                Rp {{ number_format($lembur, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600 dark:text-gray-400 tabular-nums whitespace-nowrap">
                                Rp {{ number_format($risiko, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600 dark:text-gray-400 tabular-nums whitespace-nowrap">
                                Rp {{ number_format($tunjanganLain, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-red-600 dark:text-red-500 tabular-nums whitespace-nowrap">
                                Rp {{ number_format($potongan, 0, ',', '.') }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right font-bold text-brand-600 dark:text-brand-500 tabular-nums whitespace-nowrap">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada karyawan PKWT yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                    
                    <!-- Empty State for Search Results -->
                    <tr x-show="searchQuery && document.querySelectorAll('.overview-row[style*=\'display: none\']').length === document.querySelectorAll('.overview-row').length">
                        <td colspan="10" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                            Karyawan dengan nama atau ID tersebut tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>