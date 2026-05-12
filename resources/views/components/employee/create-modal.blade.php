@props(['show' => false])

<template x-teleport="body">
    <div x-show="showModal" 
         x-data="{ selectedRole: '' }"
         @change-jabatan.window="selectedRole = $event.detail"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-400/50 backdrop-blur-sm p-4" 
         x-cloak>
        
        <!-- Modal Box: Ukuran Dikunci (Fixed Height) & 3XL Radius -->
        <div @click.away="showModal = false" 
             x-show="showModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative flex flex-col w-full max-w-2xl h-[480px] rounded-3xl bg-white shadow-xl dark:bg-gray-900 overflow-hidden border border-gray-100 dark:border-gray-800">
            
            <!-- Close Button -->
            <button @click="showModal = false" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 z-50">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
            </button>

            <!-- Header -->
            <div class="px-6 py-5 sm:px-8 border-b border-gray-100 dark:border-gray-800 flex-shrink-0 bg-white dark:bg-gray-900">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Tambah Karyawan Baru</h3>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6 sm:p-8 custom-scrollbar bg-white dark:bg-gray-900">
                <form id="createEmployeeForm" class="space-y-6">
                    <input type="hidden" name="status" value="Aktif">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <x-form.input label="Nama Lengkap" placeholder="Nama" />
                        <x-form.input label="Emp No" placeholder="EMP001" />
                        <x-form.input label="No. ID" placeholder="ID1001" />
                        <x-form.input label="NIK" placeholder="16 digit NIK" />

                        <x-form.select-custom label="Jabatan" placeholder="Pilih Jabatan" name="jabatan">
                            <x-form.select-item value="PHL">PHL</x-form.select-item>
                            <x-form.select-item value="PKWT">PKWT</x-form.select-item>
                        </x-form.select-custom>
                        
                        <x-form.input label="Email" type="email" placeholder="mail@example.com" />
                        <x-form.input label="No. Telepon" placeholder="0812xxx" />
                        <x-form.input label="Nomor Tim" placeholder="T0x" />
                        <x-form.input label="Lokasi" placeholder="HO / Site" />
                        <x-form.input label="Gaji Pokok" type="number" placeholder="Nominal" />
                        <x-form.input label="Nama Bank" placeholder="Contoh: BCA" />
                        <x-form.input label="Nomor Rekening" placeholder="12345678" />
                    </div>

                    <!-- Input Khusus PHL -->
                    <div x-show="selectedRole === 'PHL'" x-transition class="pt-4 border-t border-dashed border-gray-200 dark:border-gray-800">
                        <h4 class="text-sm font-bold text-brand-500 mb-4 italic">Data Khusus PHL:</h4>
                        <div class="grid grid-cols-1 gap-4">
                            <x-form.input label="Tunjangan Risiko" type="number" placeholder="Nominal" />
                        </div>
                    </div>

                    <!-- Input Khusus PKWT -->
                    <div x-show="selectedRole === 'PKWT'" x-transition class="pt-4 border-t border-dashed border-gray-200 dark:border-gray-800">
                        <h4 class="text-sm font-bold text-brand-500 mb-4 italic">Data Khusus PKWT:</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            <x-form.input label="Potongan" type="number" placeholder="Potongan" />
                            <x-form.input label="Bpjs Kesehatan" type="number" placeholder="BPJS Kes" />
                            <x-form.input label="Bpjs TK" type="number" placeholder="BPJS TK" />
                            <x-form.input label="PPH 21" type="number" placeholder="PPH 21" />
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-6 py-5 sm:px-8 border-t border-gray-100 dark:border-gray-800 flex-shrink-0 flex items-center justify-end gap-3 bg-white dark:bg-gray-900">
                <button @click="showModal = false" class="text-sm font-medium text-gray-500 hover:text-gray-700">Batal</button>
                <x-ui.button variant="primary" form="createEmployeeForm" type="submit" className="px-6">Simpan Karyawan</x-ui.button>
            </div>
        </div>
    </div>
</template>