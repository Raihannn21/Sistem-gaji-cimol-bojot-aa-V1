@extends('layouts.app')

@php
    // Pre-calculate overview items for Alpine.js client-side pagination
    $overviewData = $employees->map(function ($employee) use ($period) {
        $employeeAttendances = $period->attendances->where('employee_id', $employee->id);
        $daysWorked = $employeeAttendances->where('duration', '>', 0)->count();

        $pokok = $daysWorked * $employee->salary_daily;
        $lembur = $period->overtimes->where('employee_id', $employee->id)->sum('amount');
        $risiko = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
        $total = $pokok + $lembur + $risiko;

        return [
            'id' => $employee->id,
            'name' => $employee->name,
            'no_id' => $employee->no_id,
            'days_worked' => $daysWorked,
            'pokok' => (int) $pokok,
            'lembur' => (int) $lembur,
            'risiko' => (int) $risiko,
            'total' => (int) $total,
        ];
    })->values();

    // Pre-calculate attendances for Alpine.js client-side pagination
    $attendanceData = $period->attendances->map(function ($attendance) {
        return [
            'id' => $attendance->id,
            'employee_name' => $attendance->employee->name ?? 'Unknown',
            'employee_no_id' => $attendance->employee->no_id ?? '-',
            'date' => \Carbon\Carbon::parse($attendance->date)->format('Y-m-d'),
            'date_formatted' => \Carbon\Carbon::parse($attendance->date)->format('d M Y'),
            'scan_in' => $attendance->scan_in ? \Carbon\Carbon::parse($attendance->scan_in)->format('H:i') : '',
            'scan_out' => $attendance->scan_out ? \Carbon\Carbon::parse($attendance->scan_out)->format('H:i') : '',
            'late_time' => $attendance->late_time ?: '',
            'early_time' => $attendance->early_time ?: '',
            'duration' => (float) $attendance->duration,
        ];
    })->values();

    // Pre-calculate overtimes for Alpine.js client-side pagination
    $overtimesData = $period->overtimes->groupBy('employee_id')->map(function ($overtimes) {
        $first = $overtimes->first();
        $employee = $first ? $first->employee : null;
        if (!$employee) return null;
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

        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'employee_no_id' => $employee->no_id,
            'total_hours' => $totalHours,
            'total_amount' => $totalAmount,
            'detail_items' => $detailItems
        ];
    })->filter()->values();

    // Pre-calculate risks for Alpine.js client-side pagination
    $risksData = $period->riskAllowances->groupBy('employee_id')->map(function ($risks) {
        $first = $risks->first();
        $employee = $first ? $first->employee : null;
        if (!$employee) return null;
        $daysCount = $risks->count();
        $totalAmount = $risks->sum('amount');
        
        $detailItems = $risks->map(function($r) {
            return [
                'id' => $r->id,
                'date' => $r->date->format('d-m-Y'),
                'raw_date' => $r->date->format('Y-m-d'),
                'amount' => (int) $r->amount,
                'note' => $r->note ?? '-',
            ];
        });

        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'employee_no_id' => $employee->no_id,
            'days_count' => $daysCount,
            'total_amount' => $totalAmount,
            'detail_items' => $detailItems
        ];
    })->filter()->values();

    // Pre-calculate slips for Alpine.js client-side pagination
    $slipsData = $employees->map(function ($employee) use ($period) {
        $employeeAttendances = $period->attendances->where('employee_id', $employee->id);
        $daysWorked = $employeeAttendances->where('duration', '>', 0)->count();
        
        $employeeAttendance = $period->attendances->where('employee_id', $employee->id)->first();
        $resolvedTeam = ($period->status === 'Locked' && $employeeAttendance && $employeeAttendance->team_id)
            ? $employeeAttendance->team
            : $employee->team;
        $teamName = $resolvedTeam ? $resolvedTeam->name : '-';

        $pokok = $daysWorked * $employee->salary_daily;
        $lembur = $period->overtimes->where('employee_id', $employee->id)->sum('amount');
        $risiko = $period->riskAllowances->where('employee_id', $employee->id)->sum('amount');
        $total = $pokok + $lembur + $risiko;

        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'employee_no_id' => $employee->no_id,
            'days_worked' => $daysWorked,
            'salary_daily' => (int) $employee->salary_daily,
            'pokok' => (int) $pokok,
            'lembur' => (int) $lembur,
            'risiko' => (int) $risiko,
            'total' => (int) $total,
            'team_name' => $teamName,
        ];
    })->values();
@endphp

