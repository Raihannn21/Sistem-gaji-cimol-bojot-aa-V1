@props(['period' => null])
<template x-teleport="body">
    <div x-show="showAttendanceImportModal" 
         x-data="{ 
            isUploading: false,
            fileName: '',
            handleFile(e) {
                const file = e.target.files[0];
                if (file) {
                    this.fileName = file.name;
                }
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
        <div @click.away="!isUploading && (showAttendanceImportModal = false)" 
             x-show="showAttendanceImportModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative flex flex-col w-full max-w-md rounded-3xl bg-white shadow-xl dark:bg-gray-900 overflow-hidden border border-gray-100 dark:border-gray-800">
            
            <!-- Close Button -->
            <button @click="showAttendanceImportModal = false" :disabled="isUploading" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 z-50">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
            </button>

            <!-- Header -->
            <div class="px-6 py-5 sm:px-8 border-b border-gray-100 dark:border-gray-800 flex-shrink-0 bg-white dark:bg-gray-900">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Import Absensi PKWT</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unggah file Excel untuk periode ini.</p>
            </div>

            <!-- Content -->
            <div class="p-6 sm:p-8 bg-white dark:bg-gray-900">
                <form id="importAttendanceForm" action="{{ $period ? url('/payroll/pkwt/periods/' . $period->id . '/import-attendance') : '#' }}" method="POST" enctype="multipart/form-data" @submit="isUploading = true">
                    @csrf
                    <div class="space-y-6">
                        <!-- Guidance -->
                        <div class="rounded-2xl bg-brand-50 p-4 dark:bg-brand-500/10">
                            <div class="flex gap-3">
                                <svg class="h-5 w-5 text-brand-600 dark:text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h4 class="text-sm font-bold text-brand-800 dark:text-brand-300">Format Kolom:</h4>
                                    <p class="text-xs text-brand-700/80 dark:text-brand-400/80 mt-1">Emp No, No ID, Nama, Tanggal, Scan Masuk, Scan Pulang.</p>
                                </div>
                            </div>
                        </div>

                        <!-- File Input -->
                        <div class="relative group">
                            <input type="file" name="file" accept=".xlsx, .xls" class="hidden" id="attendanceFileInput" @change="handleFile($event)" required>
                            <label for="attendanceFileInput" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-3xl cursor-pointer bg-gray-50 hover:bg-gray-100 dark:border-gray-700 dark:bg-dark-900 dark:hover:bg-gray-800 transition-all group-hover:border-brand-500">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-white shadow-sm dark:bg-gray-800 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-gray-400 group-hover:text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                    </div>
                                    <p class="mb-1 text-sm font-bold text-gray-700 dark:text-gray-300" x-text="fileName || 'Pilih File Excel'"></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">XLSX or XLS (Max. 2MB)</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-6 py-5 sm:px-8 border-t border-gray-100 dark:border-gray-800 flex-shrink-0 flex items-center justify-end gap-3 bg-white dark:bg-gray-900">
                <button @click="showAttendanceImportModal = false" :disabled="isUploading" class="text-sm font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white transition-colors">Batal</button>
                <x-ui.button variant="primary" form="importAttendanceForm" type="submit" className="px-8 py-3 rounded-2xl" x-bind:disabled="isUploading">
                    <span x-show="!isUploading">Import Sekarang</span>
                    <span x-show="isUploading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Mengolah Data...
                    </span>
                </x-ui.button>
            </div>
        </div>
    </div>
</template>
