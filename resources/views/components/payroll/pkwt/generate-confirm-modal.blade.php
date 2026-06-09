@props(['period', 'employees'])
@php
    $totalOvertimeAmount = $period->overtimes->sum('amount');
    $totalRiskAmount = $period->riskAllowances->sum('amount');
    $totalOthersAmount = $period->otherAllowances->sum('amount');
    $startDate = \Carbon\Carbon::parse($period->start_date);
    $endDate = \Carbon\Carbon::parse($period->end_date);
    $totalPeriodDays = $startDate->diffInDays($endDate) + 1;

    $totalBasicAmount = $employees->sum(function ($employee) use ($period, $totalPeriodDays) {
        $periodTeam = $period->periodTeams->where('team_id', $employee->team_id)->first();
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
        $totalMonthly = ($employee->salary_monthly ?? 0) + ($employee->attendance_allowance ?? 0);
        $harian = $workDays > 0 ? ($totalMonthly / $workDays) : 0;
        return $daysWorked * $harian;
    });

    $totalDeductions = $employees->sum(function ($emp) {
        return ($emp->bpjs_health ?? 0) + ($emp->bpjs_tk ?? 0) + ($emp->pph21 ?? 0);
    });

    $totalEstimation = max(0, $totalBasicAmount + $totalOvertimeAmount + $totalRiskAmount + $totalOthersAmount - $totalDeductions);
@endphp
<template x-teleport="body">
    <div x-show="showConfirmModal" 
         class="modal fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5" 
         style="display: none;"
         x-cloak>
        
        <!-- Backdrop (Exact copy of other modal backdrops with backdrop-blur-sm) -->
        <div @click="!processing && (showConfirmModal = false)" class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        </div>

        <!-- Modal Content Box (Exact copy of other modals with max-w-[700px] sizing) -->
        <div @click.stop class="relative w-full rounded-3xl bg-white dark:bg-gray-900 max-w-[700px] shadow-2xl border border-gray-100 dark:border-gray-800"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95">
            
            <!-- Inner Wrapper (Exact copy of other modal inner wrappers for perfect styling) -->
            <div class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
                
                <!-- Close Button -->
                <button @click="showConfirmModal = false" :disabled="processing" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 z-50 transition-colors duration-150">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
                </button>

                <!-- Header (Borderless & Spaced, matching import-modal and period-modal) -->
                <div class="px-2 pr-14 mb-6">
                    <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Konfirmasi Generate Gaji</h4>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Anda akan memproses gaji untuk <span class="font-bold text-gray-700 dark:text-gray-200">{{ $employees->count() }} Karyawan</span> periode <span class="font-bold text-gray-700 dark:text-gray-200">{{ $period->title }}</span>.
                    </p>
                </div>

                <!-- Form Content Area -->
                <div class="custom-scrollbar max-h-[458px] overflow-y-auto p-2 space-y-6">
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800/50 dark:bg-white/[0.03]">
                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-800">
                            <span class="text-sm font-medium text-gray-500">Estimasi Total Pengeluaran:</span>
                            <span class="text-sm font-bold text-gray-800 dark:text-white">Rp {{ number_format($totalEstimation, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-3 flex items-start gap-2.5">
                            <svg class="mt-0.5 h-5 w-5 text-yellow-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-xs text-yellow-600 dark:text-yellow-500/80 leading-relaxed italic">Pastikan seluruh data lembur, kehadiran, dan tunjangan telah divalidasi sebelum melanjutkan. Slip gaji akan otomatis dibuat setelah proses ini.</p>
                        </div>
                    </div>
                </div>

                <!-- Footer (Borderless Border-t Spacing aligned to the right) -->
                <div class="flex items-center justify-end gap-3 px-2 mt-6 border-t border-gray-100 dark:border-gray-800 pt-5">
                    <button type="button" @click="showConfirmModal = false" :disabled="processing" class="flex justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] transition-colors duration-150">
                        Batalkan
                    </button>
                    <button type="button" @click="processing = true; document.getElementById('generate-pkwt-form').submit();" :disabled="processing" class="flex justify-center rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 shadow-lg shadow-brand-500/10 transition-all duration-150">
                        <template x-if="processing">
                            <svg class="h-4 w-4 animate-spin text-white mr-2 inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="processing ? 'Memproses...' : 'Ya, Generate Gaji'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<form id="generate-pkwt-form" action="{{ route('payroll.pkwt.periods.generate', $period->id) }}" method="POST" class="hidden">
    @csrf
</form>