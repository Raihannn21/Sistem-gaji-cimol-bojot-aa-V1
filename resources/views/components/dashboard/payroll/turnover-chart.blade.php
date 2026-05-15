<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6 flex flex-col h-full relative overflow-hidden">
    <div class="flex flex-col gap-2 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 uppercase tracking-wide">
            Turnover Karyawan
        </h3>
        <p class="text-gray-500 text-theme-sm dark:text-gray-400 font-medium italic">
            Tingkat pengunduran diri & keluar
        </p>
    </div>

    <div class="flex items-center justify-between mb-4 relative">
        <div>
            <h3 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter tabular-nums">4.2<span class="text-xl text-gray-400">%</span></h3>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-2">Bulan Ini</p>
        </div>
        <div class="text-right">
            <p class="text-sm font-black text-red-500 tabular-nums">12 Orang</p>
            <p class="text-xs text-gray-500 font-medium">Total Keluar</p>
        </div>
    </div>

    <!-- Mini Detail -->
    <div class="grid grid-cols-2 gap-4 py-4 border-y border-gray-100 dark:border-gray-800 relative mb-4">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">PKWT</p>
            <p class="text-sm font-black text-gray-800 dark:text-white tabular-nums">4 Orang</p>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">PHL</p>
            <p class="text-sm font-black text-gray-800 dark:text-white tabular-nums">8 Orang</p>
        </div>
    </div>

    <!-- Area Sparkline (At bottom of card) -->
    <div class="relative -mx-5 -mb-5 sm:-mx-6 sm:-mb-6 mt-auto">
        <div id="turnoverSparkline"></div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const options = {
            series: [{
                name: 'Turnover Rate',
                data: [3.1, 4.0, 2.8, 5.1, 4.2, 3.8, 4.2]
            }],
            chart: {
                type: 'area',
                height: 100,
                sparkline: { enabled: true },
                fontFamily: 'Inter, sans-serif'
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100, 100, 100]
                }
            },
            colors: ['#ef4444'],
            tooltip: {
                theme: 'dark',
                fixed: { enabled: false },
                x: { show: false },
                y: { title: { formatter: (seriesName) => '' } },
                marker: { show: false }
            }
        };

        const chart = new ApexCharts(document.querySelector("#turnoverSparkline"), options);
        chart.render();
    });
</script>
@endpush
