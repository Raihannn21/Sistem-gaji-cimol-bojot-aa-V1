@props([
    'label' => null,
    'placeholder' => 'Select Option',
    'options' => [],
    'name' => null,
])

<div x-data="{ 
    open: false, 
    selected: '', 
    label: '{{ $placeholder }}',
    select(value, text) {
        this.selected = value;
        this.label = text;
        this.open = false;
    }
}" 
class="relative w-full"
:class="open ? 'z-9999' : 'z-20'">
    @if($label)
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ $label }}
        </label>
    @endif
    
    <div class="relative">
        <input type="hidden" name="{{ $name }}" x-model="selected">
        
        <button 
            type="button"
            @click="open = !open"
            class="relative flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
            :class="selected ? 'text-gray-800 dark:text-white' : 'text-gray-400 dark:text-white/30'"
        >
            <span x-text="label"></span>
            <svg 
                class="h-5 w-5 transition-transform duration-200 text-gray-500" 
                :class="{ 'rotate-180': open }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div 
            x-show="open" 
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute left-0 mt-2 w-full rounded-xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900 z-[99999]"
            x-cloak
        >
            <div class="max-h-60 overflow-y-auto custom-scrollbar bg-white dark:bg-gray-900">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #E5E7EB;
        border-radius: 10px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #374151;
    }
</style>
