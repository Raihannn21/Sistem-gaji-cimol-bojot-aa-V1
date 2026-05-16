<div x-show="showDetailModal" 
         x-data="{
            formatPrice(val) {
                if (!val) return '0';
                let cleanVal = val.toString().replace(/\D/g, '');
                return new Intl.NumberFormat('id-ID').format(cleanVal);
            }
         }"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-400/50 backdrop-blur-sm p-4" 
         x-cloak>
        
        <!-- Modal Box -->
        <div @click.away="showDetailModal = false" 
             class="relative flex flex-col w-full max-w-md h-[480px] rounded-3xl bg-white shadow-xl dark:bg-gray-900 overflow-hidden border border-gray-100 dark:border-gray-800">
            
            <!-- Close Button -->
            <button @click="showDetailModal = false" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 z-50">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
            </button>

            <!-- Header -->
            <div class="p-6 sm:p-8 pb-0 flex-shrink-0 bg-white dark:bg-gray-900 text-center">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Profil Karyawan</h3>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6 sm:p-8 custom-scrollbar bg-white dark:bg-gray-900">
                <div class="flex flex-col items-center text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-500/10 text-brand-500 mb-3">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white/90" x-text="selectedEmployee.name"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedEmployee.role"></p>
                    <span class="mt-2 inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider" :class="getStatusClass(selectedEmployee.status)" x-text="selectedEmployee.status"></span>
                </div>

                <div class="mt-6 space-y-3">
                    <!-- Standard Fields -->
                    <template x-for="field in [
                        { label: 'Emp No', value: selectedEmployee.emp_no },
                        { label: 'No. ID', value: selectedEmployee.id_no || '-' },
                        { label: 'NIK', value: selectedEmployee.nik || '-' },
                        { label: 'Email', value: selectedEmployee.email || '-' },
                        { label: 'Telepon', value: selectedEmployee.phone || '-' },
                        { label: 'Tim', value: selectedEmployee.team },
                        { label: 'Lokasi', value: selectedEmployee.location },
                        { label: 'Gaji Pokok', value: 'Rp ' + formatPrice(selectedEmployee.salary) },
                        { label: 'Nama Bank', value: selectedEmployee.bank_name || '-' },
                        { label: 'No. Rekening', value: selectedEmployee.bank_account || '-' }
                    ]">
                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-800">
                            <span class="text-[11px] text-gray-500 dark:text-gray-400" x-text="field.label"></span>
                            <span class="text-[11px] font-bold text-gray-800 dark:text-white" x-text="field.value"></span>
                        </div>
                    </template>

                    <!-- Conditional: PHL Details -->
                    <div x-show="selectedEmployee.role === 'PHL'" class="mt-4 pt-4 border-t border-dashed border-gray-200 dark:border-gray-800">
                        <div class="flex justify-between pb-2">
                            <span class="text-[11px] text-brand-500 font-bold uppercase tracking-wider italic">Tunjangan Risiko</span>
                            <span class="text-[11px] font-bold text-gray-800 dark:text-white" x-text="'Rp ' + formatPrice(selectedEmployee.risk_allowance)"></span>
                        </div>
                    </div>

                    <!-- Conditional: PKWT Details -->
                    <div x-show="selectedEmployee.role === 'PKWT'" class="mt-4 pt-4 border-t border-dashed border-gray-200 dark:border-gray-800 space-y-2">
                        <p class="text-[10px] font-bold text-brand-500 uppercase italic">Potongan PKWT:</p>
                        <template x-for="field in [
                            { label: 'BPJS Kesehatan', value: formatPrice(selectedEmployee.bpjs_health) },
                            { label: 'BPJS TK', value: formatPrice(selectedEmployee.bpjs_tk) },
                            { label: 'PPH 21', value: formatPrice(selectedEmployee.pph21) },
                            { label: 'Tunjangan Risiko', value: formatPrice(selectedEmployee.risk_allowance) }
                        ]">
                            <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-800 last:border-0">
                                <span class="text-[11px] text-gray-500 dark:text-gray-400" x-text="field.label"></span>
                                <span class="text-[11px] font-bold text-gray-800 dark:text-white" x-text="'Rp ' + field.value"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 sm:p-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex-shrink-0 bg-white dark:bg-gray-900">
                <x-ui.button variant="outline" className="w-full text-xs" @click="showDetailModal = false">Tutup</x-ui.button>
            </div>
        </div>
    </div>