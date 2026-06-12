@props(['employees' => []])

<div>
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[1000px]">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Nama / ID Karyawan</p>
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
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Kelengkapan</p>
                        </th>
                        <th class="px-5 py-3 text-center">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Aksi</p>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <template x-for="employee in paginatedEmployees()" :key="employee.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.01]">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90" x-text="employee.name"></span>
                                        <span class="block text-gray-500 text-theme-xs dark:text-gray-400" x-text="'ID: ' + employee.id_no"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400" x-text="employee.role"></p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col">
                                    <span class="text-gray-800 text-theme-sm dark:text-white/90" x-text="'Tim: ' + (employee.team || '-')"></span>
                                    <span class="text-gray-500 text-theme-xs dark:text-gray-400" x-text="employee.location || '-'"></span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium" 
                                      :class="getStatusClass(employee.status)" 
                                      x-text="employee.status"></span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400" x-text="formatRupiah(employee.salary)"></p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-gray-800 text-theme-sm dark:text-white/90 font-medium" x-text="employee.bank_name || '-'"></p>
                                <p class="text-gray-500 text-theme-xs dark:text-gray-400" x-text="employee.bank_account || '-'"></p>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border"
                                     :class="{
                                         'bg-green-50 text-green-700 border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20': employee.completeness_color === 'green',
                                         'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20': employee.completeness_color === 'blue',
                                         'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/20': employee.completeness_color === 'yellow',
                                         'bg-red-50 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20': employee.completeness_color === 'red' || !employee.completeness_color
                                     }">
                                    <span class="w-1.5 h-1.5 rounded-full" 
                                          :class="{
                                              'bg-green-500': employee.completeness_color === 'green',
                                              'bg-blue-500': employee.completeness_color === 'blue',
                                              'bg-yellow-500': employee.completeness_color === 'yellow',
                                              'bg-red-500': employee.completeness_color === 'red' || !employee.completeness_color
                                          }"></span>
                                    <span class="text-[11px] font-bold" x-text="(employee.completeness_percentage || 0) + '%'"></span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" @click="selectedEmployee = employee; showDetailModal = true" class="p-2 text-gray-500 hover:bg-gray-100 hover:text-brand-500 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-brand-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <button type="button" @click="selectedEmployee = employee; showEditModal = true" class="p-2 text-gray-500 hover:bg-gray-100 hover:text-brand-500 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-brand-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button type="button" 
                                            @click="$dispatch('open-delete-modal', { 
                                                url: '/employees/' + employee.id,
                                                message: 'Apakah Anda yakin ingin menghapus data karyawan ' + employee.name + '?'
                                            })"
                                            class="p-2 text-gray-500 hover:bg-gray-100 hover:text-red-500 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-red-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    
                    <tr x-show="filteredEmployees().length === 0">
                        <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                            Tidak ada karyawan yang ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Footer Controls -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            <div class="text-xs text-gray-500 dark:text-gray-400">
                Menampilkan <span class="font-bold text-gray-700 dark:text-white" x-text="filteredEmployees().length > 0 ? (currentPage - 1) * perPage + 1 : 0"></span> 
                sampai <span class="font-bold text-gray-700 dark:text-white" x-text="Math.min(currentPage * perPage, filteredEmployees().length)"></span> 
                dari <span class="font-bold text-gray-700 dark:text-white" x-text="filteredEmployees().length"></span> data
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-3">
                <button type="button" 
                        @click="currentPage = Math.max(1, currentPage - 1)" 
                        :disabled="currentPage === 1"
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 hover:text-brand-500 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-800 dark:bg-transparent transition-colors shadow-theme-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                
                <span class="text-xs font-bold text-gray-600 dark:text-gray-400">
                    Halaman <span x-text="currentPage"></span> dari <span x-text="totalPages()"></span>
                </span>
                
                <button type="button" 
                        @click="currentPage = Math.min(totalPages(), currentPage + 1)" 
                        :disabled="currentPage === totalPages()"
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 hover:text-brand-500 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-800 dark:bg-transparent transition-colors shadow-theme-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>
