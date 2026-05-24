@props([
    'overallRate', 
    'overallDiff', 
    'pkwtRate', 
    'pkwtHours', 
    'pkwtCount', 
    'pkwtDiff', 
    'phlRate', 
    'phlHours', 
    'phlCount', 
    'phlDiff'
])

<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] flex flex-col justify-between h-full group relative overflow-hidden">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 items-center">
        <!-- Column 1: Overall Lateness Rate & Progress Bar -->
        <div class="space-y-4 pr-0 sm:pr-6 border-b sm:border-b-0 sm:border-r border-gray-100 dark:border-gray-800 pb-4 sm:pb-0">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-550 dark:text-gray-400 whitespace-nowrap">Rate Keterlambatan</p>
                    <h4 class="mt-0.5 text-lg font-bold text-gray-800 dark:text-white/90 tabular-nums">
                        {{ number_format($overallRate, 1, ',', '.') }}%
                    </h4>
                </div>
            </div>
            
            <!-- Overall Severity Progress Bar -->
            <div class="space-y-2">
                <div class="w-full bg-gray-100 rounded-full h-1.5 dark:bg-gray-800">
                    <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ min(100, max(5, $overallRate)) }}%"></div>
                </div>
                <div class="flex justify-between items-center text-[10px]">
                    @if($overallDiff > 0)
                        <span class="text-gray-500 dark:text-gray-400 font-medium">Meningkat vs bulan lalu</span>
                        <span class="font-bold text-red-500">+{{ number_format($overallDiff, 1, ',', '.') }}%</span>
                    @elseif($overallDiff < 0)
                        <span class="text-gray-500 dark:text-gray-400 font-medium">Menurun vs bulan lalu</span>
                        <span class="font-bold text-green-500">{{ number_format($overallDiff, 1, ',', '.') }}%</span>
                    @else
                        <span class="text-gray-500 dark:text-gray-400 font-medium">Stabil vs bulan lalu</span>
                        <span class="font-bold text-gray-455">0%</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Column 2: PKWT (Bulanan) Details -->
        <div class="space-y-2 pr-0 sm:pr-6 border-b sm:border-b-0 sm:border-r border-gray-100 dark:border-gray-800 pb-4 sm:pb-0">
            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 block">PKWT (Bulanan)</span>
            <div class="flex items-baseline gap-2">
                <span class="text-xl font-bold text-gray-800 dark:text-white tabular-nums">{{ number_format($pkwtRate, 1, ',', '.') }}%</span>
                <span class="text-[10px] font-semibold {{ $pkwtDiff > 0 ? 'text-red-600 dark:text-red-500' : ($pkwtDiff < 0 ? 'text-green-600 dark:text-green-500' : 'text-gray-450') }}">
                    {{ $pkwtDiff > 0 ? '+' : '' }}{{ number_format($pkwtDiff, 1, ',', '.') }}%
                </span>
            </div>
            <div class="text-[10px] text-gray-505 dark:text-gray-400 space-y-0.5">
                <span class="block">Total Kasus: <strong class="text-gray-700 dark:text-gray-300 font-semibold">{{ $pkwtCount }} Kasus</strong></span>
                <span class="block">Total Durasi: <strong class="text-gray-700 dark:text-gray-300 font-semibold">{{ number_format($pkwtHours, 1, ',', '.') }} Jam</strong></span>
            </div>
        </div>

        <!-- Column 3: PHL (Harian) Details -->
        <div class="space-y-2 pl-0 sm:pl-2">
            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 block">PHL (Harian)</span>
            <div class="flex items-baseline gap-2">
                <span class="text-xl font-bold text-gray-800 dark:text-white tabular-nums">{{ number_format($phlRate, 1, ',', '.') }}%</span>
                <span class="text-[10px] font-semibold {{ $phlDiff > 0 ? 'text-red-600 dark:text-red-500' : ($phlDiff < 0 ? 'text-green-600 dark:text-green-500' : 'text-gray-450') }}">
                    {{ $phlDiff > 0 ? '+' : '' }}{{ number_format($phlDiff, 1, ',', '.') }}%
                </span>
            </div>
            <div class="text-[10px] text-gray-550 dark:text-gray-400 space-y-0.5">
                <span class="block">Total Kasus: <strong class="text-gray-700 dark:text-gray-300 font-semibold">{{ $phlCount }} Kasus</strong></span>
                <span class="block">Total Durasi: <strong class="text-gray-700 dark:text-gray-300 font-semibold">{{ number_format($phlHours, 1, ',', '.') }} Jam</strong></span>
            </div>
        </div>
    </div>
</div>
