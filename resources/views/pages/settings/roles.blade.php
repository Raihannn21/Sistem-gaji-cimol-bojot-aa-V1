@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl">
        <x-common.page-breadcrumb :pageName="$title" />

        <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                <h3 class="font-medium text-black dark:text-white">
                    {{ $title }}
                </h3>
            </div>
            <div class="p-6.5">
                <p>Halaman {{ $title }} sedang dalam tahap pengembangan (Slicing).</p>
            </div>
        </div>
    </div>
@endsection
