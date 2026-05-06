<template x-teleport="body">
    <div x-show="showSlipModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-400/50 backdrop-blur-sm p-4" x-cloak>

        <div @click.away="showSlipModal = false" x-show="showSlipModal"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-xl rounded-3xl bg-white p-6 shadow-xl dark:bg-gray-900 sm:p-8">

            <!-- Close Button -->
            <button @click="showSlipModal = false"
                class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z"
                        fill="currentColor" />
                </svg>
            </button>

            <!-- Header -->
            <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Detail Slip Gaji</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Periode: Juli 2025 - Cimol Bojot AA</p>

            <div class="mt-8 space-y-6">
                <!-- Employee Card Info -->
                <div
                    class="flex items-center justify-between p-5 rounded-2xl bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-4">
                        <div
                            class="h-12 w-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-brand-600 font-black text-xl dark:bg-gray-800">
                            <span x-text="selectedEmployee.name.charAt(0)"></span>
                        </div>
                        <div>
                            <p class="text-base font-bold text-gray-800 dark:text-white" x-text="selectedEmployee.name">
                            </p>
                            <p class="text-[10px] font-medium text-gray-400"
                                x-text="'ID Karyawan: ' + selectedEmployee.nrp"></p>
                        </div>
                    </div>
                </div>

                <!-- Financial Details -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] font-black uppercase tracking-widest text-brand-600">Rincian
                            Penghasilan</span>
                        <div class="h-px flex-1 bg-gray-100 dark:bg-gray-800"></div>
                    </div>

                    <div class="space-y-3 px-1">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Upah Pokok</span>
                            <span class="font-bold text-gray-900 dark:text-white">Rp 3.500.000</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Lembur (15 Jam)</span>
                            <span class="font-bold text-gray-900 dark:text-white">Rp 450.000</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Tunjangan Risiko (5 Kali)</span>
                            <span class="font-bold text-gray-900 dark:text-white">Rp 100.000</span>
                        </div>
                    </div>

                    <!-- Total Section -->
                    <div class="mt-6 pt-6 border-t border-dashed border-gray-200 dark:border-gray-800">
                        <div class="flex flex-col sm:flex-row justify-between items-end gap-4">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1">Total
                                    Diterima (THP)</p>
                                <h2 class="text-3xl font-black text-gray-950 dark:text-white tracking-tighter">Rp
                                    4.050.000</h2>
                            </div>
                            <div
                                class="px-4 py-2 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-gray-800">
                                <p class="text-[10px] italic text-gray-400 font-medium leading-tight">
                                    "Empat Juta Lima Puluh Ribu Rupiah"
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons (Matched with Template) -->
                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                    <x-ui.button variant="outline" @click="window.print()">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        CETAK SLIP
                    </x-ui.button>
                    <x-ui.button variant="primary" @click="showSlipModal = false">
                        TUTUP
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
</template>