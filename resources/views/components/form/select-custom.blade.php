@props([
    'label' => null,
    'placeholder' => 'Select Option',
    'options' => [],
    'name' => null,
])

@php
    $hasError = $name && $errors->has($name);
    $oldValue = $name ? old($name) : null;
@endphp

<div x-data="{ 
    open: false, 
    selected: '{{ $oldValue }}', 
    search: '',
    select(value, text) {
        this.selected = value;
        this.search = text;
        this.open = false;
        this.$dispatch('change-' + '{{ $name }}', value);
    },
    init() {
        // Try to find the label for the old value if it exists
        if(this.selected) {
            this.$nextTick(() => {
                const activeItem = this.$el.querySelector(`[data-value='${this.selected}']`);
                if(activeItem) {
                    this.search = activeItem.innerText.trim();
                }
            });
        }

        this.$watch('search', query => {
            const q = query.toLowerCase().trim();
            
            // Find current label to avoid filtering when opened with active selection
            let currentLabel = '';
            if (this.selected) {
                const activeItem = this.$el.querySelector(`[data-value='${this.selected}']`);
                if (activeItem) currentLabel = activeItem.innerText.trim().toLowerCase();
            }

            const items = this.$el.querySelectorAll('[data-value]');
            items.forEach(item => {
                const text = item.innerText.toLowerCase();
                if (q === '' || q === currentLabel) {
                    item.style.display = 'flex';
                } else {
                    if (text.includes(q)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
        });

        this.$watch('open', value => {
            if (!value) {
                if (this.selected) {
                    const activeItem = this.$el.querySelector(`[data-value='${this.selected}']`);
                    if(activeItem) this.search = activeItem.innerText.trim();
                } else {
                    this.search = '';
                }
                const items = this.$el.querySelectorAll('[data-value]');
                items.forEach(item => item.style.display = 'flex');
            }
        });
    }
}" 
class="relative w-full"
:class="open ? 'z-9999' : 'z-20'">
    @if($label)
        <label class="mb-1.5 block text-sm font-medium {{ $hasError ? 'text-red-500' : 'text-gray-700 dark:text-gray-400' }}">
            {{ $label }}
        </label>
    @endif

    
    <div class="relative">
        <input type="hidden" name="{{ $name }}" x-model="selected" {{ $attributes->has('required') ? 'required' : '' }}>
        
        <div class="relative flex items-center group">
            <input 
                type="text"
                x-model="search"
                placeholder="{{ $placeholder }}"
                @focus="open = true"
                @click.stop="open = true"
                @input="open = true"
                class="h-11 w-full rounded-lg border bg-white dark:bg-gray-900 px-4 pr-11 py-2.5 text-sm text-gray-800 dark:text-white outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:focus:border-brand-800 {{ $hasError ? 'border-red-500 ring-4 ring-red-500/10' : 'border-gray-300 dark:border-gray-700' }} shadow-theme-xs placeholder:text-gray-400 dark:placeholder:text-white/30"
                autocomplete="off"
            >
            <button 
                type="button" 
                @click.stop="open = !open" 
                class="absolute right-4 text-gray-500 hover:text-gray-700 dark:hover:text-white"
            >
                <svg 
                    class="h-5 w-5 transition-transform duration-200" 
                    :class="{ 'rotate-180': open }"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>

        <div 
            x-show="open" 
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute left-0 mt-2 w-full rounded-xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900 z-50 bg-white"
            x-cloak
        >
            <div class="max-h-60 overflow-y-auto custom-scrollbar bg-white dark:bg-gray-900">
                @if(!empty($options))
                    @foreach($options as $option)
                        <button 
                            type="button"
                            @click="select('{{ $option['value'] }}', '{{ $option['label'] }}')"
                            class="flex w-full items-center rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 transition-colors"
                            :class="selected === '{{ $option['value'] }}' ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-500 font-medium' : ''"
                        >
                            {{ $option['label'] }}
                        </button>
                    @endforeach
                @endif
                {{ $slot }}
            </div>
        </div>
    </div>
    
    @if($name)
        <!-- Error Client-side -->
        <p x-show="typeof errors !== 'undefined' && errors.{{ $name }}" x-text="typeof errors !== 'undefined' ? errors.{{ $name }} : ''" class="mt-1.5 text-xs text-red-500 font-medium"></p>
        
        <!-- Error Server-side -->
        @error($name)
            <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
        @enderror
    @endif
</div>
