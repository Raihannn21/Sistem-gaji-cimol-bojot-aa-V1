@props(['statuses'])

<div x-data="{
    statusChanges: @json($statuses),
    getStatusClass(type) {
        return type === 'Resign' 
            ? 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-500' 
            : 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400';
    }
}">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[1000px]">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Karyawan</p>
                        </th>
                        <th class="px-5 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Jenis</p>
                        </th>
                        <th class="px-5 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tanggal Efektif</p>
                        </th>
                        <th class="px-5 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Alasan</p>
                        </th>
                        <th class="px-5 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tim & Lokasi</p>
                        </th>
                        <th class="px-5 py-3 text-center">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Aksi</p>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <template x-for="item in statusChanges" :key="item.id">
                        <tr x-show="!search || 
                                    item.name.toLowerCase().includes(search.toLowerCase()) || 
                                    item.emp_no.toLowerCase().includes(search.toLowerCase()) || 
                                    item.no_id.toLowerCase().includes(search.toLowerCase()) || 
                                    item.nik.toLowerCase().includes(search.toLowerCase()) || 
                                    (item.reason && item.reason.toLowerCase().includes(search.toLowerCase()))"
                            class="hover:bg-gray-50 dark:hover:bg-white/[0.01]">
                            <td class="px-5 py-4">
                                <div>
                                    <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90" x-text="item.name"></span>
                                    <span class="block text-gray-500 text-theme-xs dark:text-gray-400" x-text="item.emp_no"></span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium" :class="getStatusClass(item.type)" x-text="item.type"></span>
                            </td>
                            <td class="px-5 py-4 text-gray-500 text-theme-sm dark:text-gray-400" x-text="item.date"></td>
                            <td class="px-5 py-4 text-gray-500 text-theme-sm dark:text-gray-400" x-text="item.reason"></td>
                            <td class="px-5 py-4 text-gray-500 text-theme-sm dark:text-gray-400">
                                <div class="flex flex-col">
                                    <span class="text-gray-800 text-theme-sm dark:text-white/90" x-text="item.team"></span>
                                    <span class="text-gray-500 text-theme-xs dark:text-gray-400" x-text="item.location"></span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="selectedItem = item; showDetailModal = true" class="p-2 text-gray-500 hover:bg-gray-100 hover:text-brand-500 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-white/5">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <form :action="`/employees/status/${item.id}`" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini dan mengembalikan status karyawan menjadi Aktif?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-500 hover:bg-gray-100 hover:text-red-500 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-white/5">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
