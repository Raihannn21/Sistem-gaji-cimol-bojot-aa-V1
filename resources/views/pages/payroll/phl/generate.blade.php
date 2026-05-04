@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl">
        <x-common.page-breadcrumb :pageName="$title" />

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
@endsection
