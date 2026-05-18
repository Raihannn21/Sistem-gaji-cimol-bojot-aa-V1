@props(['months', 'realData', 'estData'])

<div class="rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
    <div class="flex flex-col gap-5 mb-6 sm:flex-row sm:justify-between">
        <div class="w-full">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Payroll Analytics
            </h3>
            <p class="mt-1 text-gray-500 text-theme-sm dark:text-gray-400">
                Statistik dan tren operasional penggajian karyawan (12 Bulan Terakhir)
            </p>
        </div>

        <div class="flex items-start w-full gap-3 sm:justify-end">
            <!-- Year Selector Dropdown -->
            <div x-data="{ open: false, selectedYear: '2026' }" class="relative z-20">
                <button @click="open = !open" 
                    class="h-10 inline-flex items-center justify-between gap-2.5 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-theme-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 min-w-[120px]">
                    <span class="flex items-center gap-2">
                        <svg class="text-gray-500 dark:text-gray-400" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66683 1.54199C7.08104 1.54199 7.41683 1.87778 7.41683 2.29199V3.00033H12.5835V2.29199C12.5835 1.87778 12.9193 1.54199 13.3335 1.54199C13.7477 1.54199 14.0835 1.87778 14.0835 2.29199V3.00033L15.4168 3.00033C16.5214 3.00033 17.4168 3.89576 17.4168 5.00033V7.50033V15.8337C17.4168 16.9382 16.5214 17.8337 15.4168 17.8337H4.5835C3.47893 17.8337 2.5835 16.9382 2.5835 15.8337V7.50033V5.00033C2.5835 3.89576 3.47893 3.00033 4.5835 3.00033L5.91683 3.00033V2.29199C5.91683 1.87778 6.25262 1.54199 6.66683 1.54199ZM6.66683 4.50033H4.5835C4.30735 4.50033 4.0835 4.72418 4.0835 5.00033V6.75033H15.9168V5.00033C15.9168 4.72418 15.693 4.50033 15.4168 4.50033H13.3335H6.66683ZM15.9168 8.25033H4.0835V15.8337C4.0835 16.1098 4.30735 16.3337 4.5835 16.3337H15.4168C15.693 16.3337 15.9168 16.1098 15.9168 15.8337V8.25033Z" fill="currentColor" />
                        </svg>
                        <span x-text="selectedYear"></span>
                    </span>
                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" 
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-32 rounded-lg border border-gray-150 bg-white p-1 shadow-lg dark:border-gray-800 dark:bg-gray-900 z-50">
                    <button @click="selectedYear = '2026'; open = false" class="w-full text-left px-3 py-2 text-theme-sm font-medium rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">2026</button>
                    <button @click="selectedYear = '2025'; open = false" class="w-full text-left px-3 py-2 text-theme-sm font-medium rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">2025</button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <div id="payrollAnalyticsChart" class="-ml-4 min-w-[700px] min-h-[300px] pl-2 xl:min-w-full"></div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        function initChart() {
            if (typeof ApexCharts === 'undefined') {
                setTimeout(initChart, 50);
                return;
            }
            const options = {
                series: [{
                    name: 'Realisasi Gaji',
                    data: @json($realData)
                }, {
                    name: 'Estimasi Gaji',
                    data: @json($estData)
                }],
                chart: {
                    height: 300,
                    type: 'area',
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif',
                    zoom: { enabled: false }
                },
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                colors: ['#3c50e0', '#80caee'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [20, 100, 100, 100]
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 4,
                    yaxis: { lines: { show: true } }
                },
                xaxis: {
                    categories: @json($months),
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 }
                    }
                },
                yaxis: {
                    min: 0,
                    labels: {
                        style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 }
                    }
                },
                tooltip: {
                    x: { show: true },
                    y: {
                        formatter: function (val) {
                            return 'Rp ' + val + ' Juta';
                        }
                    }
                },
                legend: {
                    show: false
                }
            };

            const chart = new ApexCharts(document.querySelector("#payrollAnalyticsChart"), options);
            chart.render();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initChart);
        } else {
            initChart();
        }
    })();
</script>
@endpush
