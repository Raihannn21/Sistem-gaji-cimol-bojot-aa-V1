<template x-teleport="body">
    <div x-show="showConfirmModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-400/50 backdrop-blur-sm p-4" 
         x-cloak>
        
        <div @click.away="!processing && (showConfirmModal = false)" 
             x-show="showConfirmModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-lg rounded-3xl bg-white p-6 shadow-xl dark:bg-gray-900 sm:p-8">
            
            <div class="text-center">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-500/10">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>

                <h3 class="text-2xl font-bold text-gray-800 dark:text-white/90">Konfirmasi Generate (PKWT)</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Anda akan memproses gaji untuk <span class="font-bold text-gray-700 dark:text-gray-200">45 Karyawan</span> periode <span class="font-bold text-gray-700 dark:text-gray-200">Juli 2025</span>.
                </p>

                <div class="mt-6 rounded-2xl bg-gray-50 p-4 text-left dark:bg-white/[0.03]">
                    <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-800">
                        <span class="text-xs text-gray-500">Estimasi Total Pengeluaran PKWT:</span>
                        <span class="text-xs font-bold text-gray-800 dark:text-white">Rp 210.500.000</span>
                    </div>
                    <div class="mt-2 flex items-start gap-2">
                        <svg class="mt-0.5 h-4 w-4 text-yellow-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-[10px] text-yellow-600 leading-tight italic">Pastikan seluruh data lembur, kehadiran, dan tunjangan telah divalidasi sebelum melanjutkan. Slip gaji akan otomatis dibuat setelah proses ini.</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-col gap-3">
                    <button @click="generate()" 
                            :disabled="processing"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 py-3.5 text-sm font-bold text-white transition-all hover:bg-brand-700 disabled:opacity-50">
                        <template x-if="processing">
                            <svg class="h-5 w-5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="processing ? 'Sedang Memproses...' : 'Ya, Generate Gaji PKWT'"></span>
                    </button>
                    
                    <button @click="showConfirmModal = false" 
                            :disabled="processing"
                            class="w-full py-3 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 disabled:opacity-50">
                        Batalkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
