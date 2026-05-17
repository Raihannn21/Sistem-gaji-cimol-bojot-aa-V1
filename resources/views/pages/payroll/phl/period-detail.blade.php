@extends('layouts.app')

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
        showAttendanceImportModal: false,
        selectedEmployee: {},
        selectedEmployeeOvertimes: [],
        selectedEmployeeRisks: [],
        selectedOvertimeId: null,
        selectedRiskId: null,
        selectedOvertimeDate: '',
        selectedOvertimeDateFormatted: '',
        selectedOvertimeHours: 0,
        selectedOvertimeAmount: 0,
        selectedOvertimeNote: '',
        selectedRiskDate: '',
        selectedRiskAmount: 0,
        selectedRiskNote: '',
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
        errors: {{ $errors->any() ? 'true' : '{}' }},
        @if($errors->any())
            init() {
                // If there are validation errors, we might want to open the specific modal
                // For now just initialize the object
            },
        @endif

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
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pusat kendali penggajian PHL (Security, Driver, & Helper).</p>
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
                    
                    <x-ui.button variant="outline" @click="alert('Silakan kirimkan format template KlikBCA / myBCA Bisnis Anda agar kami dapat membuatkan format file ekspor BCA otomatis!')" className="flex items-center gap-2 text-blue-600 border-blue-200 hover:bg-blue-50 dark:text-blue-400 dark:border-blue-800 dark:hover:bg-blue-950/20">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export BCA
                    </x-ui.button>

                    @if($period->status !== 'Locked')
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
        </div>
        <!-- Modal: Import Absensi -->
        <x-payroll.phl.attendance-import-modal :period="$period" />
        
        <!-- Modal Konfirmasi Hapus Umum -->
        <x-common.delete-confirm />
    </div>
@endsection
