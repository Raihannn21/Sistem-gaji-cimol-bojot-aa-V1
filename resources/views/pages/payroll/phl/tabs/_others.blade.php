<!-- Tab: Others (Manual Allowances) -->
<div x-show="activeTab === 'others'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" x-cloak>
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Tunjangan Lain-lain (PHL)</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Input manual untuk tunjangan khusus seperti THR, Bonus, atau insentif lainnya.</p>
        </div>
        <x-ui.button variant="primary" @click="showOthersModal = true" className="flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Input Tunjangan
        </x-ui.button>
    </div>
    
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider">Karyawan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider">Jenis Tunjangan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-right">Nominal</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider">Keterangan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-medium text-sm">
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-800 dark:text-white/90">Ahmad Fauzi</p>
                            <p class="text-xs text-gray-400">NRP. 1001</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">THR Keagamaan</span>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-800 dark:text-white tabular-nums">Rp 4.000.000</td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs italic">Pembayaran THR Tahun 2025</td>
                        <td class="px-6 py-4 text-right">
                            <button class="p-2 text-gray-400 hover:text-red-500 transition-colors"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
