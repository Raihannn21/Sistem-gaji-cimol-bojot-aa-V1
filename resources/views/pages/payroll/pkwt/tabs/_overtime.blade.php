<!-- Tab: Overtime -->
<div x-show="activeTab === 'overtime'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" x-cloak>
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Rekapitulasi Lembur PKWT</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Daftar lembur yang telah diinput untuk periode ini (Karyawan Kontrak).</p>
        </div>
        @if($period->status !== 'Locked')
        <div class="flex items-center gap-3">
            <x-ui.button variant="outline" @click="showImportOvertimeModal = true" className="flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Excel
            </x-ui.button>
            <x-ui.button variant="primary" @click="showOvertimeModal = true">Input Lembur Baru</x-ui.button>
        </div>
        @endif
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Daftar Rekapitulasi Lembur PKWT</h3>
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
                                    'rate' => $o->hours > 0 ? (int)($o->amount / $o->hours) : 0,
                                    'amount' => (int) $o->amount,
                                    'note' => $o->note ?? '-',
                                ];
                            });
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] overtime-row"
                            x-show="!searchQuery || '{{ strtolower(addslashes($employee->name)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($employee->no_id)) }}'.includes(searchQuery.toLowerCase())">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-800 dark:text-white/90">{{ $employee->name }}</p>
                                <p class="text-xs text-gray-400">ID. {{ $employee->no_id }}</p>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-gray-800 dark:text-white/90">{{ $totalHours }} Jam</td>
                            <td class="px-6 py-4 text-right font-bold text-brand-600 tabular-nums whitespace-nowrap">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <button @click="showDetailModal = true; 
                                                selectedEmployee = { id: {{ $employee->id }}, name: '{{ addslashes($employee->name) }}', nrp: '{{ $employee->no_id }}' };
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
                    
                    <!-- Empty State for Search Results -->
                    <tr x-show="searchQuery && document.querySelectorAll('.overtime-row[style*=\'display: none\']').length === document.querySelectorAll('.overtime-row').length">
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">
                            Karyawan dengan nama atau ID tersebut tidak ditemukan di rekap lembur.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
