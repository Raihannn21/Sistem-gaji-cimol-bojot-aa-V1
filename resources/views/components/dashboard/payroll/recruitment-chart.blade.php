@props(['months', 'pkwt', 'phl', 'selectedYear'])

<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6 flex flex-col h-full">
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 uppercase tracking-wide">
                Tren Rekrutmen
            </h3>
            <p class="text-gray-500 text-theme-sm dark:text-gray-400 font-medium italic mt-1">
                Penambahan karyawan baru per bulan (Januari - Desember)
            </p>
        </div>
        
        <div class="flex items-center gap-4 sm:justify-end">
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    <span class="w-2 h-2 rounded-full" style="background-color: #3c50e0;"></span> PKWT
                </span>
                <span class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    <span class="w-2 h-2 rounded-full" style="background-color: #80caee;"></span> PHL
                </span>
            </div>

            <!-- Year Selector Dropdown -->
            <div x-data="{ 
                open: false, 
                selectedYear: '{{ $selectedYear }}',
                selectYear(year) {
                    this.selectedYear = year;
                    this.open = false;
                    const urlParams = new URLSearchParams(window.location.search);
                    urlParams.set('year', year);
                    window.location.search = urlParams.toString();
                }
            }" class="relative z-20">
                <button @click="open = !open"
                    class="h-10 inline-flex items-center justify-between gap-2.5 rounded-full border border-gray-200 bg-white px-4 py-2.5 text-theme-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 min-w-[120px]">
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
                    class="absolute right-0 mt-1 w-28 rounded-lg border border-gray-150 bg-white p-1 shadow-lg dark:border-gray-800 dark:bg-gray-900 z-50">
                    <button @click="selectYear('2027')" class="w-full text-left px-2.5 py-1.5 text-xs font-semibold rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">2027</button>
                    <button @click="selectYear('2026')" class="w-full text-left px-2.5 py-1.5 text-xs font-semibold rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">2026</button>
                    <button @click="selectYear('2025')" class="w-full text-left px-2.5 py-1.5 text-xs font-semibold rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">2025</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ApexCharts Container with Horizontal Scroll -->
    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <div id="recruitmentTrendChart" class="-ml-5 min-h-[250px] min-w-[700px] xl:min-w-full"></div>
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
                    name: 'Kontrak (PKWT)',
                    data: @json($pkwt)
                }, {
                    name: 'PHL',
                    data: @json($phl)
                }],
                chart: {
                    type: 'bar',
                    height: 250,
                    stacked: true,
                    toolbar: { show: false },
                    zoom: { enabled: true },
                    fontFamily: 'Inter, sans-serif'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        legend: {
                            position: 'bottom',
                            offsetX: -10,
                            offsetY: 0
                        }
                    }
                }],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 4,
                        columnWidth: '35%',
                    },
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
                    labels: {
                        style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 }
                    }
                },
                legend: { show: false },
                fill: { opacity: 1 },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 4,
                    xaxis: { lines: { show: false } }
                },
                colors: ['#3c50e0', '#80caee'],
                dataLabels: { enabled: false },
                tooltip: {
                    x: { show: false },
                    y: {
                        formatter: function (val) {
                            return val + ' Orang';
                        },
                    },
                }
            };

            const chart = new ApexCharts(document.querySelector("#recruitmentTrendChart"), options);
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
