@extends('layouts.app')

@php
    use Illuminate\Support\HtmlString;

    $title = 'Data Karyawan';

    $exportIcon = new HtmlString('
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
        ');

    $importIcon = new HtmlString('
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
        ');

    $plusIcon = new HtmlString('
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        ');
@endphp

@section('content')
    <div class="mx-auto max-w-screen-2xl" x-data="{ 
            showModal: false, 
            showEditModal: false, 
            showDetailModal: false,
            selectedEmployee: {},
            getStatusClass(status) {
                const classes = {
                    'Aktif': 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500',
                    'Resign': 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-500',
                    'SPHK': 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400',
                };
                return classes[status] || 'bg-gray-50 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400';
            }
        }">
        <div class="space-y-6">
            <!-- Header Actions (Standardized with Title & Description) -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Manajemen Karyawan</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data induk, status, dan informasi detail
                        karyawan.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative group">
                        <span
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none transition-colors group-focus-within:text-brand-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" placeholder="Cari karyawan..."
                            class="h-11 w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-12 pr-14 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 dark:focus:border-brand-500 sm:w-80 shadow-theme-xs">
                        <div
                            class="absolute right-2.5 top-1/2 inline-flex -translate-y-1/2 items-center gap-0.5 rounded-lg border border-gray-200 bg-gray-50 px-[7px] py-[4.5px] text-xs -tracking-[0.2px] text-gray-400 dark:border-gray-800 dark:bg-white/[0.03]">
                            <span>⌘</span>
                            <span>K</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button variant="outline" :startIcon="$exportIcon">
                        Export Excel
                    </x-ui.button>
                    <x-ui.button variant="outline" :startIcon="$importIcon">
                        Import Excel
                    </x-ui.button>
                    <x-ui.button variant="primary" :startIcon="$plusIcon" @click="showModal = true">
                        Tambah Karyawan
                    </x-ui.button>
                </div>
            </div>

            <!-- Stats Overview: Paksa Satu Baris Horizontal -->
            <div class="flex flex-row flex-nowrap gap-4 w-full overflow-x-auto pb-2 custom-scrollbar">
                <!-- Total Karyawan -->
                <div class="flex-1 min-w-[200px] rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Total Karyawan</p>
                            <h4 class="mt-0.5 text-lg font-bold text-gray-800 dark:text-white/90">1.250</h4>
                        </div>
                    </div>
                </div>

                <!-- Aktif -->
                <div class="flex-1 min-w-[200px] rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Aktif</p>
                            <h4 class="mt-0.5 text-lg font-bold text-gray-800 dark:text-white/90">1.180</h4>
                        </div>
                    </div>
                </div>

                <!-- Resign -->
                <div class="flex-1 min-w-[200px] rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" /></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Resign</p>
                            <h4 class="mt-0.5 text-lg font-bold text-gray-800 dark:text-white/90">45</h4>
                        </div>
                    </div>
                </div>

                <!-- SPHK -->
                <div class="flex-1 min-w-[200px] rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-yellow-50 text-yellow-600 dark:bg-yellow-500/10 dark:text-yellow-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">SPHK</p>
                            <h4 class="mt-0.5 text-lg font-bold text-gray-800 dark:text-white/90">25</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <x-employee.employee-table />
        </div>

        <!-- Employee Modal Components -->
        <x-employee.create-modal />
        <x-employee.edit-modal />
        <x-employee.detail-modal />
    </div>
@endsection