@props(['value' => ''])

<button 
    type="button"
    data-value="{{ $value }}"
    @click="select('{{ $value }}', $el.innerText.trim())"
    class="flex w-full items-center rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300 transition-colors"
>
    {{ $slot }}
</button>
