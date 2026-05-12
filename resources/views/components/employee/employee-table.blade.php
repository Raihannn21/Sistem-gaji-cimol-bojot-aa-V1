<div x-data="{
    employees: [
        { id: 1, name: 'Ahmad Fauzi', emp_no: 'EMP001', id_no: 'ID1001', nik: '3201234567890001', role: 'PHL', status: 'Aktif', team: 'T01', location: 'HO', salary: '5.000.000', bank_name: 'BCA', bank_account: '1234567890', email: 'ahmad@example.com', phone: '08123456789' },
        { id: 2, name: 'Siti Aminah', emp_no: 'EMP002', id_no: 'ID1002', nik: '3201234567890002', role: 'PKWT', status: 'Aktif', team: 'T02', location: 'Site A', salary: '4.500.000', bank_name: 'BCA', bank_account: '0987654321', email: 'siti@example.com', phone: '08129876543' },
        { id: 3, name: 'Budi Santoso', emp_no: 'EMP003', id_no: 'ID1003', nik: '3201234567890003', role: 'PHL', status: 'Resign', team: 'T03', location: 'Site B', salary: '4.000.000', bank_name: 'Mandiri', bank_account: '1122334455', email: 'budi@example.com', phone: '08125566778' },
    ],
    getStatusClass(status) {
        const classes = {
            'Aktif': 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500',
            'Resign': 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-500',
            'SPHK': 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400',
        };
        return classes[status] || 'bg-gray-50 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400';
    }
}">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[1000px]">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Nama / Emp No</p>
                        </th>
                        <th class="px-5 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Jabatan</p>
                        </th>
                        <th class="px-5 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tim & Lokasi</p>
                        </th>
                        <th class="px-5 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p>
                        </th>
                        <th class="px-5 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Gaji Pokok</p>
                        </th>
                        <th class="px-5 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Rekening</p>
                        </th>
                        <th class="px-5 py-3 text-center">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Aksi</p>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <template x-for="emp in employees" :key="emp.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.01]">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90" x-text="emp.name"></span>
                                        <span class="block text-gray-500 text-theme-xs dark:text-gray-400" x-text="'Emp: ' + emp.emp_no"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400" x-text="emp.role"></p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col">
                                    <span class="text-gray-800 text-theme-sm dark:text-white/90" x-text="'Tim: ' + emp.team"></span>
                                    <span class="text-gray-500 text-theme-xs dark:text-gray-400" x-text="emp.location"></span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium" :class="getStatusClass(emp.status)" x-text="emp.status"></span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400" x-text="'Rp ' + emp.salary"></p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-gray-800 text-theme-sm dark:text-white/90 font-medium" x-text="emp.bank_name"></p>
                                <p class="text-gray-500 text-theme-xs dark:text-gray-400" x-text="emp.bank_account"></p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="selectedEmployee = emp; showDetailModal = true" class="p-2 text-gray-500 hover:bg-gray-100 hover:text-brand-500 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-brand-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <button @click="selectedEmployee = { ...emp }; showEditModal = true" class="p-2 text-gray-500 hover:bg-gray-100 hover:text-brand-500 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-brand-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
