<template x-teleport="body">
    <div x-show="showSlipModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-400/50 backdrop-blur-sm p-4 overflow-y-auto" 
         x-cloak>
        
        <div @click.away="showSlipModal = false" 
             x-show="showSlipModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-[500px] max-w-full my-8 rounded-3xl bg-white p-6 shadow-xl dark:bg-gray-900 sm:p-10">
            
            <button @click="showSlipModal = false" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 sm:right-6 sm:top-6 no-print">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
            </button>

            <!-- Payslip Content (Calculator Style) -->
            <div id="payslip-content" class="dark:text-white/90">
                <!-- Header Info -->
                <div class="text-center mb-10">
                    <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter italic uppercase" x-text="'SLIP GAJI ' + (selectedSlip.type ? selectedSlip.type : 'KARYAWAN')"></h1>
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-widest mt-1" x-text="'Periode ' + selectedSlip.period"></p>
                </div>

                <div class="flex justify-between items-end border-b border-gray-100 pb-6 mb-8 dark:border-gray-800">
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Karyawan</p>
                        <h4 class="text-lg font-black text-gray-800 dark:text-white" x-text="selectedSlip.name"></h4>
                        <p class="text-xs text-gray-500 font-medium" x-text="'NRP. ' + selectedSlip.nrp"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</p>
                        <p class="text-xs font-black text-green-600 uppercase italic">Finalized</p>
                    </div>
                </div>

                <!-- Calculator Body -->
                <div class="space-y-6">
                    <!-- Section: Earnings -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 text-xs font-bold text-brand-600 uppercase tracking-widest">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Penerimaan
                        </div>
                        <div class="space-y-3 pl-6 border-l-2 border-brand-100 dark:border-brand-500/20">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Gaji Pokok</span>
                                <span class="text-sm font-bold text-gray-800 dark:text-white tabular-nums" x-text="'Rp ' + (selectedSlip.detail ? new Intl.NumberFormat('id-ID').format(selectedSlip.detail.gaji_pokok) : '0')"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Lembur</span>
                                <span class="text-sm font-bold text-gray-800 dark:text-white tabular-nums" x-text="'Rp ' + (selectedSlip.detail ? new Intl.NumberFormat('id-ID').format(selectedSlip.detail.lembur) : '0')"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Tunjangan</span>
                                <span class="text-sm font-bold text-gray-800 dark:text-white tabular-nums" x-text="'Rp ' + (selectedSlip.detail ? new Intl.NumberFormat('id-ID').format(selectedSlip.detail.tunjangan) : '0')"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Deductions -->
                    <div class="space-y-4" x-show="selectedSlip.type === 'PKWT'">
                        <div class="flex items-center gap-2 text-xs font-bold text-red-500 uppercase tracking-widest">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            Potongan
                        </div>
                        <div class="space-y-3 pl-6 border-l-2 border-red-100 dark:border-red-500/20">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">BPJS Kesehatan</span>
                                <span class="text-sm font-bold text-red-500 tabular-nums" x-text="'(Rp ' + (selectedSlip.detail ? new Intl.NumberFormat('id-ID').format(selectedSlip.detail.bpjs_kesehatan) : '0') + ')'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">BPJS Ketenagakerjaan</span>
                                <span class="text-sm font-bold text-red-500 tabular-nums" x-text="'(Rp ' + (selectedSlip.detail ? new Intl.NumberFormat('id-ID').format(selectedSlip.detail.bpjs_tk) : '0') + ')'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Pajak PPh21</span>
                                <span class="text-sm font-bold text-red-500 tabular-nums" x-text="'(Rp ' + (selectedSlip.detail ? new Intl.NumberFormat('id-ID').format(selectedSlip.detail.pajak) : '0') + ')'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Final Calculation -->
                <div class="mt-10 pt-8 border-t-2 border-dashed border-gray-200 dark:border-gray-800">
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Penerimaan Bersih</p>
                            <p class="text-xs text-gray-500 italic">Gaji Akhir yang Diterima Karyawan</p>
                        </div>
                        <div class="text-right">
                            <h2 class="text-3xl font-black text-brand-600 tabular-nums dark:text-brand-500" x-text="'Rp ' + selectedSlip.total"></h2>
                        </div>
                    </div>
                </div>

                <!-- Footer Sign -->
                <div class="mt-12 pt-10 border-t border-gray-50 dark:border-gray-800 flex justify-between items-center italic text-[10px] text-gray-400">
                    <p>PT. CIMOL BOJOT AA - Sistem Payroll Otomatis</p>
                    <p>Dokumen ini sah dan diterbitkan secara digital.</p>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="mt-10 flex justify-end gap-3 no-print border-t border-gray-100 pt-6 dark:border-gray-800">
                <x-ui.button variant="outline" @click="showSlipModal = false">Tutup</x-ui.button>
                <x-ui.button variant="primary" onclick="window.print()" className="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak Slip
                </x-ui.button>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            #payslip-content, #payslip-content * { visibility: visible; color: black !important; }
            #payslip-content { position: absolute; left: 0; top: 0; width: 100%; padding: 30px; }
            .no-print { display: none !important; }
        }
    </style>
</template>
