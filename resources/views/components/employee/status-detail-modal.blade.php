<template x-teleport="body">
    <div x-show="showDetailModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-400/50 backdrop-blur-sm p-4" 
         x-cloak>
        
        <div @click.away="showDetailModal = false" 
             x-show="showDetailModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-[500px] max-w-full rounded-3xl bg-white p-6 shadow-xl dark:bg-gray-900 sm:p-8 max-h-[90vh] overflow-y-auto custom-scrollbar">
            
            <button @click="showDetailModal = false" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
            </button>

            <div class="flex flex-col items-center text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-red-50 text-red-500 dark:bg-red-500/10" :class="selectedItem.type === 'SPHK' ? 'bg-yellow-50 text-yellow-600 dark:bg-yellow-500/10' : ''">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </div>
                <h3 class="mt-4 text-xl font-bold text-gray-800 dark:text-white/90">Detail Perubahan Status</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Informasi lengkap pemberhentian karyawan.</p>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-y-4">
                <div class="rounded-2xl bg-gray-50 p-4 dark:bg-white/[0.03]">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Karyawan</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white" x-text="selectedItem.name + ' (' + selectedItem.emp_no + ')'"></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedItem.role"></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-gray-100 p-4 dark:border-gray-800">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Jenis Status</p>
                        <span class="mt-2 inline-block rounded-full px-2.5 py-0.5 text-xs font-medium" :class="getStatusClass(selectedItem.type)" x-text="selectedItem.type"></span>
                    </div>
                    <div class="rounded-2xl border border-gray-100 p-4 dark:border-gray-800">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal Efektif</p>
                        <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white" x-text="selectedItem.date"></p>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-100 p-4 dark:border-gray-800">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Tim & Lokasi</p>
                    <p class="mt-1 text-sm text-gray-800 dark:text-white" x-text="selectedItem.team + ' - ' + selectedItem.location"></p>
                </div>

                <div class="rounded-2xl border border-gray-100 p-4 dark:border-gray-800">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Alasan Pemberhentian</p>
                    <p class="mt-1 text-sm text-gray-800 dark:text-white leading-relaxed" x-text="selectedItem.reason"></p>
                </div>
            </div>

            <div class="mt-8">
                <x-ui.button variant="outline" className="w-full" @click="showDetailModal = false">Tutup</x-ui.button>
            </div>
        </div>
    </div>
</template>
