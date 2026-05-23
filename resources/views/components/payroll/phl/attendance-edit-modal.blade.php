@props(['period'])
<template x-teleport="body">
    <div x-show="showEditAttendanceModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-400/50 backdrop-blur-sm p-4" 
         x-cloak>
        
        <div @click.away="showEditAttendanceModal = false" 
             x-show="showEditAttendanceModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-[700px] rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11 shadow-xl">
            
            <!-- Absolute Close Button -->
            <button @click="showEditAttendanceModal = false" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 sm:right-6 sm:top-6 transition-colors duration-150">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
            </button>

            <!-- Header -->
            <div class="mb-8 pr-10">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Edit Log Absensi</h3>
                <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-semibold text-brand-600 dark:text-brand-400" x-text="selectedAttendance.employee_name"></span>
                    <span class="mx-2 text-gray-300 dark:text-gray-700">•</span>
                    <span x-text="selectedAttendance.date" class="font-medium text-gray-700 dark:text-gray-300"></span>
                </p>
            </div>

            <!-- Form -->
            <form :action="`/payroll/phl/periods/{{ $period->id }}/attendance/${selectedAttendance.id}`" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- 2-Column Responsive Input Grid -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Scan Masuk (Jam Masuk)
                        </label>
                        <input type="time" name="scan_in" x-model="selectedAttendance.scan_in" class="w-full rounded-xl border border-gray-200 bg-transparent py-3 px-5 text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:text-white dark:focus:border-brand-500 transition duration-150">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Scan Pulang (Jam Keluar)
                        </label>
                        <input type="time" name="scan_out" x-model="selectedAttendance.scan_out" class="w-full rounded-xl border border-gray-200 bg-transparent py-3 px-5 text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:text-white dark:focus:border-brand-500 transition duration-150">
                    </div>
                </div>

                <!-- 2-Column Responsive Input Grid untuk Terlambat & Pulang Cepat -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Terlambat (Jam:Menit)
                        </label>
                        <input type="text" name="late_time" x-model="selectedAttendance.late_time" placeholder="Contoh: 00:26 atau -" class="w-full rounded-xl border border-gray-200 bg-transparent py-3 px-5 text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:text-white dark:focus:border-brand-500 transition duration-150">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Pulang Cepat (Jam:Menit)
                        </label>
                        <input type="text" name="early_time" x-model="selectedAttendance.early_time" placeholder="Contoh: 02:58 atau -" class="w-full rounded-xl border border-gray-200 bg-transparent py-3 px-5 text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:text-white dark:focus:border-brand-500 transition duration-150">
                    </div>
                </div>

                <!-- Indigo Warning Guidance Card -->
                <div class="flex items-start gap-3 rounded-2xl bg-indigo-50/50 p-4 dark:bg-indigo-500/5 border border-indigo-100/50 dark:border-indigo-500/10">
                    <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-xs text-indigo-700/95 dark:text-indigo-300/90 leading-relaxed">
                        <strong>Catatan Penghitungan:</strong> Durasi kehadiran akan dihitung ulang secara otomatis berdasarkan selisih jam masuk dan jam pulang (maksimal 8 jam). Jika hanya salah satu diisi, durasi otomatis dianggap 8 jam kerja penuh.
                    </p>
                </div>

                <!-- Action Footer -->
                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                    <x-ui.button variant="outline" type="button" @click="showEditAttendanceModal = false" className="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">Batal</x-ui.button>
                    <x-ui.button variant="primary" type="submit" className="shadow-sm shadow-brand-500/10 transition-all hover:translate-y-[-1px]">Simpan Perubahan</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</template>