@section('content')
    <div class="mx-auto max-w-screen-2xl" x-data="{ 
        activeTab: '{{ request()->query('tab', 'overview') }}',
        // Global Modal States
        showOvertimeModal: false,
        showRiskModal: false,
        showOvertimeDetailModal: false,
        showRiskDetailModal: false,
        showEditOvertimeModal: false,
        showEditRiskModal: false,
        showSlipModal: false,
        showConfirmModal: false,
        showUnlockModal: false,
        showAttendanceImportModal: false,
        showEditAttendanceModal: false,
        selectedAttendance: { id: null, employee_name: '', date: '', scan_in: '', scan_out: '', late_time: '', early_time: '' },
        searchQuery: '',
        selectedEmployee: {},
        selectedEmployeeOvertimes: [],
        selectedEmployeeRisks: [],
        selectedOvertimeId: null,
        selectedRiskId: null,
        selectedOvertimeDate: '',
        selectedOvertimeDateFormatted: '',
        selectedOvertimeHours: 0,
        selectedOvertimeRate: 0,
        selectedOvertimeAmount: 0,
        selectedOvertimeNote: '',
        selectedRiskDate: '',
        selectedRiskAmount: 0,
        selectedRiskNote: '',
        showImportOvertimeModal: false,
        showImportRiskModal: false,
        formatCurrency(el) {
            let val = el.value.replace(/\D/g, '');
            if (val === '') {
                el.value = '';
                return;
            }
            el.value = new Intl.NumberFormat('id-ID').format(val);
        },
        selectedSlip: {},
        processing: false,
        emailSending: false,
        emailSendingEmployeeId: null,
        errors: {},
        
        // Overview pagination
        overviewPage: 1,
        overviewPerPage: 15,
        overviewList: @js($overviewData),
        filteredOverview() {
            let q = this.searchQuery.toLowerCase();
            return this.overviewList.filter(item => 
                !q || 
                item.name.toLowerCase().includes(q) || 
                item.no_id.toLowerCase().includes(q)
            );
        },
        paginatedOverview() {
            let start = (this.overviewPage - 1) * this.overviewPerPage;
            return this.filteredOverview().slice(start, start + this.overviewPerPage);
        },
        overviewTotalPages() {
            return Math.ceil(this.filteredOverview().length / this.overviewPerPage) || 1;
        },

        // Attendance pagination
        attendancePage: 1,
        attendancePerPage: 15,
        attendanceList: @js($attendanceData),
        filteredAttendance() {
            let q = this.searchQuery.toLowerCase();
            return this.attendanceList.filter(item => 
                !q || 
                item.employee_name.toLowerCase().includes(q) || 
                item.employee_no_id.toLowerCase().includes(q)
            );
        },
        paginatedAttendance() {
            let start = (this.attendancePage - 1) * this.attendancePerPage;
            return this.filteredAttendance().slice(start, start + this.attendancePerPage);
        },
        attendanceTotalPages() {
            return Math.ceil(this.filteredAttendance().length / this.attendancePerPage) || 1;
        },

        // Overtime pagination
        overtimePage: 1,
        overtimePerPage: 15,
        overtimeList: @js($overtimesData),
        filteredOvertime() {
            let q = this.searchQuery.toLowerCase();
            return this.overtimeList.filter(item => 
                !q || 
                item.employee_name.toLowerCase().includes(q) || 
                item.employee_no_id.toLowerCase().includes(q)
            );
        },
        paginatedOvertime() {
            let start = (this.overtimePage - 1) * this.overtimePerPage;
            return this.filteredOvertime().slice(start, start + this.overtimePerPage);
        },
        overtimeTotalPages() {
            return Math.ceil(this.filteredOvertime().length / this.overtimePerPage) || 1;
        },

        // Risk pagination
        riskPage: 1,
        riskPerPage: 15,
        riskList: @js($risksData),
        filteredRisk() {
            let q = this.searchQuery.toLowerCase();
            return this.riskList.filter(item => 
                !q || 
                item.employee_name.toLowerCase().includes(q) || 
                item.employee_no_id.toLowerCase().includes(q)
            );
        },
        paginatedRisk() {
            let start = (this.riskPage - 1) * this.riskPerPage;
            return this.filteredRisk().slice(start, start + this.riskPerPage);
        },
        riskTotalPages() {
            return Math.ceil(this.filteredRisk().length / this.riskPerPage) || 1;
        },

        // Slips pagination
        slipsPage: 1,
        slipsPerPage: 15,
        slipsList: @js($slipsData),
        filteredSlips() {
            let q = this.searchQuery.toLowerCase();
            return this.slipsList.filter(item => 
                !q || 
                item.employee_name.toLowerCase().includes(q) || 
                item.employee_no_id.toLowerCase().includes(q)
            );
        },
        paginatedSlips() {
            let start = (this.slipsPage - 1) * this.slipsPerPage;
            return this.filteredSlips().slice(start, start + this.slipsPerPage);
        },
        slipsTotalPages() {
            return Math.ceil(this.filteredSlips().length / this.slipsPerPage) || 1;
        },

        formatRupiah(val) {
            if (val === null || val === '') return 'Rp 0';
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
        },

        init() {
            this.$watch('searchQuery', value => {
                this.overviewPage = 1;
                this.attendancePage = 1;
                this.overtimePage = 1;
                this.riskPage = 1;
                this.slipsPage = 1;
            });
            this.$watch('activeTab', value => {
                this.overviewPage = 1;
                this.attendancePage = 1;
                this.overtimePage = 1;
                this.riskPage = 1;
                this.slipsPage = 1;
            });
        },

        generate() {
            this.processing = true;
            setTimeout(() => {
                this.processing = false;
                this.showConfirmModal = false;
                alert('Payroll berhasil digenerate!');
            }, 2000);
        }
    }">
        <div class="space-y-6">
            <!-- Header Actions (Standardized with Template DNA) -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ url('/payroll/phl/periods') }}" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-brand-500 transition-colors dark:bg-white/[0.03] dark:border-gray-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">{{ $period->title }}</h2>
                            <span class="rounded-full {{ $period->status === 'Open' ? 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500' : 'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-gray-400' }} px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                                {{ $period->status === 'Open' ? 'Aktif' : $period->status }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 flex flex-wrap items-center gap-1.5">
                            <span>Pusat kendali penggajian PHL (Security, Driver, & Helper).</span>
                            <span class="text-gray-300 dark:text-gray-750 font-normal hidden sm:inline">|</span>
                            <span class="inline-flex items-center gap-1 font-bold text-brand-600 dark:text-brand-400 text-xs bg-brand-50/50 dark:bg-brand-500/10 px-2 py-0.5 rounded-lg border border-brand-100/50 dark:border-brand-500/20">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $period->start_date->format('d M Y') }} s/d {{ $period->end_date->format('d M Y') }}
                            </span>
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="relative" x-data="{ showExportDropdown: false }" @click.away="showExportDropdown = false">
                        <x-ui.button variant="outline" @click="showExportDropdown = !showExportDropdown" className="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Export Report
                        </x-ui.button>
                        
                        <div x-show="showExportDropdown" x-transition x-cloak class="absolute right-0 mt-2 w-48 rounded-xl border border-gray-100 bg-white p-2 shadow-xl dark:border-gray-800 dark:bg-gray-900 z-50">
                            <a href="{{ route('payroll.phl.periods.export.pdf', $period->id) }}" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-xs font-bold text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Export to PDF
                            </a>
                            <a href="{{ route('payroll.phl.periods.export.excel', $period->id) }}" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-xs font-bold text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export to Excel
                            </a>
                        </div>
                    </div>
                    
                    <x-ui.button variant="outline" @click="if('{{ $period->status }}' === 'Locked') { window.location.href = '{{ route('payroll.phl.periods.export.bca', $period->id) }}'; } else { showToast('Export BCA hanya dapat diunduh setelah payroll periode ini digenerate!', 'error'); }" className="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export BCA
                    </x-ui.button>

                    @if($period->status === 'Locked')
                    <x-ui.button variant="outline" @click="showUnlockModal = true" className="flex items-center gap-2 text-red-650 border-red-200 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:border-red-900/30 dark:hover:bg-red-500/10">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                        Buka Kunci Payroll
                    </x-ui.button>
                    @else
                    <x-ui.button variant="primary" @click="showConfirmModal = true" x-show="activeTab === 'overview'">
                        Generate Payroll
                    </x-ui.button>
                    @endif
                </div>
            </div>

            <!-- Tab Navigation (Standardized Subtle Style) -->
            <div class="flex items-center gap-1 overflow-x-auto no-scrollbar rounded-2xl border border-gray-200 bg-white p-1.5 dark:border-gray-800 dark:bg-white/[0.03] shadow-sm">
                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-gray-100 text-brand-600 dark:bg-white/10 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="flex flex-1 items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-bold transition-all whitespace-nowrap px-4">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Overview
                </button>
                <button @click="activeTab = 'attendance'" :class="activeTab === 'attendance' ? 'bg-gray-100 text-brand-600 dark:bg-white/10 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="flex flex-1 items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-bold transition-all whitespace-nowrap px-4">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Absensi
                </button>
                <button @click="activeTab = 'overtime'" :class="activeTab === 'overtime' ? 'bg-gray-100 text-brand-600 dark:bg-white/10 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="flex flex-1 items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-bold transition-all whitespace-nowrap px-4">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Lembur
                </button>
                <button @click="activeTab = 'risk'" :class="activeTab === 'risk' ? 'bg-gray-100 text-brand-600 dark:bg-white/10 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="flex flex-1 items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-bold transition-all whitespace-nowrap px-4">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Risiko
                </button>
                <button @click="activeTab = 'slips'" :class="activeTab === 'slips' ? 'bg-gray-100 text-brand-600 dark:bg-white/10 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="flex flex-1 items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-bold transition-all whitespace-nowrap px-4">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Slip Gaji
                </button>
            </div>

            <!-- Tab Content Wrapper (Standard Spacing) -->
            <div class="min-h-[400px]">
                @include('pages.payroll.phl.tabs._overview')
                @include('pages.payroll.phl.tabs._attendance')
                @include('pages.payroll.phl.tabs._overtime')
                @include('pages.payroll.phl.tabs._risk')
                @include('pages.payroll.phl.tabs._slips')
            </div>

            <!-- Modal Components (Consolidated) -->
            <x-payroll.phl.overtime-modal :period="$period" :employees="$employees" />
            <x-payroll.phl.overtime-detail-modal :period="$period" />
            <x-payroll.phl.overtime-edit-modal :period="$period" />
            <x-payroll.phl.risk-allowance-modal :period="$period" :employees="$employees" />
            <x-payroll.phl.risk-allowance-detail-modal :period="$period" />
            <x-payroll.phl.risk-allowance-edit-modal :period="$period" />
            <x-payroll.phl.generate-confirm-modal :period="$period" :employees="$employees" />
            <x-payroll.phl.payslip-modal />

            <!-- Modal: Konfirmasi Buka Kunci Gaji -->
            <template x-teleport="body">
                <div x-show="showUnlockModal" 
                     class="fixed inset-0 flex items-center justify-center p-4" 
                     style="z-index: 99999999; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;"
                     x-cloak>
                    
                    <!-- Backdrop -->
                    <div x-show="showUnlockModal" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0"
                         style="position: fixed; inset: 0; background: rgba(156, 163, 175, 0.5); backdrop-filter: blur(4px);"
                         @click="showUnlockModal = false"></div>

                    <!-- Modal Content Box -->
                    <div x-show="showUnlockModal"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                         class="relative bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 overflow-hidden"
                         style="width: 500px; max-width: 100%; border-radius: 32px; border: 1px solid rgba(0,0,0,0.05);"
                         @click.away="showUnlockModal = false">
                        
                        <!-- Close Button -->
                        <button @click="showUnlockModal = false"
                            style="position: absolute; right: 20px; top: 20px; width: 40px; height: 40px; background: #f3f4f6; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #9ca3af; transition: all 0.3s;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z"
                                    fill="currentColor" />
                            </svg>
                        </button>

                        <!-- Icon & Header -->
                        <div style="display: flex; flex-direction: column;">
                            <div style="width: 56px; height: 56px; background: #fef2f2; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                                <svg style="width: 28px; height: 28px; color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 style="margin: 0; font-size: 20px; font-weight: 700; color: #1f2937;">Konfirmasi Buka Kunci Gaji</h3>
                            <p style="margin: 8px 0 0 0; font-size: 14px; color: #6b7280; line-height: 1.5;">Apakah Anda yakin ingin membuka kunci periode payroll ini? Hal ini memungkinkan data absensi, lembur, dan tunjangan diubah kembali.</p>
                        </div>

                        <!-- Footer Buttons -->
                        <div style="display: flex; gap: 12px; margin-top: 32px;">
                            <button type="button" @click="showUnlockModal = false" 
                                    style="flex: 1; padding: 12px; background: white; border: 1px solid #e5e7eb; border-radius: 16px; color: #374151; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                                    onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                Batalkan
                            </button>
                            
                            <form action="{{ route('payroll.phl.periods.unlock', $period->id) }}" method="POST" style="flex: 1; margin: 0;">
                                @csrf
                                <button type="submit" 
                                        style="width: 100%; padding: 12px; background: #dc2626; border: none; border-radius: 16px; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                                        onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                                    Ya, Buka Kunci
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <!-- Modal: Import Absensi -->
        <x-payroll.phl.attendance-import-modal :period="$period" />
        
        <!-- Modal: Import Lembur -->
        <x-payroll.phl.overtime-import-modal :period="$period" />
        
        <!-- Modal: Import Risiko -->
        <x-payroll.phl.risk-import-modal :period="$period" />
        
        <!-- Modal: Edit Absensi -->
        <x-payroll.phl.attendance-edit-modal :period="$period" />
        
        <!-- Modal Konfirmasi Hapus Umum -->
        <x-common.delete-confirm />
    </div>
@endsection
