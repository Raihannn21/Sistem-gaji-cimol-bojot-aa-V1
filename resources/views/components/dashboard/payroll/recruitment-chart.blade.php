@props(['months', 'pkwt', 'phl'])

<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6 flex flex-col h-full">
    <div class="flex flex-col gap-2 mb-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 uppercase tracking-wide">
                Tren Rekrutmen
            </h3>
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    <span class="w-2 h-2 rounded-full bg-brand-500"></span> PKWT
                </span>
                <span class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    <span class="w-2 h-2 rounded-full bg-brand-200 dark:bg-brand-800"></span> PHL
                </span>
            </div>
        </div>
        <p class="text-gray-500 text-theme-sm dark:text-gray-400 font-medium italic">
            Penambahan karyawan baru per bulan (12 Bulan Terakhir)
        </p>
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
