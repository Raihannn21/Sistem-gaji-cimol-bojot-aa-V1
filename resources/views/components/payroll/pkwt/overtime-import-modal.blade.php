@props(['period' => null])
<template x-teleport="body">
    <div x-show="showImportOvertimeModal" 
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
         class="modal fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5" 
         style="display: none;"
         x-cloak>
        
        <!-- Backdrop -->
        <div @click="!isUploading && (showImportOvertimeModal = false)" class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        </div>

        <!-- Modal Content Box -->
        <div @click.stop class="relative w-full rounded-3xl bg-white dark:bg-gray-900 max-w-[700px] shadow-2xl border border-gray-100 dark:border-gray-800"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95">
            
            <!-- Inner Wrapper -->
            <div class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
                
                <!-- Close Button -->
                <button @click="showImportOvertimeModal = false" :disabled="isUploading" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 z-50 transition-colors duration-150">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
                </button>

                <!-- Header -->
                <div class="px-2 pr-14 mb-6">
                    <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Import Lembur PKWT</h4>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 font-medium">Unggah file Excel untuk memasukkan data lembur periode ini.</p>
                </div>

                <!-- Form Content -->
                <form id="importOvertimeForm" action="{{ $period ? url('/payroll/pkwt/periods/' . $period->id . '/import-overtime') : '#' }}" method="POST" enctype="multipart/form-data" @submit="isUploading = true" class="flex flex-col">
                    @csrf
                    
                    <div class="custom-scrollbar max-h-[458px] overflow-y-auto p-2 space-y-6">
                        <!-- Guidance Alert -->
                        <div class="rounded-2xl border border-brand-100 bg-brand-50/50 p-4 dark:border-brand-900/30 dark:bg-brand-950/20">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-500 text-white shadow-md shadow-brand-500/10">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-white/90">Format Kolom Excel:</h4>
                                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium">
                                        Pastikan file Anda memiliki kolom-kolom berikut: <span class="font-bold text-gray-700 dark:text-gray-300">ID Karyawan, Nama Karyawan, Tanggal (DD-MM-YYYY), Jumlah Jam, Nominal per Jam, Keterangan</span>.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Template Download Link -->
                        <div class="rounded-2xl border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-transparent flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="p-2.5 rounded-xl bg-green-500/10 text-green-600 dark:text-green-400">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-white/90">Belum punya template Excel?</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Unduh template lembur PKWT yang sesuai standar.</p>
                                </div>
                            </div>
                            <x-ui.button variant="outline" type="button" href="/templates/overtime_import_template_pkwt.xlsx" download className="flex items-center gap-2 text-xs font-semibold hover:bg-gray-100 dark:hover:bg-white/5">
                                Unduh Template
                            </x-ui.button>
                        </div>

                        <!-- File Input -->
                        <div class="relative group">
                            <input type="file" name="file" accept=".xlsx, .xls, .csv" class="hidden" id="overtimeFileInput" @change="handleFile($event)" required>
                            <label for="overtimeFileInput" class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 dark:border-gray-700 dark:bg-dark-900 dark:hover:bg-gray-800 transition-all group-hover:border-brand-500">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-3 text-gray-400 group-hover:text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400 font-bold" x-text="fileName || 'Klik untuk pilih file'"></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">XLSX, XLS, atau CSV (Max. 2MB)</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-end gap-3 px-2 mt-6 border-t border-gray-100 dark:border-gray-800 pt-5">
                        <button type="button" @click="showImportOvertimeModal = false" :disabled="isUploading" class="flex justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] transition-colors duration-150">
                            Batal
                        </button>
                        <button type="submit" :disabled="isUploading" class="flex justify-center rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 shadow-lg shadow-brand-500/10 transition-all duration-150">
                            <span x-show="!isUploading">Import Sekarang</span>
                            <span x-show="isUploading" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Mengolah Data...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
