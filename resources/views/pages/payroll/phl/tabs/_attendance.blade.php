<!-- Tab: Attendance -->
<div x-show="activeTab === 'attendance'" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
    class="space-y-6" x-cloak>
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div
            class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Data Absensi Periode Ini</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola data kehadiran harian karyawan PHL.
                </p>
            </div>
            <x-ui.button variant="outline" @click="showAttendanceImportModal = true" className="flex items-center gap-2 text-xs py-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Import Excel
            </x-ui.button>
        </div>

        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider">Karyawan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center">
                            Tanggal</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center">Scan
                            Masuk</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center">Scan
                            Pulang</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-center">
                            Durasi</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 tracking-wider text-right">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($period->attendances as $attendance)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400 font-bold text-sm">
                                        {{ substr($attendance->employee->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $attendance->employee->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">NRP. {{ $attendance->employee->emp_no ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                    {{ $attendance->scan_in ? \Carbon\Carbon::parse($attendance->scan_in)->format('H:i') : '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                    {{ $attendance->scan_out ? \Carbon\Carbon::parse($attendance->scan_out)->format('H:i') : '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold {{ $attendance->duration >= 8 ? 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400' : 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400' }}">
                                    {{ $attendance->duration }} Jam
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" class="text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-32 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 text-gray-300 dark:bg-white/5">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <h4 class="text-base font-bold text-gray-800 dark:text-white/90">Belum Ada Data Absensi</h4>
                                    <p class="mx-auto mt-1 max-w-xs text-sm text-gray-500 dark:text-gray-400">Silakan import file Excel absensi untuk memproses data kehadiran.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>