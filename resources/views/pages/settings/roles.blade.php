@extends('layouts.app')

@php
    use Illuminate\Support\HtmlString;

    $title = 'User & Role';

    $plusIcon = new HtmlString('
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        ');
    
    $users = [
        [
            'name' => 'Musharof Chowdhury',
            'email' => 'randomuser@pimjo.com',
            'role' => 'Administrator',
            'status' => 'Aktif',
            'last_login' => '10 menit yang lalu',
            'avatar' => '/images/user/owner.png'
        ],
        [
            'name' => 'Raihan',
            'email' => 'raihan@cimolbojot.com',
            'role' => 'Payroll Manager',
            'status' => 'Aktif',
            'last_login' => '2 jam yang lalu',
            'avatar' => null
        ],
        [
            'name' => 'Siti Aminah',
            'email' => 'siti@cimolbojot.com',
            'role' => 'HR Staff',
            'status' => 'Non-Aktif',
            'last_login' => '1 hari yang lalu',
            'avatar' => null
        ],
    ];
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
            getRoleClass(role) {
                if (role === 'Administrator') return 'bg-purple-50 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400';
                if (role === 'Payroll Manager') return 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400';
                return 'bg-gray-50 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400';
            },
            editUser(user) {
                this.selectedUser = { ...user };
                this.showEditModal = true;
            }
        }">
        <div class="space-y-6">
            <!-- Header Actions -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">User & Role Management</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola akses pengguna, peran, dan izin sistem.</p>
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
                                <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400 text-center">Role</th>
                                <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400 text-center">Status</th>
                                <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400 text-center">Login Terakhir</th>
                                <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-5 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $user['name'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user['email'] }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getRoleClass('{{ $user['role'] }}')">
                                        {{ $user['role'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusClass('{{ $user['status'] }}')">
                                        {{ $user['status'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    {{ $user['last_login'] }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button class="p-1.5 text-gray-500 hover:text-brand-500 transition-colors" title="Edit" @click="editUser({{ json_encode($user) }})">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <button class="p-1.5 text-gray-500 hover:text-red-500 transition-colors" title="Hapus">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
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
    </div>
@endsection
