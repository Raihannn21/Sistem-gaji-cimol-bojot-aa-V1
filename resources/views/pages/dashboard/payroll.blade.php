@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl" x-data="{ selectedMonth: 'Juli', selectedYear: '2025' }">
        <div class="mb-8 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <x-common.page-breadcrumb :pageName="$title" />

            <!-- Period Selector -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] shadow-sm h-11 px-1">
                    <button class="flex items-center gap-2 px-4 h-full text-sm font-bold text-gray-700 dark:text-gray-300 hover:text-brand-600 transition-colors">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span x-text="selectedMonth + ' ' + selectedYear"></span>
                        <svg class="h-4 w-4 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Grid (Following eCommerce Layout) -->
        <div class="grid grid-cols-12 gap-4 md:gap-6">
            
            <!-- Row 1: Top Level Metrics (Full Width Grid) -->
            <div class="col-span-12">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 md:gap-6">
                    <x-dashboard.payroll.salary-cost-card />
                    <x-dashboard.payroll.overtime-cost-card />
                    <x-dashboard.payroll.manpower-card />
                    <x-dashboard.payroll.work-effort-card />
                </div>
            </div>

            <!-- New Row: Payroll Analytics -->
            <div class="col-span-12">
                <x-dashboard.payroll.analytics-chart />
            </div>

            <!-- Row 2: Trends & Charts -->
            <div class="col-span-12 xl:col-span-7">
                <x-dashboard.payroll.recruitment-chart />
            </div>

            <div class="col-span-12 xl:col-span-5">
                <x-dashboard.payroll.turnover-chart />
            </div>

            <!-- Row 3: Operational Health (Bottom) -->
            <div class="col-span-12 xl:col-span-4">
                <x-dashboard.payroll.lateness-card />
            </div>
        </div>
    </div>
@endsection
