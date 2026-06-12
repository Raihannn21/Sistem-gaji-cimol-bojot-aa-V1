@extends('layouts.app')

@php
    use Illuminate\Support\HtmlString;
    
    $title = 'Resign & PHK';
    
    $plusIcon = new HtmlString('
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
    ');
@endphp

@section('content')
    <script>
        window.initialStatusChanges = @json($statuses);
    </script>
    <div class="mx-auto max-w-screen-2xl" x-data="{ 
        showModal: false,
        showDetailModal: false,
        selectedItem: {},
        search: '',
        errors: {},
        statusChanges: window.initialStatusChanges,
        getStatusClass(type) {
            return type === 'Resign' 
                ? 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-500' 
                : 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400';
        }
    }">
        <div class="space-y-6">
            <!-- Header Actions (Standardized Title & Description) -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Resign & SPHK</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola riwayat pemberhentian kerja dan status kontrak karyawan.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative group">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none transition-colors group-focus-within:text-brand-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input x-model.debounce.150ms="search" type="text" placeholder="Cari data..." class="h-11 w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-12 pr-4 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 dark:focus:border-brand-500 sm:w-80 shadow-theme-xs">
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button variant="primary" :startIcon="$plusIcon" @click="showModal = true">
                        Input Status Baru
                    </x-ui.button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Records</p>
                            <h4 class="mt-1 text-xl font-bold text-gray-800 dark:text-white/90">{{ $stats['total'] }}</h4>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Karyawan Resign</p>
                            <h4 class="mt-1 text-xl font-bold text-gray-800 dark:text-white/90">{{ $stats['resign'] }}</h4>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Karyawan SPHK</p>
                            <h4 class="mt-1 text-xl font-bold text-gray-800 dark:text-white/90">{{ $stats['sphk'] }}</h4>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-50 text-yellow-600 dark:bg-yellow-500/10 dark:text-yellow-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <x-employee.status-table />
        </div>

        <!-- Modal Components -->
        <x-employee.status-modal :active-employees="$activeEmployees" />
        <x-employee.status-detail-modal />
    </div>
@endsection
