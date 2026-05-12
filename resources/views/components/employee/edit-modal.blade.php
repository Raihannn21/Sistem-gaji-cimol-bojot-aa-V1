@props(['employee' => null])

<template x-teleport="body">
    <div x-show="showEditModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-400/50 backdrop-blur-sm p-4" 
         x-cloak>
        
        <!-- Modal Box: Style Standardized -->
        <div @click.away="showEditModal = false" 
             class="relative flex flex-col w-full max-w-2xl h-auto max-h-[480px] rounded-3xl bg-white shadow-xl dark:bg-gray-900 overflow-hidden border border-gray-100 dark:border-gray-800">
            
            <!-- Close Button -->
            <button @click="showEditModal = false" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 z-50">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
            </button>

            <!-- Header -->
            <div class="p-6 sm:p-8 pb-0 flex-shrink-0 bg-white dark:bg-gray-900">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Edit Data Karyawan</h3>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6 sm:p-8 custom-scrollbar bg-white dark:bg-gray-900">
                <form id="editEmployeeForm" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <x-form.input label="Nama Lengkap" x-model="selectedEmployee.name" />
                        <x-form.input label="Emp No" x-model="selectedEmployee.emp_no" />
                        <x-form.input label="No. ID" x-model="selectedEmployee.id_no" />
                        <x-form.input label="NIK" x-model="selectedEmployee.nik" />

                        <x-form.select-custom label="Jabatan" name="jabatan" x-model="selectedEmployee.role">
                            <x-form.select-item value="PHL">PHL</x-form.select-item>
                            <x-form.select-item value="PKWT">PKWT</x-form.select-item>
                        </x-form.select-custom>
                        
                        <x-form.input label="Email" type="email" x-model="selectedEmployee.email" />
                        <x-form.input label="No. Telepon" x-model="selectedEmployee.phone" />
                        <x-form.input label="Nomor Tim" x-model="selectedEmployee.team" />
                        <x-form.input label="Lokasi" x-model="selectedEmployee.location" />
                        <x-form.input label="Gaji Pokok" type="number" x-model="selectedEmployee.salary" />
                        
                        <x-form.input label="Nama Bank" x-model="selectedEmployee.bank_name" />
                        <x-form.input label="Nomor Rekening" x-model="selectedEmployee.bank_account" />
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="p-6 sm:p-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex-shrink-0 flex items-center justify-end gap-3 bg-white dark:bg-gray-900">
                <button @click="showEditModal = false" class="text-sm font-medium text-gray-500 hover:text-gray-700">Batal</button>
                <x-ui.button variant="primary" form="editEmployeeForm" type="submit" className="px-6">Update Data</x-ui.button>
            </div>
        </div>
    </div>
</template>