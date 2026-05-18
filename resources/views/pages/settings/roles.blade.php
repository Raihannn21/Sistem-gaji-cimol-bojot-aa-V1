@extends('layouts.app')

@php
    use Illuminate\Support\HtmlString;

    $title = 'User & Role';

    $plusIcon = new HtmlString('
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        ');

    if (!function_exists('getFirstLetter')) {
        function getFirstLetter($name) {
            return !empty(trim($name)) ? strtoupper(substr(trim($name), 0, 1)) : '?';
        }
    }

    if (!function_exists('getBgColorHex')) {
        function getBgColorHex($name) {
            $bgColors = [
                '#dc2626', // Red
                '#2563eb', // Blue
                '#16a34a', // Green
                '#ea580c', // Orange
                '#9333ea', // Purple
                '#db2777', // Pink
                '#0d9488', // Teal
                '#4f46e5', // Indigo
            ];
            $index = abs(crc32($name)) % count($bgColors);
            return $bgColors[$index];
        }
    }
@endphp

@section('content')
    <div class="mx-auto max-w-screen-2xl" x-data="{ 
            showModal: false, 
            showEditModal: false, 
            selectedUser: {},
            getStatusClass(status) {
                return status === 'Aktif' 
                    ? 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500' 
                    : 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-500';
            },
            editUser(user) {
                this.selectedUser = { ...user };
                this.showEditModal = true;
            }
        }">
        <div class="space-y-6">
            <!-- Notifications -->
            @if(session('success'))
                <div class="rounded-xl bg-green-50 p-4 text-xs font-semibold text-green-700 dark:bg-green-500/15 dark:text-green-500 shadow-sm border border-green-100 dark:border-green-500/25">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl bg-red-50 p-4 text-xs font-semibold text-red-700 dark:bg-red-500/15 dark:text-red-500 shadow-sm border border-red-100 dark:border-red-500/25">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Header Actions -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">User Management</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola akses pengguna, data profil, dan status keaktifan user sistem.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative group">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none transition-colors group-focus-within:text-brand-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" placeholder="Cari user..."
                            class="h-11 w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-12 pr-14 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 dark:focus:border-brand-500 sm:w-80 shadow-theme-xs">
                    </div>
                    
                    <x-ui.button variant="primary" :startIcon="$plusIcon" @click="showModal = true">
                        Tambah User
                    </x-ui.button>
                </div>
            </div>

            <!-- Table Section -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">User</th>
                                <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400 text-center">Nomor HP</th>
                                <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400 text-center">Status</th>
                                <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400 text-center">Login Terakhir</th>
                                <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <!-- Dynamic Premium Squircle Letter Avatar -->
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-white font-extrabold text-lg uppercase shadow-sm select-none" style="background-color: {{ getBgColorHex($user->name) }};">
                                            {{ getFirstLetter($user->name) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center text-sm font-medium text-gray-800 dark:text-white">
                                    {{ $user->phone }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusClass('{{ $user->status }}')">
                                        {{ $user->status }}
                                    </span>
                                </td>
                                @php
                                    $isOnline = false;
                                    try {
                                        $isOnline = \DB::table('sessions')
                                            ->where('user_id', $user->id)
                                            ->where('last_activity', '>=', time() - 300)
                                            ->exists();
                                    } catch (\Exception $e) {
                                        $isOnline = auth()->id() === $user->id;
                                    }
                                @endphp
                                <td class="px-5 py-4 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $isOnline ? 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-white/5 dark:text-gray-400' }}">
                                        <!-- Glowing CSS Ping Indicator -->
                                        <span class="relative flex h-2 w-2">
                                            @if($isOnline)
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                            @else
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-400 dark:bg-gray-600"></span>
                                            @endif
                                        </span>
                                        <span>{{ $isOnline ? 'Sedang Aktif' : 'Offline' }}</span>
                                    </div>
                                    @if(!$isOnline)
                                    <div class="mt-1 text-[10px] text-gray-400 dark:text-gray-500 font-medium">
                                        {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Belum pernah login' }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                @click="editUser({{ json_encode($user) }})" 
                                                class="p-2 text-gray-500 hover:bg-gray-100 hover:text-brand-500 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-brand-500" 
                                                title="Edit">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        
                                        @if(auth()->id() !== $user->id)
                                        <button type="button" 
                                                @click="$dispatch('open-delete-modal', { 
                                                    url: '{{ route('settings.roles.destroy', $user->id) }}',
                                                    message: 'Apakah Anda yakin ingin menghapus user {{ $user->name }}?'
                                                })"
                                                class="p-2 text-gray-500 hover:bg-gray-100 hover:text-red-500 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-red-500" 
                                                title="Hapus">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                        @else
                                        <!-- Placeholder to keep exact same grid spacing and width for perfect visual alignment -->
                                        <div class="w-9 h-9"></div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- User Modal Components -->
        <x-settings.roles.create-modal />
        <x-settings.roles.edit-modal />
        <x-common.delete-confirm />
    </div>
@endsection
