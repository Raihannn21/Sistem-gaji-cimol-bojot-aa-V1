<template x-teleport="body">
    <div x-show="showSlipModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-400/50 backdrop-blur-sm p-4" x-cloak>

        <!-- Modal Container -->
        <div @click.away="showSlipModal = false" x-show="showSlipModal"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative rounded-3xl bg-white p-6 shadow-xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 flex flex-col" style="width: 480px; max-width: 100%; max-height: 85vh;">

            <!-- Close Button (Fixed) -->
            <button @click="showSlipModal = false"
                class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition-all duration-200 no-print z-10">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <!-- Scrollable Content Body (Formal Corporate Slip Style) -->
            <div class="flex-1 pr-1.5 custom-scrollbar my-2" style="overflow-y: auto;" id="payslip-content">
                <!-- Header Info (Corporate Logo/Name and Title) -->
                <div class="text-center mb-4 mt-2">
                    <img src="{{ asset('images/logo/logo-cimol-bojot-aa.png') }}"
                        class="h-12 mx-auto mb-2 dark:brightness-110" alt="Logo PT Cimol Bojot AA">
                    <h2 class="text-xs font-bold tracking-widest text-gray-500 dark:text-gray-400 uppercase">PT. CIMOL BOJOT AA</h2>
                    <div class="border-b border-gray-300 dark:border-gray-700 my-2"></div>
                    <h1 class="text-sm font-extrabold text-gray-800 dark:text-white uppercase tracking-widest"
                        x-text="'SLIP GAJI KARYAWAN (' + (selectedSlip.type ? selectedSlip.type : 'KARYAWAN') + ')'"></h1>
                    <p class="text-[10px] text-gray-500 font-semibold" x-text="'Periode: ' + selectedSlip.period">
                    </p>
                </div>

                <!-- Employee Details (Formal Vertical List) -->
                <div
                    class="space-y-2 text-xs border border-gray-200 dark:border-gray-800 rounded-lg p-3 bg-gray-50/30 dark:bg-white/[0.01] mb-5">
                    <div class="flex gap-1.5 items-center">
                        <span class="text-gray-500 font-medium w-20 shrink-0">Nama</span>
                        <span class="text-gray-500">:</span>
                        <span class="font-bold text-gray-900 dark:text-white truncate"
                            x-text="selectedSlip.name"></span>
                    </div>
                    <div class="flex gap-1.5 items-center">
                        <span class="text-gray-500 font-medium w-20 shrink-0">ID</span>
                        <span class="text-gray-500">:</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200"
                            x-text="selectedSlip.nrp"></span>
                    </div>
                    <div class="flex gap-1.5 items-center">
                        <span class="text-gray-500 font-medium w-20 shrink-0">Klasifikasi</span>
                        <span class="text-gray-500">:</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200"
                            x-text="selectedSlip.type === 'PKWT' ? 'Karyawan Kontrak (PKWT)' : 'Pekerja Harian Lepas (PHL)'"></span>
                    </div>
                </div>

                <!-- Salary Breakdown (Formal Accounting Table style) -->
                <div class="space-y-4 text-xs">
                    <!-- I. PENERIMAAN -->
                    <div class="space-y-2">
                        <div
                            class="font-bold text-gray-900 dark:text-white border-b border-gray-300 dark:border-gray-700 pb-1 uppercase tracking-wider text-[10px]">
                            I. Penerimaan / Penghasilan
                        </div>
                        <div class="space-y-2 px-1">
                            <div class="flex justify-between items-start gap-4">
                                <div class="min-w-0">
                                    <span class="font-bold text-gray-800 dark:text-gray-200"
                                        x-text="selectedSlip.type === 'PKWT' ? 'Gaji Pokok Prorata' : 'Gaji Pokok Harian'"></span>
                                    <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-0.5"
                                        x-text="selectedSlip.type === 'PKWT' ? (selectedSlip.detail ? (selectedSlip.detail.days_worked + ' Hari Kerja / ' + selectedSlip.detail.total_days + ' Hari Kalender x Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(selectedSlip.detail.tarif_harian))) : '') : (selectedSlip.detail ? (selectedSlip.detail.days_worked + ' Hari Kerja x Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(selectedSlip.detail.salary_daily))) : '')"></p>
                                </div>
                                <span class="font-bold text-gray-900 dark:text-white tabular-nums shrink-0"
                                    x-text="'Rp ' + (selectedSlip.detail ? new Intl.NumberFormat('id-ID').format(Math.round(selectedSlip.detail.gaji_pokok)) : '0')"></span>
                            </div>
                            <div class="flex justify-between items-start gap-4" x-show="selectedSlip.detail && selectedSlip.detail.lembur > 0">
                                <div class="min-w-0">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">Upah Kerja Lembur</span>
                                    <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-0.5">Lembur tervalidasi sistem</p>
                                </div>
                                <span class="font-bold text-gray-900 dark:text-white tabular-nums shrink-0"
                                    x-text="'Rp ' + (selectedSlip.detail ? new Intl.NumberFormat('id-ID').format(Math.round(selectedSlip.detail.lembur)) : '0')"></span>
                            </div>
                            <div class="flex justify-between items-start gap-4" x-show="selectedSlip.detail && selectedSlip.detail.tunjangan_risiko > 0">
                                <div class="min-w-0">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">Tunjangan Risiko</span>
                                    <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-0.5">Tunjangan risiko operasional</p>
                                </div>
                                <span class="font-bold text-gray-900 dark:text-white tabular-nums shrink-0"
                                    x-text="'Rp ' + (selectedSlip.detail ? new Intl.NumberFormat('id-ID').format(Math.round(selectedSlip.detail.tunjangan_risiko)) : '0')"></span>
                            </div>
                            <div class="flex justify-between items-start gap-4" x-show="selectedSlip.detail && selectedSlip.detail.tunjangan_lainnya > 0">
                                <div class="min-w-0">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">Tunjangan Lainnya</span>
                                    <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-0.5">Tunjangan operasional lainnya</p>
                                </div>
                                <span class="font-bold text-gray-900 dark:text-white tabular-nums shrink-0"
                                    x-text="'Rp ' + (selectedSlip.detail ? new Intl.NumberFormat('id-ID').format(Math.round(selectedSlip.detail.tunjangan_lainnya)) : '0')"></span>
                            </div>
                        </div>
                    </div>

                    <!-- II. POTONGAN (Only for PKWT) -->
                    <div class="space-y-2" x-show="selectedSlip.type === 'PKWT'">
                        <div
                            class="font-bold text-gray-900 dark:text-white border-b border-gray-300 dark:border-gray-700 pb-1 uppercase tracking-wider text-[10px]">
                            II. Potongan Gaji / Deduksi
                        </div>
                        <div class="space-y-2 px-1">
                            <div class="flex justify-between items-center gap-4" x-show="selectedSlip.detail && selectedSlip.detail.bpjs_kesehatan > 0">
                                <div class="min-w-0">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">BPJS Kesehatan</span>
                                    <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-0.5">Iuran Jaminan Kesehatan</p>
                                </div>
                                <span class="font-bold text-red-600 dark:text-red-400 tabular-nums shrink-0"
                                    x-text="'-Rp ' + (selectedSlip.detail ? new Intl.NumberFormat('id-ID').format(Math.round(selectedSlip.detail.bpjs_kesehatan)) : '0')"></span>
                            </div>
                            <div class="flex justify-between items-center gap-4" x-show="selectedSlip.detail && selectedSlip.detail.bpjs_tk > 0">
                                <div class="min-w-0">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">BPJS Ketenagakerjaan</span>
                                    <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-0.5">Iuran Jaminan Sosial</p>
                                </div>
                                <span class="font-bold text-red-600 dark:text-red-400 tabular-nums shrink-0"
                                    x-text="'-Rp ' + (selectedSlip.detail ? new Intl.NumberFormat('id-ID').format(Math.round(selectedSlip.detail.bpjs_tk)) : '0')"></span>
                            </div>
                            <div class="flex justify-between items-center gap-4" x-show="selectedSlip.detail && selectedSlip.detail.pajak > 0">
                                <div class="min-w-0">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">Potongan PPh 21</span>
                                    <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-0.5">Pajak Penghasilan Pasal 21</p>
                                </div>
                                <span class="font-bold text-red-600 dark:text-red-400 tabular-nums shrink-0"
                                    x-text="'-Rp ' + (selectedSlip.detail ? new Intl.NumberFormat('id-ID').format(Math.round(selectedSlip.detail.pajak)) : '0')"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Final Calculation (Double-Underline) -->
                <div class="mt-6 pt-3 border-t border-gray-300 dark:border-gray-700">
                    <div class="flex items-center justify-between font-bold text-xs py-1">
                        <span class="text-gray-900 dark:text-white uppercase tracking-wider text-[10px]">TOTAL DITERIMA</span>
                        <span class="text-base font-extrabold text-brand-600 dark:text-brand-400 tabular-nums"
                            x-text="'Rp ' + selectedSlip.total"></span>
                    </div>
                    <!-- Double line under Take Home Pay (Standard Accounting Practice) -->
                    <div class="border-b-4 border-double border-gray-900 dark:border-gray-700 mt-0.5"></div>
                </div>
            </div>

            <!-- Fixed Footer Actions -->
            <div
                class="mt-4 flex flex-col gap-2 sm:flex-row sm:justify-end no-print border-t border-gray-200 pt-4 dark:border-gray-800 shrink-0">
                <x-ui.button variant="outline" @click="showSlipModal = false"
                    className="w-full sm:w-auto text-[11px] py-1.5 px-3">Tutup</x-ui.button>
                <x-ui.button variant="primary"
                    @click="window.print()"
                    className="w-full sm:w-auto text-[11px] py-1.5 px-3 flex items-center justify-center gap-1 bg-brand-500 text-white hover:bg-brand-600">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Slip Gaji
                </x-ui.button>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #payslip-content,
            #payslip-content * {
                visibility: visible;
                color: black !important;
            }

            #payslip-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 30px;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</template>
