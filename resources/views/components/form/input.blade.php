@props([
    'label' => null,
    'placeholder' => '',
    'type' => 'text',
    'value' => '',
    'prefix' => null,
])

<div>
    @if($label)
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ $label }}
        </label>
    @endif
    <div class="relative flex items-center group">
        @if($prefix)
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                    {{ $prefix }}
                </span>
            </div>
        @endif
        <input 
            type="{{ $type }}" 
            placeholder="{{ $placeholder }}" 
            value="{{ $value }}"
            {{ $attributes->merge(['class' => ($prefix ? 'pl-12' : 'px-4') . ' h-11 w-full rounded-lg border border-gray-300 bg-white dark:bg-dark-900 px-4 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30 shadow-theme-xs']) }}
        >
    </div>
</div>
