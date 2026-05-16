@props([
    'id' => 'delete-modal',
    'title' => 'Konfirmasi Hapus',
    'message' => 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
    'confirmText' => 'Ya, Hapus',
    'cancelText' => 'Batal'
])

<div x-data="{ 
        open: false, 
        action: '',
        message: '{{ $message }}',
        openModal(url, msg = null) {
            this.action = url;
            if(msg) this.message = msg;
            this.open = true;
        }
     }"
     @open-delete-modal.window="openModal($event.detail.url, $event.detail.message)"
     class="hidden">
    
    <template x-teleport="body">
        <div x-show="open" 
             class="fixed inset-0 flex items-center justify-center p-4" 
             style="z-index: 99999999; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;"
             x-cloak>
            
            <!-- Backdrop -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0"
                 style="position: fixed; inset: 0; background: rgba(156, 163, 175, 0.5); backdrop-filter: blur(4px);"
                 @click="open = false"></div>

            <!-- Modal Content (Pakai Inline Style untuk Border Radius agar TIDAK RUNCING) -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 overflow-hidden"
                 style="width: 500px; max-width: 100%; border-radius: 32px; border: 1px solid rgba(0,0,0,0.05);"
                 @click.away="open = false">
                
                <!-- Close Button -->
                <button @click="open = false"
                    style="position: absolute; right: 20px; top: 20px; width: 40px; height: 40px; background: #f3f4f6; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #9ca3af; transition: all 0.3s;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z"
                            fill="currentColor" />
                    </svg>
                </button>

                <!-- Icon & Header -->
                <div style="display: flex; flex-direction: column;">
                    <div style="width: 56px; height: 56px; background: #fef2f2; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                        <svg style="width: 28px; height: 28px; color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 style="margin: 0; font-size: 20px; font-weight: 700; color: #1f2937;" x-text="'{{ $title }}'"></h3>
                    <p style="margin: 8px 0 0 0; font-size: 14px; color: #6b7280; line-height: 1.5;" x-text="message"></p>
                </div>

                <!-- Warning Box -->
                <div style="margin-top: 24px; padding: 16px; background: #fef2f2; border: 1px solid #fee2e2; border-radius: 16px; display: flex; gap: 12px;">
                    <svg style="width: 20px; height: 20px; color: #dc2626; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p style="margin: 0; font-size: 12px; color: #b91c1c; font-weight: 500; line-height: 1.4;">
                        Data yang dihapus akan hilang secara permanen dari sistem dan tidak dapat dikembalikan.
                    </p>
                </div>

                <!-- Footer Buttons -->
                <div style="display: flex; gap: 12px; margin-top: 32px;">
                    <button type="button" @click="open = false" 
                            style="flex: 1; padding: 12px; background: white; border: 1px solid #e5e7eb; border-radius: 16px; color: #374151; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        {{ $cancelText }}
                    </button>
                    
                    <form :action="action" method="POST" style="flex: 1;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                style="width: 100%; padding: 12px; background: #dc2626; border: none; border-radius: 16px; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                                onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                            {{ $confirmText }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
