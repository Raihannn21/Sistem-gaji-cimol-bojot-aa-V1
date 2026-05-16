@php
    $toastArr = session('toast');
    $message = session('success') ?? session('status') ?? session('toast_message') ?? (is_array($toastArr) ? ($toastArr['message'] ?? null) : null);
    $type = session('error') ? 'error' : (session('toast_type') ?? ($toastArr['type'] ?? 'success'));

    // Tangkap juga error validasi
    if (!$message && $errors->any()) {
        $message = "Ada kesalahan input. Silakan cek kembali.";
        $type = 'error';
    }

    $bg = $type === 'success' ? '#059669' : '#dc2626';
    $shadow = $type === 'success' ? 'rgba(5, 150, 105, 0.4)' : 'rgba(220, 38, 38, 0.4)';
@endphp

@if ($message)
    <div id="app-toast" 
         class="animate-toast-in"
         style="position: fixed; top: 30px; right: 30px; z-index: 99999999; display: flex; align-items: center; gap: 16px; background: {{ $bg }}; color: white; padding: 16px 28px; border-radius: 24px; box-shadow: 0 20px 25px -5px {{ $shadow }}, 0 10px 10px -5px rgba(0, 0, 0, 0.1); font-family: 'Outfit', sans-serif;">
        
        <div style="flex-shrink: 0; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);">
            @if($type === 'success')
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
            @else
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
            @endif
        </div>

        <div style="padding-right: 8px;">
            <p style="margin: 0; font-size: 15px; font-weight: 800; letter-spacing: 0.025em; line-height: 1.2;">
                {{ $message }}
            </p>
        </div>

        <button onclick="closeToast()" style="margin-left: 8px; background: none; border: none; color: white; cursor: pointer; padding: 6px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='none'">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <style>
        @keyframes toast-in {
            0% { transform: translateY(-50px) scale(0.9); opacity: 0; }
            100% { transform: translateY(0) scale(1); opacity: 1; }
        }
        @keyframes toast-out {
            0% { transform: translateY(0) scale(1); opacity: 1; }
            100% { transform: translateY(-50px) scale(0.9); opacity: 0; }
        }
        .animate-toast-in {
            animation: toast-in 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
        .animate-toast-out {
            animation: toast-out 0.4s cubic-bezier(0.6, -0.28, 0.735, 0.045) forwards;
        }
    </style>

    <script>
        function closeToast() {
            const t = document.getElementById('app-toast');
            if(t) {
                t.className = 'animate-toast-out';
                setTimeout(() => t.remove(), 400);
            }
        }
        setTimeout(closeToast, 5000);
    </script>
@endif