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
                <x-ui.button variant="outline" @click="alert('Seluruh email slip gaji karyawan periode {{ $period->title }} berhasil dijadwalkan untuk dikirim!')" className="flex items-center gap-2 text-xs py-2 px-4 text-brand-600 border-brand-100 hover:bg-brand-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Kirim Semua Email
                </x-ui.button>
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
                        @forelse($employees as $employee)
                            @php
                                $employeeAttendances = $period->attendances->where('employee_id', $employee->id);
                                $daysWorked = $employeeAttendances->where('duration', '>', 0)->count();
                                
                                $pokok = $daysWorked * $employee->salary_daily;
                                $lembur = $period->overtimes->where('employee_id', $employee->id)->sum('amount');
                                $risiko = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
                                $total = $pokok + $lembur + $risiko;
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800 dark:text-white/90">{{ $employee->name }}</p>
                                    <p class="text-xs text-gray-400">ID. {{ $employee->emp_no }}</p>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-brand-600 tabular-nums">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-0.5 text-[10px] font-bold text-green-700 dark:bg-green-500/10 dark:text-green-500 uppercase tracking-wider">Published</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="showSlipModal = true; selectedSlip = { 
                                                    period_id: {{ $period->id }},
                                                    employee_id: {{ $employee->id }},
                                                    name: '{{ $employee->name }}', 
                                                    nrp: '{{ $employee->emp_no }}', 
                                                    days_worked: {{ $daysWorked }},
                                                    salary_daily: {{ $employee->salary_daily }},
                                                    pokok: {{ $pokok }}, 
                                                    lembur: {{ $lembur }}, 
                                                    risiko: {{ $risiko }}, 
                                                    total: {{ $total }},
                                                    type: 'phl',
                                                    period_title: '{{ $period->title }}'
                                                }" 
                                                class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-brand-500 transition-colors"
                                                title="Lihat Slip Gaji">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <button @click="alert('Slip gaji karyawan {{ $employee->name }} berhasil dikirim ke email {{ $employee->email ?? 'karyawan@bojot.com' }}!')" 
                                                class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-brand-500 transition-colors" 
                                                title="Kirim ke Email">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                    Tidak ada data karyawan PHL aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
