@php
    $toast = session('toast');
    $type = session('toast_type') ?? ($toast['type'] ?? 'success');
    $message = session('toast_message') ?? ($toast['message'] ?? '');

    $styles = [
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-yellow-200 bg-yellow-50 text-yellow-800',
        'info' => 'border-blue-200 bg-blue-50 text-blue-800',
    ];

    $icon = [
        'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
        'error' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M4.93 19h14.14a2 2 0 001.74-3l-7.07-12a2 2 0 00-3.48 0l-7.07 12a2 2 0 001.74 3z" />',
        'info' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 18a6 6 0 110-12 6 6 0 010 12z" />',
    ];
@endphp

@if ($message)
    <div id="app-toast" class="fixed right-6 top-6 z-[999999]">
        <div class="flex items-start gap-3 rounded-xl border px-4 py-3 shadow-lg {{ $styles[$type] ?? $styles['success'] }}">
            <span class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/70">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon[$type] ?? $icon['success'] !!}</svg>
            </span>
            <div class="text-sm font-semibold">
                {{ $message }}
            </div>
            <button type="button" class="ml-2 text-sm" onclick="document.getElementById('app-toast')?.remove()">&times;</button>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('app-toast');
            if (toast) {
                toast.remove();
            }
        }, 3500);
    </script>
@endif
