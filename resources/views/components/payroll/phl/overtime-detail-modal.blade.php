<template x-teleport="body">
    <div x-show="showDetailModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-400/50 backdrop-blur-sm p-4" 
         x-cloak>
        
        <div @click.away="showDetailModal = false" 
             x-show="showDetailModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-[500px] max-w-full rounded-3xl bg-white p-6 shadow-xl dark:bg-gray-900 sm:p-8">
            
            <button @click="showDetailModal = false" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 sm:right-6 sm:top-6">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
            </button>

            <div class="flex items-center gap-4 border-b border-gray-100 pb-6 dark:border-gray-800">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-500/10 text-brand-500 font-bold text-xl">
                    <span x-text="selectedEmployee.name ? selectedEmployee.name.charAt(0) : ''"></span>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white/90" x-text="selectedEmployee.name"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="'NRP. ' + selectedEmployee.nrp"></p>
                </div>
            </div>

            <div class="mt-6">
                <h4 class="text-sm font-bold text-gray-800 dark:text-white mb-4">Rincian Lembur Periode Ini</h4>
                <div class="max-h-[300px] overflow-y-auto rounded-2xl border border-gray-100 dark:border-gray-800">
                    <table class="w-full text-left">
                        <thead class="sticky top-0 bg-gray-50 dark:bg-white/[0.03]">
                            <tr>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-gray-500">Tanggal</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-gray-500 text-center">Jam</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-gray-500">Keterangan</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-wider text-gray-500 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @for ($i = 1; $i <= 3; $i++)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">{{ now()->subDays($i)->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm text-center font-bold text-gray-800 dark:text-white">4 Jam</td>
                                <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 italic">Lembur Shift Malam - Produksi...</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <button @click="showEditOvertimeModal = true; selectedOvertimeDate = '{{ now()->subDays($i)->format('d M Y') }}'; selectedOvertimeHours = 4; selectedOvertimeNote = 'Lembur Shift Malam - Produksi'"
                                                class="p-1.5 text-gray-400 hover:text-brand-500 transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button class="p-1.5 text-gray-400 hover:text-red-500 transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 flex justify-end pt-6 border-t border-gray-100 dark:border-gray-800">
                <x-ui.button variant="outline" @click="showDetailModal = false">Tutup</x-ui.button>
            </div>
        </div>
    </div>
</template>
