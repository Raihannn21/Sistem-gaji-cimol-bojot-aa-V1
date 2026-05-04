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
             class="relative w-full max-w-lg rounded-3xl bg-white p-6 shadow-xl dark:bg-gray-900 sm:p-8">
            
            <button @click="showDetailModal = false" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
            </button>

            <div class="flex flex-col items-center text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-brand-500/10 text-brand-500">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3 class="mt-4 text-xl font-bold text-gray-800 dark:text-white/90" x-text="selectedEmployee.name"></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedEmployee.role"></p>
                
                <span class="mt-3 inline-block rounded-full px-3 py-1 text-xs font-medium" :class="getStatusClass(selectedEmployee.status)" x-text="selectedEmployee.status"></span>
            </div>

            <div class="mt-8 space-y-4">
                <div class="flex justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
                    <span class="text-sm text-gray-500 dark:text-gray-400">NRP</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white" x-text="selectedEmployee.nrp"></span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Tim</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white" x-text="selectedEmployee.team"></span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Lokasi</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white" x-text="selectedEmployee.location"></span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Gaji Pokok</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white" x-text="'Rp ' + selectedEmployee.salary"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Rekening</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white" x-text="selectedEmployee.bank"></span>
                </div>
            </div>

            <div class="mt-8">
                <x-ui.button variant="outline" className="w-full" @click="showDetailModal = false">Tutup</x-ui.button>
            </div>
        </div>
    </div>
</template>
