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
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-md">Slip gaji untuk periode ini belum diterbitkan. Silakan lengkapi seluruh data absensi, lembur, tunjangan risiko, dan tunjangan lainnya, lalu klik tombol <strong class="text-brand-600 dark:text-brand-400">"Generate Payroll"</strong> pada tab Overview untuk menerbitkan slip gaji.</p>
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
                               x-model="searchQuery" 
                               placeholder="Cari nama atau ID..." 
                               class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50/50 pr-4 text-xs text-gray-800 outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent dark:text-white dark:focus:border-brand-500 transition-colors"
                               style="padding-left: 2.75rem;">
                    </div>
                    <form action="{{ route('payroll.pkwt.periods.slip.send-all', $period->id) }}" 
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
                        @php
                            $startDate = \Carbon\Carbon::parse($period->start_date);
                            $endDate = \Carbon\Carbon::parse($period->end_date);
                            $totalPeriodDays = $startDate->diffInDays($endDate) + 1;
                        @endphp
                        @forelse($employees as $employee)
                            @php
                                $employeeAttendance = $period->attendances->where('employee_id', $employee->id)->first();
                                $resolvedTeam = ($period->status === 'Locked' && $employeeAttendance && $employeeAttendance->team_id)
                                    ? $employeeAttendance->team
                                    : $employee->team;
                                $teamName = $resolvedTeam ? $resolvedTeam->name : '-';
                                $resolvedTeamId = $resolvedTeam ? $resolvedTeam->id : $employee->team_id;

                                $periodTeam = $period->periodTeams->where('team_id', $resolvedTeamId)->first();
                                $offDates = $periodTeam ? ($periodTeam->off_dates ?? []) : [];

                                $daysWorked = $period->attendances->where('employee_id', $employee->id)
                                    ->filter(function ($att) use ($offDates) {
                                        if ($att->duration <= 0) {
                                            return false;
                                        }
                                        $dateStr = $att->date instanceof \Carbon\Carbon ? $att->date->format('Y-m-d') : $att->date;
                                        return !in_array($dateStr, $offDates);
                                    })
                                    ->count();
                                
                                $workDays = $periodTeam ? $periodTeam->work_days : ($totalPeriodDays ?: 1);

                                $daysAbsent = max(0, $workDays - $daysWorked);
                                
                                $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
                                $harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
                                $pokok = $daysWorked * $harian;
                                
                                $lembur = $period->overtimes->where('employee_id', $employee->id)->sum('amount');
                                $risiko = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
                                $lain_lain = $period->otherAllowances->where('employee_id', $employee->id)->sum('amount');
                                
                                $potongan = ($employee->bpjs_health ?? 0) + ($employee->bpjs_tk ?? 0) + ($employee->pph21 ?? 0);
                                $total = max(0, $pokok + $lembur + $risiko + $lain_lain - $potongan);
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] slip-row"
                                x-show="!searchQuery || '{{ strtolower(addslashes($employee->name)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($employee->no_id)) }}'.includes(searchQuery.toLowerCase())">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800 dark:text-white/90">{{ $employee->name }}</p>
                                    <p class="text-xs text-gray-400">ID. {{ $employee->no_id }}</p>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-brand-600 tabular-nums whitespace-nowrap">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-0.5 text-[10px] font-bold text-green-700 dark:bg-green-500/10 dark:text-green-500 uppercase tracking-wider">Published</span>
                                    <span class="text-[10px] text-gray-400 font-semibold block mt-0.5">{{ $daysWorked }} / {{ $workDays }} HK</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="showSlipModal = true; selectedSlip = { 
                                                    period_id: {{ $period->id }},
                                                    employee_id: {{ $employee->id }},
                                                    name: '{{ addslashes($employee->name) }}', 
                                                    nrp: '{{ $employee->no_id }}', 
                                                    days_worked: {{ $daysWorked }},
                                                    days_absent: {{ $daysAbsent }},
                                                    total_days: {{ $workDays }},
                                                    salary_monthly: {{ $employee->salary_monthly }},
                                                    gaji_full: {{ (int) $totalMonthly }},
                                                    tarif_harian: {{ (int) $harian }},
                                                    pokok: {{ (int) $pokok }}, 
                                                    lembur: {{ (int) $lembur }}, 
                                                    risiko: {{ (int) $risiko }}, 
                                                    lain_lain: {{ (int) $lain_lain }},
                                                    bpjs_health: {{ (int) ($employee->bpjs_health ?? 0) }},
                                                    bpjs_tk: {{ (int) ($employee->bpjs_tk ?? 0) }},
                                                    pph21: {{ (int) ($employee->pph21 ?? 0) }},
                                                    potongan_absen: {{ (int) ($daysAbsent * $harian) }},
                                                    potongan: {{ (int) ($potongan + $daysAbsent * $harian) }},
                                                    total: '{{ number_format($total, 0, ',', '.') }}',
                                                    type: 'pkwt',
                                                    period_title: '{{ $period->title }}',
                                                    team_name: '{{ addslashes($teamName) }}'
                                                }" 
                                                class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-brand-500 transition-colors"
                                                title="Lihat Slip Gaji">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <form action="{{ route('payroll.pkwt.periods.slip.send', [$period->id, $employee->id]) }}" 
                                              method="POST" 
                                              class="inline"
                                              @submit="emailSending = true; emailSendingEmployeeId = {{ $employee->id }}">
                                            @csrf
                                            <button type="submit" 
                                                    ::disabled="emailSending"
                                                    class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-brand-500 transition-colors flex items-center justify-center" 
                                                    title="Kirim ke Email">
                                                <template x-if="!(emailSending && emailSendingEmployeeId === {{ $employee->id }})">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                </template>
                                                <template x-if="emailSending && emailSendingEmployeeId === {{ $employee->id }}">
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
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                    Tidak ada data karyawan PKWT aktif.
                                </td>
                            </tr>
                        @endforelse
                        <tr x-show="searchQuery && Array.from(document.querySelectorAll('.slip-row')).every(el => el.style.display === 'none')" x-cloak>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                Tidak ada karyawan yang cocok dengan pencarian "<span x-text="searchQuery" class="font-bold"></span>".
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
