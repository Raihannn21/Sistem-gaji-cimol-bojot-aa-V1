@extends('layouts.app')

@php
    $title = 'Import & Export';
@endphp

@section('content')
    <div class="mx-auto max-w-screen-2xl">
        <div class="space-y-6">
            <!-- Header Actions (Standardized Title & Description) -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Import & Export Karyawan</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data massal dan unduh laporan data karyawan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Import Section -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] sm:p-8">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-5 dark:border-gray-800">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-500/10 text-brand-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Import Data Karyawan</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Unggah file Excel untuk tambah/update massal.</p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <div class="rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-8 text-center transition-colors hover:border-brand-500 dark:border-gray-800 dark:bg-white/[0.02]">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-sm dark:bg-gray-900">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h4 class="text-base font-semibold text-gray-800 dark:text-white">Klik atau seret file ke sini</h4>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Format yang didukung: .xlsx, .csv (Maks. 10MB)</p>

                                <input type="file" class="hidden" id="fileInput">
                                <x-ui.button variant="outline" className="mt-6" onclick="document.getElementById('fileInput').click()">
                                    Pilih File Excel
                                </x-ui.button>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between rounded-xl bg-brand-50 p-4 dark:bg-brand-500/10">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium text-brand-700 dark:text-brand-400">Belum punya formatnya?</span>
                            </div>
                            <a href="#" class="text-sm font-bold text-brand-600 hover:underline dark:text-brand-500">Download Template</a>
                        </div>
                    </div>
                </div>

                <!-- Export Section -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] sm:p-8">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-5 dark:border-gray-800">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Export Data & BCA Bisnis</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Unduh data untuk laporan atau transfer bank.</p>
                        </div>
                    </div>

                    <div class="mt-8 space-y-4">
                        <!-- BCA Bisnis Card -->
                        <div class="group relative rounded-2xl border border-gray-100 bg-white p-5 transition-all hover:border-brand-500 hover:shadow-theme-md dark:border-gray-800 dark:bg-white/[0.02]">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-500 text-white shadow-lg shadow-brand-500/20">
                                        <span class="text-xs font-black uppercase">BCA</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 dark:text-white">Format BCA Bisnis</h4>
                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">File mass transfer gaji karyawan (.xlsx)</p>
                                    </div>
                                </div>
                                <x-ui.button variant="primary" className="h-9 px-4 text-xs">Download</x-ui.button>
                            </div>
                            <div class="mt-4 flex items-center gap-4 border-t border-gray-50 pt-4 dark:border-gray-800">
                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Periode: Juli 2025</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>Total: 120 Karyawan</span>
                                </div>
                            </div>
                        </div>

                        <!-- Master Data Card -->
                        <div class="group relative rounded-2xl border border-gray-100 bg-white p-5 transition-all hover:border-blue-500 hover:shadow-theme-md dark:border-gray-800 dark:bg-white/[0.02]">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500 text-white shadow-lg shadow-blue-500/20">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 dark:text-white">Master Data Karyawan</h4>
                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Download seluruh biodata karyawan (.xlsx)</p>
                                    </div>
                                </div>
                                <x-ui.button variant="outline" className="h-9 px-4 text-xs border-blue-200 text-blue-600 hover:bg-blue-50">Download</x-ui.button>
                            </div>
                        </div>

                        <div class="mt-6 rounded-2xl bg-yellow-50 p-4 dark:bg-yellow-500/5 border border-yellow-100 dark:border-yellow-500/20">
                            <div class="flex gap-3">
                                <svg class="h-5 w-5 text-yellow-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-xs text-yellow-800 dark:text-yellow-500/80 leading-relaxed">
                                    <strong>Catatan Keamanan:</strong> File Export BCA Bisnis berisi data rekening dan nominal gaji yang sensitif. Pastikan file ini disimpan dan dikelola hanya oleh pihak yang berwenang.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection