@props(['months', 'monthlyTrend', 'dailyDates', 'dailyCounts', 'dailyHours'])

<div class="rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6 max-w-full overflow-hidden" 
     x-data="{ viewMode: 'monthly' }">
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:justify-between sm:items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Analisis Keterlambatan Karyawan
            </h3>
            <p class="mt-1 text-gray-505 text-theme-sm dark:text-gray-400">
                Visualisasi tingkat kedisiplinan kehadiran karyawan PKWT (Bulanan) & PHL (Harian)
            </p>
        </div>

        <!-- Tab Toggle Buttons -->
        <div class="flex items-center gap-1 bg-gray-100 dark:bg-white/5 p-1 rounded-xl shrink-0">
            <button @click="viewMode = 'monthly'; setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 50)" 
                    :class="viewMode === 'monthly' ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
                Tren Bulanan (PKWT)
            </button>
            <button @click="viewMode = 'daily'; setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 50)" 
                    :class="viewMode === 'daily' ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
                Detail Harian (PHL)
            </button>
        </div>
    </div>

    <!-- Chart Containers -->
    <div class="w-full">
        <div x-show="viewMode === 'monthly'" class="w-full">
            <div id="latenessMonthlyChart" class="w-full"></div>
        </div>
        <div x-show="viewMode === 'daily'" class="w-full" x-cloak>
            @if(empty($dailyDates))
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <p class="text-sm text-gray-400 italic">Tidak ada data harian untuk periode aktif ini.</p>
                </div>
            @else
                <div id="latenessDailyChart" class="w-full"></div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            function initLatenessCharts() {
                if (typeof ApexCharts === 'undefined') {
                    setTimeout(initLatenessCharts, 50);
                    return;
                }

                // 1. Monthly Lateness Rate Chart (Area Chart)
                const monthlyOptions = {
                    series: [{
                        name: 'Rate Keterlambatan',
                        data: @json($monthlyTrend)
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
                        width: 3,
                        colors: ['#ef4444']
                    },
                    colors: ['#ef4444'],
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
                            formatter: function (val) {
                                return val + '%';
                            },
                            style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 }
                        }
                    },
                    tooltip: {
                        x: { show: true },
                        y: {
                            formatter: function (val) {
                                return val + '% Kehadiran';
                            }
                        }
                    }
                };

                const monthlyChart = new ApexCharts(document.querySelector("#latenessMonthlyChart"), monthlyOptions);
                monthlyChart.render();

                // 2. Daily Lateness Count & Hours Chart (Mixed Column & Line Chart)
                @if(!empty($dailyDates))
                const dailyOptions = {
                    series: [{
                        name: 'Kasus Terlambat',
                        type: 'column',
                        data: @json($dailyCounts)
                    }, {
                        name: 'Durasi Terlambat',
                        type: 'line',
                        data: @json($dailyHours)
                    }],
                    chart: {
                        height: 300,
                        type: 'line',
                        toolbar: { show: false },
                        fontFamily: 'Inter, sans-serif'
                    },
                    stroke: {
                        width: [0, 3],
                        curve: 'smooth'
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '40%',
                            borderRadius: 4
                        }
                    },
                    colors: ['#fca5a5', '#dc2626'],
                    xaxis: {
                        categories: @json($dailyDates),
                        labels: {
                            rotate: -45,
                            style: { colors: '#9ca3af', fontSize: '9px', fontWeight: 600 }
                        }
                    },
                    yaxis: [{
                        title: {
                            text: 'Jumlah Kasus',
                            style: { color: '#ef4444', fontSize: '10px', fontWeight: 600 }
                        },
                        labels: {
                            style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 }
                        }
                    }, {
                        opposite: true,
                        title: {
                            text: 'Durasi (Jam)',
                            style: { color: '#b91c1c', fontSize: '10px', fontWeight: 600 }
                        },
                        labels: {
                            formatter: function(val) {
                                return val + ' jam';
                            },
                            style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 }
                        }
                    }],
                    grid: {
                        borderColor: '#f1f1f1',
                        strokeDashArray: 4
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    }
                };

                const dailyChart = new ApexCharts(document.querySelector("#latenessDailyChart"), dailyOptions);
                dailyChart.render();
                @endif
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initLatenessCharts);
            } else {
                initLatenessCharts();
            }
        })();
    </script>
@endpush
