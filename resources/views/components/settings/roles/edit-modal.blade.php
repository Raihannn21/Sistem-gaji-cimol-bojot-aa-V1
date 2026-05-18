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

        <div @click.away="showEditModal = false" 
             x-show="showEditModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-[700px] rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11 shadow-xl max-h-[90vh] overflow-y-auto custom-scrollbar">

            <!-- Absolute Close Button -->
            <button @click="showEditModal = false"
                class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 sm:right-6 sm:top-6 transition-colors duration-150 z-50">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z"
                        fill="currentColor" />
                </svg>
            </button>

            <!-- Header -->
            <div class="mb-8 pr-10">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Edit Data User</h3>
                <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">Perbarui informasi profil dan hak akses pengguna di bawah ini.</p>
            </div>

            <!-- Form -->
            <form method="POST" :action="'/settings/roles/' + selectedUser.id" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Nama Lengkap -->
                    <x-form.input label="Nama Lengkap" name="name" x-model="selectedUser.name" required />

                    <!-- Email -->
                    <x-form.input label="Email" type="email" name="email" x-model="selectedUser.email" required />
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Nomor HP -->
                    <x-form.input label="Nomor HP" name="phone" x-model="selectedUser.phone" required />

                    <!-- Password (Optional on Edit) -->
                    <x-form.input label="Password Baru (Kosongkan jika tidak diganti)" type="password" name="password" placeholder="••••••••" />
                </div>

                <!-- Status Toggle -->
                <div class="flex items-center justify-between rounded-2xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-white/[0.02]">
                    <div>
                        <label class="text-sm font-semibold text-gray-800 dark:text-white">Status Akun</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Aktifkan untuk mengizinkan pengguna masuk ke sistem.</p>
                    </div>
                    <div @click="selectedUser.status = (selectedUser.status === 'Aktif' ? 'Non-Aktif' : 'Aktif')" 
                         class="relative cursor-pointer rounded-full transition-all duration-200 focus:outline-none" 
                         :class="selectedUser.status === 'Aktif' ? '' : 'bg-gray-200 dark:bg-gray-700'"
                         :style="selectedUser.status === 'Aktif' ? 'width: 44px; height: 24px; background-color: #10b981;' : 'width: 44px; height: 24px;'">
                        <div class="absolute rounded-full bg-white shadow-sm transition-all duration-200" 
                             :style="selectedUser.status === 'Aktif' ? 'top: 2px; left: 22px; width: 20px; height: 20px;' : 'top: 2px; left: 2px; width: 20px; height: 20px;'"></div>
                        <input type="hidden" name="status" :value="selectedUser.status">
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                    <x-ui.button variant="outline" type="button" @click="showEditModal = false" className="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">Batal</x-ui.button>
                    <x-ui.button variant="primary" type="submit" className="shadow-sm shadow-brand-500/10 transition-all hover:translate-y-[-1px]">Update User</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</template>
