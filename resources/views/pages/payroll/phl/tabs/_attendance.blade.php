<!-- Tab: Attendance -->
<div x-show="activeTab === 'attendance'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" x-cloak>
    <div class="group relative rounded-[40px] border-2 border-dashed border-gray-200 bg-white p-16 text-center transition-all hover:border-brand-300 hover:bg-brand-50/10 dark:border-gray-800 dark:bg-white/[0.02] dark:hover:border-brand-500/50">
        <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-3xl bg-brand-50 text-brand-600 shadow-sm transition-transform group-hover:scale-110 dark:bg-brand-500/10 dark:text-brand-400">
            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
        </div>
        <h3 class="text-2xl font-black text-gray-800 dark:text-white">Import Absensi Karyawan</h3>
        <p class="mx-auto mt-2 max-w-sm text-gray-500 dark:text-gray-400">Tarik dan letakkan file Excel absensi Anda di sini, atau klik tombol di bawah untuk memilih file.</p>
        <div class="mt-10">
            <x-ui.button variant="primary" className="px-10 py-4 text-base shadow-xl shadow-brand-500/20">Unggah File Sekarang</x-ui.button>
        </div>
        <p class="mt-6 text-[10px] font-bold uppercase tracking-widest text-gray-400">FORMAT: .XLS, .XLSX (MAX. 10MB)</p>
    </div>
</div>
