@props(['show' => false])

<div x-show="showModal" 
     x-data="{ 
        selectedRole: '{{ old('jabatan') }}',
        errors: {},
        formatCurrency(el) {
            let val = el.value.replace(/\D/g, '');
            if (val === '') {
                el.value = '';
                return;
            }
            el.value = new Intl.NumberFormat('id-ID').format(val);
        },
        validateForm(e) {
            this.errors = {};
            let hasError = false;
            
            const name = this.$el.querySelector('[name=name]').value;
            const emp_no = this.$el.querySelector('[name=emp_no]').value;
            const no_id = this.$el.querySelector('[name=no_id]').value;
            const jabatan = this.$el.querySelector('[name=jabatan]').value;

            if (!name) { this.errors.name = 'Nama lengkap wajib diisi.'; hasError = true; }
            if (!emp_no) { this.errors.emp_no = 'Emp No wajib diisi.'; hasError = true; }
            if (!no_id) { this.errors.no_id = 'No. ID wajib diisi.'; hasError = true; }
            if (!jabatan) { this.errors.jabatan = 'Jabatan wajib diisi.'; hasError = true; }

            if (hasError) {
                e.preventDefault();
                return false;
            }
            return true;
        }
     }"
     @change-jabatan.window="selectedRole = $event.detail; if($event.detail) delete errors.jabatan"
     class="modal fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5" 
     style="display: none;"
     x-cloak>
    
    <!-- Backdrop (Exact copy of modal.blade.php backdrop with backdrop-blur-sm) -->
    <div @click="showModal = false" class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-sm"
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
            <button @click="showModal = false" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 z-50 transition-colors duration-150">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
            </button>

            <!-- Header (Borderless & Spaced) -->
            <div class="px-2 pr-14 mb-6">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Tambah Karyawan Baru</h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Isi data di bawah ini untuk mendaftarkan karyawan baru ke dalam sistem.</p>
            </div>

            <!-- Form Body with Scroll area -->
            <form id="createEmployeeForm" 
                  class="flex flex-col" 
                  method="POST" 
                  action="{{ route('employees.store') }}"
                  @submit="validateForm($event)"
                  novalidate>
                @csrf
                <input type="hidden" name="status" value="Aktif">

                <div class="custom-scrollbar h-[458px] overflow-y-auto p-2 space-y-6">
                    <div>
                        <h5 class="mb-4 text-base font-bold text-gray-800 dark:text-white/90">Informasi Umum</h5>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                            <x-form.input name="name" label="Nama Lengkap" placeholder="Nama" @input="delete errors.name" />
                            <x-form.input name="emp_no" label="Emp No" placeholder="EMP001" @input="delete errors.emp_no" />
                            <x-form.input name="no_id" label="No. ID" placeholder="ID1001" @input="delete errors.no_id" />
                            <x-form.input name="nik" label="NIK" placeholder="16 digit NIK" />

                            <x-form.select-custom label="Jabatan" placeholder="Pilih Jabatan" name="jabatan">
                                <x-form.select-item value="PHL">PHL</x-form.select-item>
                                <x-form.select-item value="PKWT">PKWT</x-form.select-item>
                            </x-form.select-custom>
                            
                            <x-form.input name="email" label="Email" type="email" placeholder="mail@example.com" />
                            <x-form.input name="phone" label="No. Telepon" placeholder="0812xxx" />
                            <x-form.input name="team" label="Nomor Tim" placeholder="T0x" />
                            <x-form.input name="location" label="Lokasi" placeholder="HO / Site" />
                            
                            <!-- Gaji Pokok with Rp and Format -->
                            <x-form.input name="salary" label="Gaji Pokok" prefix="Rp" placeholder="0" @input="formatCurrency($event.target)" />
                            
                            <x-form.input name="bank_name" label="Nama Bank" placeholder="Contoh: BCA" />
                            <x-form.input name="bank_account" label="Nomor Rekening" placeholder="12345678" />
                        </div>
                    </div>

                    <!-- Input Khusus PHL -->
                    <div x-show="selectedRole === 'PHL'" x-transition class="pt-5 border-t border-dashed border-gray-200 dark:border-gray-800">
                        <h5 class="mb-1 text-base font-bold text-emerald-600 dark:text-emerald-400">Data Khusus PHL</h5>
                        <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">Gaji pokok PHL diinput per hari.</p>
                        <div class="grid grid-cols-1 gap-4">
                            <x-form.input name="risk_allowance" label="Tunjangan Risiko" prefix="Rp" placeholder="0" @input="formatCurrency($event.target)" />
                        </div>
                    </div>

                    <!-- Input Khusus PKWT -->
                    <div x-show="selectedRole === 'PKWT'" x-transition class="pt-5 border-t border-dashed border-gray-200 dark:border-gray-800">
                        <h5 class="mb-4 text-base font-bold text-blue-600 dark:text-blue-400">Data Khusus PKWT</h5>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                            <x-form.input name="bpjs_health" label="Bpjs Kesehatan" prefix="Rp" placeholder="0" @input="formatCurrency($event.target)" />
                            <x-form.input name="bpjs_tk" label="Bpjs TK" prefix="Rp" placeholder="0" @input="formatCurrency($event.target)" />
                            <x-form.input name="pph21" label="PPH 21" prefix="Rp" placeholder="0" @input="formatCurrency($event.target)" />
                            <x-form.input name="risk_allowance" label="Tunjangan Risiko" prefix="Rp" placeholder="0" @input="formatCurrency($event.target)" />
                        </div>
                    </div>
                </div>

                <!-- Footer (Borderless Border-t Spacing) -->
                <div class="flex items-center justify-end gap-3 px-2 mt-6 border-t border-gray-100 dark:border-gray-800 pt-5">
                    <button type="button" @click="showModal = false" class="flex justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] transition-colors duration-150">
                        Batal
                    </button>
                    <button type="submit" class="flex justify-center rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 shadow-lg shadow-brand-500/10 transition-all duration-150">
                        Simpan Karyawan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>