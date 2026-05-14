@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6" x-data="{ 
        isDragging: false,
        files: [],
        uploadProgress: 0,
        isUploading: false,
        removeFile(index) {
            this.files.splice(index, 1);
        },
        handleDrop(e) {
            this.isDragging = false;
            const newFiles = [...e.dataTransfer.files];
            this.files.push(...newFiles);
        },
        handleFileSelect(e) {
            const newFiles = [...e.target.files];
            this.files.push(...newFiles);
        },
        startUpload() {
            if (this.files.length === 0) return;
            this.isUploading = true;
            let interval = setInterval(() => {
                if (this.uploadProgress >= 100) {
                    clearInterval(interval);
                    this.isUploading = false;
                    alert('Berhasil mengunggah ' + this.files.length + ' file laporan.');
                    this.files = [];
                    this.uploadProgress = 0;
                } else {
                    this.uploadProgress += 5;
                }
            }, 100);
        }
    }">
        <x-common.page-breadcrumb :pageName="$title" />

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Upload Section -->
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-3xl border-2 border-dashed border-gray-200 bg-white p-12 text-center transition-all dark:border-gray-800 dark:bg-white/[0.03]"
                     :class="isDragging ? 'border-brand-500 bg-brand-50/50 dark:bg-brand-500/10' : ''"
                     @dragover.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="handleDrop($event)">
                    
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 mb-6 shadow-lg shadow-brand-500/10">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </div>
                    
                    <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight italic">Tarik & Lepas File Laporan</h3>
                    <p class="text-sm text-gray-500 font-medium mt-2 mb-8">Format yang didukung: <span class="font-bold text-gray-700 dark:text-gray-300">.pdf, .xlsx, .csv</span></p>
                    
                    <label class="relative inline-flex cursor-pointer items-center justify-center rounded-xl bg-brand-600 px-8 py-3 text-sm font-black text-white shadow-xl shadow-brand-500/20 hover:bg-brand-700 transition-all uppercase italic">
                        <span>Pilih File dari Komputer</span>
                        <input type="file" class="hidden" multiple @change="handleFileSelect($event)">
                    </label>
                </div>

                <!-- File List Section -->
                <template x-if="files.length > 0">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-widest italic" x-text="files.length + ' File Terpilih'"></h4>
                            <button @click="files = []" class="text-[10px] font-black text-red-500 uppercase italic hover:underline">Hapus Semua</button>
                        </div>
                        
                        <div class="space-y-3">
                            <template x-for="(file, index) in files" :key="index">
                                <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-white/[0.01]">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white shadow-sm dark:bg-gray-800">
                                            <template x-if="file.name.endsWith('.pdf')">
                                                <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            </template>
                                            <template x-if="!file.name.endsWith('.pdf')">
                                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </template>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-tight" x-text="file.name"></p>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest" x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'"></p>
                                        </div>
                                    </div>
                                    <button @click="removeFile(index)" class="text-gray-400 hover:text-red-500 transition-colors">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Progress Bar & Upload Button -->
                        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800">
                            <template x-if="isUploading">
                                <div class="mb-6 space-y-2">
                                    <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-gray-400 italic">
                                        <span>Mengunggah...</span>
                                        <span x-text="uploadProgress + '%'"></span>
                                    </div>
                                    <div class="h-2 w-full bg-gray-100 rounded-full dark:bg-white/5 overflow-hidden">
                                        <div class="h-full bg-brand-600 rounded-full transition-all duration-300" :style="'width: ' + uploadProgress + '%'"></div>
                                    </div>
                                </div>
                            </template>
                            
                            <div class="flex justify-end">
                                <x-ui.button variant="primary" @click="startUpload()" :disabled="isUploading" className="w-full sm:w-auto px-12 shadow-xl shadow-brand-500/20">
                                    <template x-if="!isUploading">
                                        <span class="flex items-center gap-2">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Mulai Unggah Laporan
                                        </span>
                                    </template>
                                    <template x-if="isUploading">
                                        <span class="flex items-center gap-2">
                                            <svg class="animate-spin h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Memproses...
                                        </span>
                                    </template>
                                </x-ui.button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Guidelines Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight italic mb-6">Panduan Import</h3>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 font-black italic dark:bg-brand-500/10">1</div>
                            <p class="text-xs text-gray-500 font-medium leading-relaxed">
                                Pastikan format file adalah <span class="text-gray-800 dark:text-white font-bold">.pdf</span> untuk laporan statis atau <span class="text-gray-800 dark:text-white font-bold">.xlsx/.csv</span> untuk data terstruktur.
                            </p>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 font-black italic dark:bg-brand-500/10">2</div>
                            <p class="text-xs text-gray-500 font-medium leading-relaxed">
                                Nama file harus menyertakan <span class="text-gray-800 dark:text-white font-bold">Periode</span> (contoh: Laporan_Gaji_Juli_2025).
                            </p>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 font-black italic dark:bg-brand-500/10">3</div>
                            <p class="text-xs text-gray-500 font-medium leading-relaxed">
                                Ukuran maksimal file per unggahan adalah <span class="text-gray-800 dark:text-white font-bold">10 MB</span>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-gray-900 p-6 shadow-xl text-white">
                    <h4 class="text-xs font-black uppercase tracking-widest italic mb-4">Butuh Bantuan?</h4>
                    <p class="text-xs text-gray-400 font-medium leading-relaxed mb-6">Jika Anda mengalami kendala saat mengunggah, hubungi tim IT Support kami.</p>
                    <x-ui.button variant="primary" className="w-full bg-white text-gray-900 hover:bg-gray-100">Hubungi IT Support</x-ui.button>
                </div>
            </div>
        </div>
    </div>
@endsection
