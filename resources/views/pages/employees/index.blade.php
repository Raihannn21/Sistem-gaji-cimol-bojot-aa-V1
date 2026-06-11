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
            showModal: @js($errors->any()), 
            showEditModal: false, 
            showDetailModal: false,
            showImportModal: false,
            search: '',
            onlyIncomplete: false,
            selectedEmployee: {},
            errors: @js($errors->any() ? $errors->getMessages() : (object)[]),
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
            <!-- Judul Halaman -->
            <div class="mb-2">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Manajemen Karyawan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data induk, status, dan informasi detail karyawan.</p>
            </div>

            <!-- Baris Aksi: Responsif (Kolom di Mobile, Baris di Desktop) -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <!-- Kiri: Pencarian -->
                <div class="w-full md:w-1/3">
                    <div class="relative group">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        <input type="text" x-model="search" placeholder="Cari karyawan..."
                            class="h-11 w-full rounded-xl border border-gray-200 bg-white pl-12 pr-4 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 placeholder:text-gray-400 dark:placeholder:text-white/30 shadow-sm transition-all">
                    </div>
                </div>

                <!-- Kanan: Checkbox & Buttons -->
                <div class="flex flex-wrap items-center gap-3 w-full justify-between md:flex-nowrap md:w-auto md:justify-end md:gap-4">
                    <label class="flex items-center gap-2 cursor-pointer whitespace-nowrap">
                        <input type="checkbox" x-model="onlyIncomplete" 
                               class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 transition-colors">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Hanya Data Belum Lengkap</span>
                    </label>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <x-ui.button variant="outline" :startIcon="$importIcon" @click="showImportModal = true" className="h-11 px-4 whitespace-nowrap">
                            Import Excel
                        </x-ui.button>
                        
                        <x-ui.button variant="primary" :startIcon="$plusIcon" @click="showModal = true" className="h-11 px-6 whitespace-nowrap">
                            Tambah Karyawan
                        </x-ui.button>
                    </div>
                </div>
            </div>

            <!-- Stats Overview: Grid 2x2 di Mobile, Flex Row di Desktop -->
            <div class="grid grid-cols-2 gap-4 w-full md:flex md:flex-row md:flex-nowrap md:overflow-x-auto md:pb-2 md:custom-scrollbar">
                <!-- Total Karyawan -->
                <div class="w-full rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] md:flex-1 md:min-w-[200px]">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Total Karyawan</p>
                            <h4 class="mt-0.5 text-lg font-bold text-gray-800 dark:text-white/90">{{ $stats['total'] }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Aktif -->
                <div class="w-full rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] md:flex-1 md:min-w-[200px]">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Aktif</p>
                            <h4 class="mt-0.5 text-lg font-bold text-gray-800 dark:text-white/90">{{ $stats['aktif'] }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Resign -->
                <div class="w-full rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] md:flex-1 md:min-w-[200px]">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" /></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Resign</p>
                            <h4 class="mt-0.5 text-lg font-bold text-gray-800 dark:text-white/90">{{ $stats['resign'] }}</h4>
                        </div>
                    </div>
                </div>

                <!-- SPHK -->
                <div class="w-full rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] md:flex-1 md:min-w-[200px]">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-yellow-50 text-yellow-600 dark:bg-yellow-500/10 dark:text-yellow-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">SPHK</p>
                            <h4 class="mt-0.5 text-lg font-bold text-gray-800 dark:text-white/90">{{ $stats['sphk'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <x-employee.employee-table :employees="$employees" />
        </div>

        <!-- Employee Modal Components -->
        <x-employee.create-modal :teams="$teams" />
        <x-employee.edit-modal :teams="$teams" />
        <x-employee.detail-modal />
        <x-employee.import-modal />
    </div>
@endsection
