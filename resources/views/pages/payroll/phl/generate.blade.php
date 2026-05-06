@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl" x-data="{ 
        showConfirmModal: false,
        processing: false,
        generate() {
            this.processing = true;
            setTimeout(() => {
                this.processing = false;
                this.showConfirmModal = false;
                alert('Payroll berhasil digenerate!');
            }, 2000);
        }
    }">
        <div class="space-y-6">
            <!-- Header Actions (Standardized Title & Description) -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Generate Payroll PHL</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Proses kalkulasi akhir dan pembuatan slip gaji untuk periode aktif.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-brand-50 rounded-lg dark:bg-brand-500/10">
                        <p class="text-xs font-medium text-brand-600 dark:text-brand-400">Periode Aktif: <span class="font-bold">Juli 2025</span></p>
                    </div>
                    <x-ui.button variant="primary" @click="showConfirmModal = true" className="flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Generate Sekarang
                    </x-ui.button>
                </div>
            </div>

            <!-- Stats Overview (Standardized) -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Siap Generate</p>
                    <div class="mt-2 flex items-baseline gap-2">
                        <h4 class="text-xl font-bold text-gray-800 dark:text-white">142</h4>
                        <span class="text-xs text-gray-500">Karyawan</span>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Perlu Review</p>
                    <div class="mt-2 flex items-baseline gap-2">
                        <h4 class="text-xl font-bold text-gray-800 dark:text-white">8</h4>
                        <span class="text-xs text-gray-500">Karyawan</span>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Estimasi Gaji</p>
                    <div class="mt-2">
                        <h4 class="text-xl font-bold text-brand-600 dark:text-brand-500">Rp 482.500.000</h4>
                    </div>
                </div>
            </div>

            <!-- Detailed Table (Standardized) -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">KARYAWAN</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">GAJI POKOK</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">LEMBUR</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">TUNJ. RISIKO</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">TOTAL GAJI</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-center">STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @for ($i = 1; $i <= 8; $i++)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                <td class="px-5 py-4">
                                    <p class="text-sm font-bold text-gray-800 dark:text-white">{{ ['Ahmad Fauzi', 'Budi Santoso', 'Citra Lestari', 'Dedi Kurniawan', 'Eka Putri', 'Fahri Ali', 'Gita Nur', 'Hadi Su'][($i-1)%8] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">NRP. 100{{ $i }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-right text-gray-700 dark:text-gray-400">Rp 2.500.000</td>
                                <td class="px-5 py-4 text-sm text-right text-gray-700 dark:text-gray-400">Rp 450.000</td>
                                <td class="px-5 py-4 text-sm text-right text-gray-700 dark:text-gray-400">Rp 150.000</td>
                                <td class="px-5 py-4 text-sm text-right font-bold text-brand-600 dark:text-brand-500">Rp 3.100.000</td>
                                <td class="px-5 py-4 text-center">
                                    @if($i <= 6)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-[10px] font-bold text-green-700 dark:bg-green-500/10 dark:text-green-400">
                                            Ready
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-100 px-2.5 py-0.5 text-[10px] font-bold text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400">
                                            Review
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Confirm Generate Modal -->
        <x-payroll.phl.generate-confirm-modal />
    </div>
@endsection
