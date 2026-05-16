@props([
    'label' => null,
    'placeholder' => '',
    'type' => 'text',
    'value' => '',
    'prefix' => null,
    'name' => null,
])

@php
    $hasError = $name && $errors->has($name);
    $oldValue = $name ? old($name, $value) : $value;
@endphp

<div>
    @if($label)
        <label class="mb-1.5 block text-sm font-medium {{ $hasError ? 'text-red-500' : 'text-gray-700 dark:text-gray-400' }}">
            {{ $label }}
        </label>
    @endif
    <div class="relative flex items-center group">
        @if($prefix)
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <span class="text-sm font-semibold {{ $hasError ? 'text-red-400' : 'text-gray-500 dark:text-gray-400' }}">
                    {{ $prefix }}
                </span>
            </div>
        @endif
        <input 
            type="{{ $type }}" 
            name="{{ $name }}"
            placeholder="{{ $placeholder }}" 
            value="{{ $oldValue }}"
            {{ $attributes->merge([
                'required' => $attributes->has('required'),
                'class' => ($prefix ? 'pl-12' : 'px-4') . ' h-11 w-full rounded-lg border ' . ($hasError ? 'border-red-500 ring-4 ring-red-500/10' : 'border-gray-300 dark:border-gray-700') . ' bg-white dark:bg-dark-900 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:text-white/90 dark:placeholder:text-white/30 shadow-theme-xs'
            ]) }}
        >
    </div>
    @if($name)
        <!-- Error Client-side (Tanpa Refresh) -->
        <p x-show="errors && errors.{{ $name }}" x-text="errors.{{ $name }}" class="mt-1.5 text-xs text-red-500 font-medium"></p>
        
        <!-- Error Server-side (Laravel) -->
        @error($name)
            <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
        @enderror
    @endif
</div>
