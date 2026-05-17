<!-- Tab: Risk Allowance -->
<div x-show="activeTab === 'risk'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" x-cloak>
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Tunjangan Risiko Lapangan</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kompensasi risiko harian untuk unit kerja tertentu.</p>
        </div>
        <x-ui.button variant="primary" @click="showRiskModal = true">Input Risiko Baru</x-ui.button>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500">Karyawan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 text-center">Hari Risiko</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 text-right">Total Tunjangan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-medium text-sm">
                    @for ($i = 1; $i <= 3; $i++)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                        <td class="px-6 py-4"><p class="font-bold text-gray-800 dark:text-white/90">Budi Santoso</p><p class="text-xs text-gray-400">NRP. 1002</p></td>
                        <td class="px-6 py-4 text-center font-bold text-gray-800 dark:text-white/90">8 Hari</td>
                        <td class="px-6 py-4 text-right font-bold text-brand-600 tabular-nums">Rp 400.000</td>
                        <td class="px-6 py-4 text-center">
                            <button @click="showRiskDetailModal = true; selectedEmployee = { name: 'Budi Santoso', nrp: '1002' }" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-brand-500 transition-colors"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
