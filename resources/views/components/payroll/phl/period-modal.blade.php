<template x-teleport="body">
    <div x-show="showModal" 
         class="modal fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5" 
         style="display: none;"
         x-cloak>
        
        <!-- Backdrop (Exact copy of other modal backdrops with backdrop-blur-sm) -->
        <div @click="showModal = false" class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        </div>

        <!-- Modal Content Box (Exact copy of other modals with max-w-[700px] sizing) -->
        <div @click.stop class="relative w-full rounded-3xl bg-white dark:bg-gray-900 max-w-[700px] shadow-2xl border border-gray-100 dark:border-gray-800"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95">
            
            <!-- Inner Wrapper (Exact copy of other modal inner wrappers for perfect styling) -->
            <div class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
                
                <!-- Close Button -->
                <button @click="showModal = false" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 z-50 transition-colors duration-150">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
                </button>

                <!-- Header (Borderless & Spaced) -->
                <div class="px-2 pr-14 mb-6">
                    <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Buka Periode Gaji Baru (PHL)</h4>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tentukan rentang tanggal untuk mengaktifkan periode penggajian Pekerja Harian Lepas (PHL).</p>
                </div>

                <!-- Form Content -->
                <form action="{{ route('payroll.phl.periods.store') }}" method="POST" class="flex flex-col">
                    @csrf
                    
                    <div class="custom-scrollbar max-h-[458px] overflow-y-auto p-2 space-y-6 pb-20">
                        <div class="space-y-5">
                                <!-- Judul Periode -->
                                <x-form.input label="Judul Periode" name="title" placeholder="Contoh: Gaji PHL Juli 2025" required />

                                <!-- Rentang Tanggal -->
                                <x-form.date-picker name="date_range" label="Rentang Tanggal" placeholder="Pilih Rentang Tanggal"
                                    mode="range" dateFormat="d-m-Y" :static="true" required />

                            <div class="rounded-xl bg-brand-50 p-4 dark:bg-brand-500/10 border border-brand-100 dark:border-brand-500/20">
                                <div class="flex gap-3">
                                    <svg class="h-5 w-5 text-brand-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs text-brand-700 dark:text-brand-400 leading-relaxed">
                                        Membuka periode baru akan mengaktifkan modul absensi dan lembur. Pastikan periode sebelumnya sudah selesai.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer (Borderless Border-t Spacing) -->
                    <div class="flex items-center justify-end gap-3 px-2 mt-6 border-t border-gray-100 dark:border-gray-800 pt-5">
                        <button type="button" @click="showModal = false" class="flex justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] transition-colors duration-150">
                            Batal
                        </button>
                        <button type="submit" class="flex justify-center rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 shadow-lg shadow-brand-500/10 transition-all duration-150">
                            Buka Periode
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>