<!-- Tab: Overtime -->
<div x-show="activeTab === 'overtime'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" x-cloak>
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Rekapitulasi Lembur PKWT</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Daftar lembur yang telah diinput untuk periode ini (Karyawan Kontrak).</p>
        </div>
        @if($period->status !== 'Locked')
        <x-ui.button variant="primary" @click="showOvertimeModal = true">Input Lembur Baru</x-ui.button>
        @endif
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500">Karyawan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 text-center">Total Jam</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 text-right">Nominal</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-medium text-sm">
                    @php
                        $groupedOvertimes = $period->overtimes->groupBy('employee_id');
                    @endphp
                    @forelse($groupedOvertimes as $employeeId => $overtimes)
                        @php
                            $employee = $overtimes->first()->employee;
                            if (!$employee) continue;
                            $totalHours = $overtimes->sum('hours');
                            $totalAmount = $overtimes->sum('amount');
                            
                            $detailItems = $overtimes->map(function($o) {
                                return [
                                    'id' => $o->id,
                                    'date' => $o->date->format('d-m-Y'),
                                    'raw_date' => $o->date->format('Y-m-d'),
                                    'hours' => $o->hours,
                                    'amount' => (int) $o->amount,
                                    'note' => $o->note ?? '-',
                                ];
                            });
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-800 dark:text-white/90">{{ $employee->name }}</p>
                                <p class="text-xs text-gray-400">NRP. {{ $employee->emp_no }}</p>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-gray-800 dark:text-white/90">{{ $totalHours }} Jam</td>
                            <td class="px-6 py-4 text-right font-bold text-brand-600 tabular-nums whitespace-nowrap">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <button @click="showDetailModal = true; 
                                                selectedEmployee = { id: {{ $employee->id }}, name: '{{ addslashes($employee->name) }}', nrp: '{{ $employee->emp_no }}' };
                                                selectedEmployeeOvertimes = @js($detailItems);" 
                                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-brand-500 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">
                                Belum ada data lembur diinput untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
