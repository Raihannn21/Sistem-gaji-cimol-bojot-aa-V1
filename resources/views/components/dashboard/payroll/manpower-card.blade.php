@props(['total', 'pkwt', 'phl'])

<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] flex flex-col">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-500">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Total Manpower</p>
            <h4 class="mt-0.5 text-lg font-bold text-gray-800 dark:text-white/90 tabular-nums">{{ number_format($total) }} <span class="text-xs font-medium text-gray-400">Orang</span></h4>
        </div>
    </div>

    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 space-y-2">
        <div class="flex justify-between items-center text-[11px]">
            <span class="font-medium text-gray-400 dark:text-gray-400">PKWT (Kontrak)</span>
            <span class="font-bold text-gray-800 dark:text-white tabular-nums">{{ number_format($pkwt) }} Orang</span>
        </div>
        <div class="flex justify-between items-center text-[11px]">
            <span class="font-medium text-gray-400 dark:text-gray-400">PHL</span>
            <span class="font-bold text-gray-800 dark:text-white tabular-nums">{{ number_format($phl) }} Orang</span>
        </div>
    </div>
</div>
