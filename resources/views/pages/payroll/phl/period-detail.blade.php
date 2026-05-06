@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl" x-data="{ 
        showSlipModal: false,
        selectedEmployee: { name: '', nrp: '' },
        openSlip(name, nrp) {
            this.selectedEmployee = { name, nrp };
            this.showSlipModal = true;
        }
    }">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('payroll.phl.periods') }}" class="mb-2 inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-brand-500 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Daftar Periode
                </a>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Detail Periode: Juli 2025</h2>
                <div class="mt-1 flex items-center gap-2">
                    <span class="inline-flex rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-500/10 dark:text-green-500">Open</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Dibuat pada 01 Juli 2025 oleh Admin</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <x-ui.button variant="outline" className="flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export Excel
                </x-ui.button>
                <x-ui.button variant="primary" className="flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Finalisasi Periode
                </x-ui.button>
            </div>
        </div>

        <!-- Overview Stats -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Gaji Bersih</p>
                <h4 class="mt-2 text-xl font-bold text-gray-800 dark:text-white">Rp 450.750.000</h4>
                <div class="mt-2 flex items-center gap-1 text-xs text-green-600">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    <span>1.2% dari bulan lalu</span>
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Lembur</p>
                <h4 class="mt-2 text-xl font-bold text-gray-800 dark:text-white">Rp 42.300.000</h4>
                <p class="mt-2 text-xs text-gray-400">840 Jam Akumulatif</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Tunjangan Risiko</p>
                <h4 class="mt-2 text-xl font-bold text-gray-800 dark:text-white">Rp 12.000.000</h4>
                <p class="mt-2 text-xs text-gray-400">600 Transaksi (Rp 20k/ea)</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Karyawan Terdaftar</p>
                <h4 class="mt-2 text-xl font-bold text-gray-800 dark:text-white">120 Orang</h4>
                <p class="mt-2 text-xs text-gray-400">Semua Data Terproses</p>
            </div>
        </div>

        <!-- Breakdown Table -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Rincian Pembayaran Karyawan</h3>
                <div class="flex items-center gap-3">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-brand-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" placeholder="Cari nama atau NRP..." 
                               class="h-11 w-80 rounded-full border border-gray-200 bg-white pl-12 pr-5 text-sm transition-all focus:border-brand-500 focus:ring-4 focus:ring-brand-500/5 focus:outline-none dark:border-gray-800 dark:bg-white/5 dark:text-white">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-white/[0.02] border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Karyawan</th>
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">HK (Hari Kerja)</th>
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">Upah Pokok</th>
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">Lembur</th>
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">Risiko</th>
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">Total Bersih</th>
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @for ($i = 1; $i <= 5; $i++)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-brand-500/10 flex items-center justify-center text-brand-500 font-bold text-xs">
                                        {{ substr(['Ahmad Fauzi', 'Budi Santoso', 'Citra Lestari', 'Dedi Kurniawan', 'Eka Putri'][$i-1], 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ ['Ahmad Fauzi', 'Budi Santoso', 'Citra Lestari', 'Dedi Kurniawan', 'Eka Putri'][$i-1] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">NRP. 100{{ $i }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-400">26 Hari</td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-400 text-right">Rp 3.500.000</td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-400 text-right">Rp 450.000</td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-400 text-right">Rp 100.000</td>
                            <td class="px-5 py-4 text-sm font-bold text-brand-600 dark:text-brand-500 text-right">Rp 4.050.000</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center">
                                    <button class="p-2 text-gray-500 hover:text-brand-500 rounded-lg hover:bg-brand-50 transition-colors" 
                                            title="Lihat Slip Gaji"
                                            @click="openSlip('{{ ['Ahmad Fauzi', 'Budi Santoso', 'Citra Lestari', 'Dedi Kurniawan', 'Eka Putri'][$i-1] }}', '100{{ $i }}')">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50/50 dark:bg-white/[0.01] border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Menampilkan 5 dari 120 karyawan</p>
                    <div class="flex items-center gap-2">
                        <button class="rounded-lg border border-gray-200 bg-white px-3 py-1 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:bg-white/5 dark:text-gray-400 dark:hover:bg-white/10">Prev</button>
                        <button class="rounded-lg border border-gray-200 bg-white px-3 py-1 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:bg-white/5 dark:text-gray-400 dark:hover:bg-white/10">Next</button>
                    </div>
                </div>
            </div>
        </div>

        <x-payroll.phl.payslip-modal />
    </div>
@endsection
