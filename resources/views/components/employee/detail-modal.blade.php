<div x-show="showDetailModal" 
     x-data="{
        formatPrice(val) {
            if (!val) return '0';
            let cleanVal = val.toString().replace(/\D/g, '');
            return new Intl.NumberFormat('id-ID').format(cleanVal);
        }
     }"
     class="modal fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5" 
     style="display: none;"
     x-cloak>
    
    <!-- Backdrop (Exact copy of modal.blade.php backdrop with backdrop-blur-sm) -->
    <div @click="showDetailModal = false" class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>

    <!-- Modal Content Box (Exact copy of modal.blade.php content wrapper) -->
    <div @click.stop class="relative w-full rounded-3xl bg-white dark:bg-gray-900 max-w-[700px] shadow-2xl border border-gray-100 dark:border-gray-800"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95">
        
        <!-- Inner Wrapper (Exact copy of profile-card.blade.php inner wrapper) -->
        <div class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
            
            <!-- Close Button -->
            <button @click="showDetailModal = false" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 z-50 transition-colors duration-150">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
            </button>

            <!-- Header (Borderless & Spaced) -->
            <div class="px-2 pr-14 mb-6 flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-500/10 text-brand-500 flex-shrink-0">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90" x-text="selectedEmployee.name"></h4>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedEmployee.role"></span>
                        <span class="inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider" :class="getStatusClass(selectedEmployee.status)" x-text="selectedEmployee.status"></span>
                    </div>
                </div>
            </div>

            <!-- Content Area with Scroll -->
            <div class="custom-scrollbar h-[458px] overflow-y-auto p-2 space-y-6">
                <!-- General Info Section -->
                <div>
                    <h5 class="mb-4 text-base font-bold text-gray-800 dark:text-white/90">Informasi Umum</h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                        <div>
                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">Emp No</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="selectedEmployee.emp_no"></p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">No. ID</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="selectedEmployee.id_no || '-'"></p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">NIK</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="selectedEmployee.nik || '-'"></p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">Email</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="selectedEmployee.email || '-'"></p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">No. Telepon</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="selectedEmployee.phone || '-'"></p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">Nomor Tim</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="selectedEmployee.team || '-'"></p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">Lokasi</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="selectedEmployee.location || '-'"></p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">Gaji Pokok</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90" x-text="'Rp ' + formatPrice(selectedEmployee.salary)"></p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">Nama Bank</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="selectedEmployee.bank_name || '-'"></p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">Nomor Rekening</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="selectedEmployee.bank_account || '-'"></p>
                        </div>
                    </div>
                </div>

                <!-- Conditional: PHL Details -->
                <div x-show="selectedEmployee.role === 'PHL'" class="pt-5 border-t border-dashed border-gray-200 dark:border-gray-800">
                    <h5 class="mb-4 text-base font-bold text-emerald-600 dark:text-emerald-400">Detail Khusus PHL</h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                        <div>
                            <p class="mb-1 text-xs text-emerald-600 dark:text-emerald-400">Tunjangan Risiko</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90" x-text="'Rp ' + formatPrice(selectedEmployee.risk_allowance)"></p>
                        </div>
                    </div>
                </div>

                <!-- Conditional: PKWT Details -->
                <div x-show="selectedEmployee.role === 'PKWT'" class="pt-5 border-t border-dashed border-gray-200 dark:border-gray-800">
                    <h5 class="mb-4 text-base font-bold text-blue-600 dark:text-blue-400">Detail Khusus PKWT</h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                        <div>
                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">BPJS Kesehatan</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90" x-text="'Rp ' + formatPrice(selectedEmployee.bpjs_health)"></p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">BPJS TK</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90" x-text="'Rp ' + formatPrice(selectedEmployee.bpjs_tk)"></p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">PPH 21</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90" x-text="'Rp ' + formatPrice(selectedEmployee.pph21)"></p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">Tunjangan Risiko</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90" x-text="'Rp ' + formatPrice(selectedEmployee.risk_allowance)"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer (Borderless Border-t Spacing) -->
            <div class="flex items-center justify-end gap-3 px-2 mt-6 border-t border-gray-100 dark:border-gray-800 pt-5">
                <button type="button" @click="showDetailModal = false" class="flex justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] transition-colors duration-150 w-full sm:w-auto">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>