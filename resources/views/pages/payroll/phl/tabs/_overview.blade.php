<!-- Tab: Overview -->
<div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" x-cloak>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Total Karyawan</p>
            <h4 class="mt-3 text-2xl font-black text-gray-800 dark:text-white">153 <span class="text-sm font-medium text-gray-400">Orang</span></h4>
        </div>
        <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Kesiapan Data</p>
            <h4 class="mt-3 text-2xl font-black text-gray-800 dark:text-white">94% <span class="text-sm font-medium text-green-500">Ready</span></h4>
        </div>
        <div class="rounded-3xl border border-brand-100 bg-brand-50/30 p-6 shadow-sm dark:border-brand-500/10 dark:bg-brand-500/5">
            <p class="text-xs font-bold uppercase tracking-widest text-brand-600 dark:text-brand-400">Estimasi Pengeluaran</p>
            <h4 class="mt-3 text-2xl font-black text-brand-600 dark:text-brand-500">Rp 482.500.000</h4>
        </div>
    </div>
    
    <div class="rounded-3xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Rangkuman Kalkulasi Gaji</h3>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500">
                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                Sinkron dengan data terbaru
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Karyawan</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Pokok</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Lembur</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Risiko</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right font-black">Total Bersih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @for ($i = 1; $i <= 5; $i++)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                        <td class="px-6 py-4"><p class="text-sm font-bold text-gray-800 dark:text-white">Ahmad Fauzi</p><p class="text-[10px] text-gray-400 font-medium uppercase tracking-tighter">NRP. 100{{ $i }} • SECURITY</p></td>
                        <td class="px-6 py-4 text-sm text-right text-gray-600 dark:text-gray-400 tabular-nums">Rp 2.500.000</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-600 dark:text-gray-400 tabular-nums">Rp 450.000</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-600 dark:text-gray-400 tabular-nums">Rp 150.000</td>
                        <td class="px-6 py-4 text-sm text-right font-bold text-brand-600 dark:text-brand-500 tabular-nums">Rp 3.100.000</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
