@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl" x-data="{ 
        activeTab: 'overview',
        // Global Modal States
        showOvertimeModal: false,
        showRiskModal: false,
        showDetailModal: false,
        showEditRiskModal: false,
        showSlipModal: false,
        showConfirmModal: false,
        selectedEmployee: {},
        selectedRiskDate: '',
        selectedRiskAmount: 0,
        selectedRiskNote: '',
        selectedSlip: {},
        processing: false,
        
        generate() {
            this.processing = true;
            setTimeout(() => {
                this.processing = false;
                this.showConfirmModal = false;
                alert('Payroll berhasil digenerate!');
            }, 2000);
        }
    }">
        <!-- Header: Contextual Command Center -->
        <div class="mb-8 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-5">
                <a href="{{ url('/payroll/phl/periods') }}" class="group flex h-12 w-12 items-center justify-center rounded-2xl bg-white shadow-sm border border-gray-100 text-gray-400 hover:text-brand-500 hover:border-brand-100 transition-all dark:bg-white/5 dark:border-gray-800 dark:hover:bg-white/10">
                    <svg class="h-6 w-6 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-black tracking-tight text-gray-800 dark:text-white">Juli 2025</h2>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-500/10 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-green-600 dark:text-green-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            Active Period
                        </span>
                    </div>
                    <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Pusat Kendali Penggajian PHL (Security, Driver, & Helper)</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <button class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-bold text-gray-700 shadow-sm transition-all hover:bg-gray-50 dark:border-gray-800 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Report PDF
                </button>
                <x-ui.button variant="primary" @click="showConfirmModal = true" x-show="activeTab === 'overview'" className="shadow-lg shadow-brand-500/20">
                    Generate All Payroll
                </x-ui.button>
            </div>
        </div>

        <!-- Navigation Tabs (Premium UI) -->
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white p-1.5 shadow-sm dark:border-gray-800 dark:bg-white/[0.02]">
            <div class="flex flex-wrap items-center gap-1">
                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5'" class="flex flex-1 items-center justify-center gap-2.5 rounded-xl py-3 text-sm font-bold transition-all min-w-[120px]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Overview
                </button>
                <button @click="activeTab = 'attendance'" :class="activeTab === 'attendance' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5'" class="flex flex-1 items-center justify-center gap-2.5 rounded-xl py-3 text-sm font-bold transition-all min-w-[120px]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Absensi
                </button>
                <button @click="activeTab = 'overtime'" :class="activeTab === 'overtime' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5'" class="flex flex-1 items-center justify-center gap-2.5 rounded-xl py-3 text-sm font-bold transition-all min-w-[120px]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Lembur
                </button>
                <button @click="activeTab = 'risk'" :class="activeTab === 'risk' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5'" class="flex flex-1 items-center justify-center gap-2.5 rounded-xl py-3 text-sm font-bold transition-all min-w-[120px]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Risiko
                </button>
                <button @click="activeTab = 'slips'" :class="activeTab === 'slips' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5'" class="flex flex-1 items-center justify-center gap-2.5 rounded-xl py-3 text-sm font-bold transition-all min-w-[120px]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Slip Gaji
                </button>
            </div>
        </div>

        <!-- Tab Content Wrapper (Modularized via Partials) -->
        <div class="min-h-[500px]">
            @include('pages.payroll.phl.tabs._overview')
            @include('pages.payroll.phl.tabs._attendance')
            @include('pages.payroll.phl.tabs._overtime')
            @include('pages.payroll.phl.tabs._risk')
            @include('pages.payroll.phl.tabs._slips')
        </div>

        <!-- Modal Components (Consolidated) -->
        <x-payroll.phl.overtime-modal />
        <x-payroll.phl.overtime-detail-modal />
        <x-payroll.phl.overtime-edit-modal />
        <x-payroll.phl.risk-allowance-modal />
        <x-payroll.phl.risk-allowance-detail-modal />
        <x-payroll.phl.risk-allowance-edit-modal />
        <x-payroll.phl.generate-confirm-modal />
        <x-payroll.phl.payslip-modal />
    </div>
@endsection
