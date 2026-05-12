<!-- Tab: Attendance -->
<div x-show="activeTab === 'attendance'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" x-cloak>
    <div class="rounded-2xl border-2 border-dashed border-gray-200 bg-white p-12 text-center transition-all hover:border-brand-200 hover:bg-gray-50/50 dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-brand-500/50">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Import Absensi Karyawan</h3>
        <p class="mx-auto mt-1 max-w-sm text-sm text-gray-500 dark:text-gray-400">Pilih file Excel absensi Anda untuk memproses data kehadiran periode ini.</p>
        <div class="mt-8">
            <x-ui.button variant="primary" className="px-8">Pilih File Excel</x-ui.button>
        </div>
        <p class="mt-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Format: .xls, .xlsx (Maks. 10MB)</p>
    </div>
</div>
