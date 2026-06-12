@props([
    'model' => 'perPage',
    'page' => 'currentPage',
])

<div class="relative inline-block text-left" x-data="{ open: false }" @click.away="open = false">
    <button type="button" @click="open = !open"
        class="flex h-8 w-16 items-center justify-between rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-gray-700 shadow-sm focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 cursor-pointer">
        <span x-text="{{ $model }}"></span>
        <svg class="h-3 w-3 stroke-current text-gray-550 dark:text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </button>
    <div x-show="open" x-transition x-cloak
         style="bottom: 100%; margin-bottom: 4px;"
         class="absolute left-0 w-16 rounded-lg border border-gray-150 bg-white p-1 shadow-lg dark:border-gray-800 dark:bg-gray-900 z-50">
        <div class="space-y-0.5">
            <template x-for="val in [5, 10, 15, 25, 50]">
                <button type="button" 
                    @click="{{ $model }} = val; {{ $page }} = 1; open = false;"
                    class="flex w-full items-center justify-center rounded px-2 py-1 text-xs text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 transition-colors font-semibold"
                    :class="{{ $model }} === val ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-500 font-bold' : ''"
                    x-text="val">
                </button>
            </template>
        </div>
    </div>
</div>
