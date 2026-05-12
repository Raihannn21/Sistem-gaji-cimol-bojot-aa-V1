<!-- Tab: Slips -->
<div x-show="activeTab === 'slips'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" x-cloak>
    <div class="rounded-3xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="p-6 border-b border-gray-50 dark:border-gray-800">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Daftar Slip Gaji Terbit</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-gray-400">Karyawan</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-gray-400 text-right">Gaji Bersih</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-gray-400 text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-gray-400 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800 font-medium">
                    @for ($i = 1; $i <= 5; $i++)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                        <td class="px-6 py-4 text-sm font-bold text-gray-800 dark:text-white">Ahmad Fauzi</td>
                        <td class="px-6 py-4 text-right text-sm font-black text-brand-600 tabular-nums">Rp 3.150.000</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-[10px] font-bold text-green-700 dark:bg-green-500/10">Published</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button @click="showSlipModal = true; selectedSlip = { name: 'Ahmad Fauzi', nrp: '1001', total: '3.150.000' }" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-brand-500 transition-colors"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
