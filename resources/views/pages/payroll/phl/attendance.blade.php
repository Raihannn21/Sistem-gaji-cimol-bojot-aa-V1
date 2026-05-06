@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl">
        <div class="space-y-6">
            <!-- Header Actions (Standardized Title & Description) -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Rekap Absensi PHL</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Lihat dan tinjau rekapitulasi kehadiran harian karyawan.</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ $title }}
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Halaman {{ $title }} sedang dalam tahap pengembangan (Slicing).
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
