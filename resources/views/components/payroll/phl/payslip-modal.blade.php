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
            class="relative w-[480px] max-w-full rounded-3xl bg-white p-6 shadow-xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 flex flex-col max-h-[85vh]">

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
            <div class="flex-1 overflow-y-auto pr-1.5 custom-scrollbar my-2" id="payslip-content">
                <!-- Header Info (Corporate Logo/Name and Title) -->
                <div class="text-center mb-4 mt-2">
                    <h2 class="text-base font-extrabold tracking-wider text-gray-900 dark:text-white">CIMOL BOJOT AA
                    </h2>
                    <div class="border-b border-gray-300 dark:border-gray-700 my-2"></div>
                    <h1 class="text-sm font-extrabold text-gray-800 dark:text-white uppercase tracking-widest">SLIP GAJI
                        KARYAWAN (PHL)</h1>
                    <p class="text-[10px] text-gray-500 font-semibold" x-text="'Periode: ' + selectedSlip.period_title">
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
                        <span class="text-gray-500 font-medium w-20 shrink-0">Klasifikasi</span>
                        <span class="text-gray-500">:</span>
                        <span class="font-semibold text-gray-850 dark:text-gray-300">Pekerja Harian Lepas (PHL)</span>
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
                                    <span class="font-bold text-gray-800 dark:text-gray-200">Gaji Pokok Harian</span>
                                    <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-0.5"
                                        x-text="selectedSlip.days_worked + ' Hari Kerja x Rp ' + Number(selectedSlip.salary_daily).toLocaleString('id-ID')">
                                    </p>
                                </div>
                                <span class="font-bold text-gray-900 dark:text-white tabular-nums shrink-0"
                                    x-text="'Rp ' + Number(selectedSlip.pokok).toLocaleString('id-ID')"></span>
                            </div>
                            <div class="flex justify-between items-start gap-4">
                                <div class="min-w-0">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">Upah Lembur</span>
                                    <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-0.5">Lembur tervalidasi
                                        sistem</p>
                                </div>
                                <span class="font-bold text-gray-900 dark:text-white tabular-nums shrink-0"
                                    x-text="'Rp ' + Number(selectedSlip.lembur).toLocaleString('id-ID')"></span>
                            </div>
                            <div class="flex justify-between items-start gap-4">
                                <div class="min-w-0">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">Tunjangan Risiko</span>
                                    <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-0.5">Tunjangan risiko
                                        operasional</p>
                                </div>
                                <span class="font-bold text-gray-900 dark:text-white tabular-nums shrink-0"
                                    x-text="'Rp ' + Number(selectedSlip.risiko).toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                    </div>

                    <!-- II. POTONGAN -->
                    <div class="space-y-2">
                        <div
                            class="font-bold text-gray-900 dark:text-white border-b border-gray-300 dark:border-gray-700 pb-1 uppercase tracking-wider text-[10px]">
                            II. Potongan Gaji
                        </div>
                        <div class="space-y-2 px-1">
                            <div class="flex justify-between items-center gap-4">
                                <div class="min-w-0">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">Potongan Terdaftar</span>
                                    <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-0.5">Tidak ada pemotongan
                                        terdaftar</p>
                                </div>
                                <span class="font-bold text-gray-900 dark:text-white tabular-nums shrink-0">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Final Calculation (Formal Double-Underline Accounting Style) -->
                <div class="mt-6 pt-3 border-t border-gray-300 dark:border-gray-700">
                    <div class="flex items-center justify-between font-bold text-xs py-1">
                        <span class="text-gray-900 dark:text-white uppercase tracking-wider text-[10px]">TOTAL DITERIMA
                            (TAKE HOME PAY)</span>
                        <span class="text-base font-extrabold text-brand-600 dark:text-brand-400 tabular-nums"
                            x-text="'Rp ' + Number(selectedSlip.total).toLocaleString('id-ID')"></span>
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
                <x-ui.button variant="outline"
                    @click="alert('Slip gaji karyawan ' + selectedSlip.name + ' berhasil dikirim ke email!')"
                    className="w-full sm:w-auto text-[11px] py-1.5 px-3 text-brand-600 border-brand-100 hover:bg-brand-50 flex items-center justify-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Kirim Email
                </x-ui.button>
                <x-ui.button variant="primary" onclick="window.print()"
                    className="w-full sm:w-auto text-[11px] py-1.5 px-3 flex items-center justify-center gap-1 bg-brand-500 text-white hover:bg-brand-600">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Slip
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