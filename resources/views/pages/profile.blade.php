@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="User Profile" />
    
    <div class="space-y-6">
        <!-- Session Notifications -->
        @if(session('success'))
            <div class="rounded-xl bg-green-50 p-4 text-xs font-semibold text-green-700 dark:bg-green-500/15 dark:text-green-500 shadow-sm border border-green-100 dark:border-green-500/25">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-xl bg-red-50 p-4 text-xs font-semibold text-red-700 dark:bg-red-500/15 dark:text-red-500 shadow-sm border border-red-100 dark:border-red-500/25">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">Profile</h3>
            <x-profile.profile-card :user="$user" />
            <x-profile.personal-info-card :user="$user" />
        </div>
    </div>
@endsection
