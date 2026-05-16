@php
    $message = session('success') ?? session('status') ?? session('toast_message') ?? "TES TOAST PAKSA (Jika Anda melihat ini, lapor saya Pak!)";
    $type = session('error') ? 'error' : 'success';

    $styles = [
        'success' => 'background: #059669; color: white; border: 2px solid #064e3b;',
        'error' => 'background: #dc2626; color: white; border: 2px solid #7f1d1d;',
    ];
@endphp

<div id="app-toast"
    style="position: fixed; top: 20px; right: 20px; z-index: 9999999; padding: 20px; border-radius: 12px; font-weight: bold; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); {{ $styles[$type] ?? $styles['success'] }}">
    <div style="display: flex; align-items: center; gap: 15px;">
        <div
            style="background: rgba(255,255,255,0.2); width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            @if($type === 'success')
                ✓
            @else
                ✕
            @endif
        </div>
        <div>
            {{ $message }}
        </div>
        <button onclick="this.parentElement.parentElement.remove()"
            style="margin-left: 20px; background: none; border: none; color: white; cursor: pointer; font-size: 20px;">&times;</button>
    </div>
</div>

<script>
    console.log("Toast component rendered at: " + new Date().toLocaleTimeString());
</script>