<template x-teleport="body">
    <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-400/50 backdrop-blur-sm p-4" x-cloak>

        <div @click.away="showModal = false" x-show="showModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-[500px] max-w-full rounded-3xl bg-white p-6 shadow-xl dark:bg-gray-900 sm:p-8 max-h-[90vh] overflow-y-auto custom-scrollbar">

            <!-- Close Button -->
            <button @click="showModal = false"
                class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z"
                        fill="currentColor" />
                </svg>
            </button>

            <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Tambah User Baru</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Daftarkan pengguna baru untuk memberikan akses ke
                sistem penggajian.</p>

            <form class="mt-8 space-y-5 pb-10">
                <!-- Nama Lengkap -->
                <x-form.input label="Nama Lengkap" name="name" placeholder="Contoh: Musharof Chowdhury" required />

                <!-- Email -->
                <x-form.input label="Email" type="email" name="email" placeholder="user@cimolbojot.com" required />

                <!-- Role Selection -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Role</label>
                    <select
                        class="w-full h-12 rounded-xl border border-gray-200 bg-white px-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white appearance-none">
                        <option>Administrator</option>
                        <option>Payroll Manager</option>
                        <option>HR Staff</option>
                    </select>
                </div>

                <!-- Password -->
                <x-form.input label="Password" type="password" name="password" placeholder="••••••••" required />
                <div class="flex items-center gap-3 pt-4">
                    <x-ui.button variant="outline" className="flex-1" @click="showModal = false">Batal</x-ui.button>
                    <x-ui.button variant="primary" className="flex-1" type="submit">Simpan User</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</template>