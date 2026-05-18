@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl" x-data="{ selectedMonth: 'Juli', selectedYear: '2025' }">
        <div class="mb-8 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <x-common.page-breadcrumb :pageName="$title" />

            <!-- Period Selector -->
            <div x-data="{ 
                open: false,
                months: [
                    { val: '01', name: 'Januari' },
                    { val: '02', name: 'Februari' },
                    { val: '03', name: 'Maret' },
                    { val: '04', name: 'April' },
                    { val: '05', name: 'Mei' },
                    { val: '06', name: 'Juni' },
                    { val: '07', name: 'Juli' },
                    { val: '08', name: 'Agustus' },
                    { val: '09', name: 'September' },
                    { val: '10', name: 'Oktober' },
                    { val: '11', name: 'November' },
                    { val: '12', name: 'Desember' }
                ],
                years: ['2025', '2026', '2027'],
                selectedMonthVal: '{{ sprintf("%02d", $selectedMonth) }}',
                selectedYearVal: '{{ $selectedYear }}',
                
                getMonthName(val) {
                    const m = this.months.find(x => x.val === val);
                    return m ? m.name : '';
                },
                
                selectPeriod(monthVal, yearVal) {
                    window.location.search = '?month=' + monthVal + '&year=' + yearVal;
                }
            }" class="relative z-30">
                <button @click="open = !open" 
                    class="h-11 inline-flex items-center justify-between gap-2.5 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-theme-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 min-w-[150px]">
                    <span class="flex items-center gap-2">
                        <svg class="text-gray-500 dark:text-gray-400" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66683 1.54199C7.08104 1.54199 7.41683 1.87778 7.41683 2.29199V3.00033H12.5835V2.29199C12.5835 1.87778 12.9193 1.54199 13.3335 1.54199C13.7477 1.54199 14.0835 1.87778 14.0835 2.29199V3.00033L15.4168 3.00033C16.5214 3.00033 17.4168 3.89576 17.4168 5.00033V7.50033V15.8337C17.4168 16.9382 16.5214 17.8337 15.4168 17.8337H4.5835C3.47893 17.8337 2.5835 16.9382 2.5835 15.8337V7.50033V5.00033C2.5835 3.89576 3.47893 3.00033 4.5835 3.00033L5.91683 3.00033V2.29199C5.91683 1.87778 6.25262 1.54199 6.66683 1.54199ZM6.66683 4.50033H4.5835C4.30735 4.50033 4.0835 4.72418 4.0835 5.00033V6.75033H15.9168V5.00033C15.9168 4.72418 15.693 4.50033 15.4168 4.50033H13.3335H6.66683ZM15.9168 8.25033H4.0835V15.8337C4.0835 16.1098 4.30735 16.3337 4.5835 16.3337H15.4168C15.693 16.3337 15.9168 16.1098 15.9168 15.8337V8.25033Z" fill="currentColor" />
                        </svg>
                        <span x-text="getMonthName(selectedMonthVal) + ' ' + selectedYearVal"></span>
                    </span>
                    <svg class="text-gray-500 dark:text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <!-- Dropdown Card -->
                <div x-show="open" @click.away="open = false" 
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-72 rounded-2xl border border-gray-150 bg-white p-4 shadow-lg dark:border-gray-800 dark:bg-gray-900 z-50">
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Column 1: Months -->
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Bulan</p>
                            <div class="max-h-48 overflow-y-auto custom-scrollbar space-y-1 pr-1">
                                <template x-for="m in months">
                                    <button @click="selectPeriod(m.val, selectedYearVal)" 
                                        class="w-full text-left px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                        :class="selectedMonthVal === m.val ? 'bg-brand-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-850'"
                                        x-text="m.name">
                                    </button>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Column 2: Years -->
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Tahun</p>
                            <div class="space-y-1">
                                <template x-for="y in years">
                                    <button @click="selectPeriod(selectedMonthVal, y)" 
                                        class="w-full text-left px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                        :class="selectedYearVal === y ? 'bg-brand-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-850'"
                                        x-text="y">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Grid (Following eCommerce Layout) -->
        <div class="grid grid-cols-12 gap-4 md:gap-6">
            
            <!-- Row 1: Top Level Metrics (Full Width Grid) -->
            <div class="col-span-12">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 md:gap-6">
                    <x-dashboard.payroll.salary-cost-card :total="$totalSalaryCost" :pkwt="$totalPkwtSalary" :phl="$totalPhlSalary" />
                    <x-dashboard.payroll.overtime-cost-card :total="$totalOvertimeCost" :pkwt="$pkwtOvertimeCost" :phl="$phlOvertimeCost" />
                    <x-dashboard.payroll.manpower-card :total="$totalManpower" :pkwt="$pkwtCount" :phl="$phlCount" />
                    <x-dashboard.payroll.work-effort-card :total="$totalWorkEffort" :reg="$totalRegHours" :ovt="$totalOvtHours" />
                </div>
            </div>

            <!-- New Row: Payroll Analytics -->
            <div class="col-span-12 min-w-0">
                <x-dashboard.payroll.analytics-chart :months="$months" :realData="$payrollRealData" :estData="$payrollEstData" :selectedYear="$selectedYear" />
            </div>

            <!-- Row 2: Trends & Charts -->
            <div class="col-span-12 xl:col-span-7 min-w-0">
                <x-dashboard.payroll.recruitment-chart :months="$months" :pkwt="$recruitmentPkwt" :phl="$recruitmentPhl" />
            </div>

            <div class="col-span-12 xl:col-span-5 min-w-0">
                <x-dashboard.payroll.turnover-chart :rate="$turnoverRate" :total="$totalResigned" :pkwt="$pkwtResigned" :phl="$phlResigned" :sparkline="$turnoverSparklineData" />
            </div>
        </div>
    </div>
@endsection
