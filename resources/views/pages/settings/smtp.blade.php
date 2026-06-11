@extends('layouts.app')

@php
    $title = 'Konfigurasi Brevo';
@endphp

@section('content')
    <div class="mx-auto max-w-screen-xl">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Konfigurasi Email Brevo</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Atur kredensial Brevo untuk pengiriman slip gaji dan notifikasi sistem.</p>
            </div>
        </div>

        <!-- Notifications -->
        @if(session('success'))
            <div class="mb-6 rounded-xl bg-green-50 p-4 text-sm font-semibold text-green-700 dark:bg-green-500/15 dark:text-green-500 shadow-sm border border-green-100 dark:border-green-500/25">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-700 dark:bg-red-500/15 dark:text-red-500 shadow-sm border border-red-100 dark:border-red-500/25">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form Card -->
        <div class="rounded-3xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            <!-- Card Header -->
            <div class="border-b border-gray-100 dark:border-gray-800 px-6 py-5 sm:px-8">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Pengaturan Server</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pastikan pengaturan ini sesuai dengan penyedia layanan email Anda.</p>
            </div>

            <!-- Card Body -->
            <form action="{{ route('settings.smtp.update') }}" method="POST" id="smtpForm">
                @csrf
                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Grid 1: Basic Config -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.input label="Mail Mailer" name="mail_mailer" value="{{ old('mail_mailer', $config->mail_mailer) }}" placeholder="smtp" required />
                        <x-form.input label="Mail Host" name="mail_host" value="{{ old('mail_host', $config->mail_host) }}" placeholder="smtp.gmail.com" required />
                    </div>

                    <!-- Grid 2: Port & Encryption -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.input label="Mail Port" type="number" name="mail_port" value="{{ old('mail_port', $config->mail_port) }}" placeholder="587" required />
                        
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Mail Encryption</label>
                            <div class="relative">
                                <select name="mail_encryption" class="w-full h-11 rounded-xl border border-gray-200 bg-white px-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white appearance-none transition-colors">
                                    <option value="tls" {{ old('mail_encryption', $config->mail_encryption) == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('mail_encryption', $config->mail_encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ empty(old('mail_encryption', $config->mail_encryption)) || old('mail_encryption', $config->mail_encryption) == 'null' ? 'selected' : '' }}>None</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-800">

                    <!-- Grid 3: Credentials -->
                    <div>
                        <h4 class="text-base font-bold text-gray-800 dark:text-white/90 mb-4">Kredensial Autentikasi</h4>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <x-form.input label="Mail Username" name="mail_username" value="{{ old('mail_username', $config->mail_username) }}" placeholder="email@perusahaan.com" required />
                            
                            <div class="space-y-2" x-data="{ showPass: false }">
                                <label class="mb-1.5 block text-sm font-medium {{ $errors->has('mail_password') ? 'text-red-500' : 'text-gray-700 dark:text-gray-400' }}">
                                    Mail Password
                                </label>
                                <div class="relative flex items-center">
                                    <input 
                                        :type="showPass ? 'text' : 'password'" 
                                        name="mail_password"
                                        placeholder="••••••••••••" 
                                        value="{{ old('mail_password', $config->mail_password) }}"
                                        required
                                        class="h-11 w-full rounded-lg border {{ $errors->has('mail_password') ? 'border-red-500 ring-4 ring-red-500/10' : 'border-gray-300 dark:border-gray-700' }} bg-white dark:bg-dark-900 pl-4 pr-10 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:text-white/90 dark:placeholder:text-white/30 shadow-theme-xs"
                                    >
                                    <button 
                                        type="button" 
                                        @click="showPass = !showPass" 
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none"
                                    >
                                        <svg x-show="!showPass" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="showPass" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                                @error('mail_password')
                                    <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-800">

                    <!-- Grid 4: Sender Info -->
                    <div>
                        <h4 class="text-base font-bold text-gray-800 dark:text-white/90 mb-4">Informasi Pengirim</h4>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <x-form.input label="Mail From Address" type="email" name="mail_from_address" value="{{ old('mail_from_address', $config->mail_from_address) }}" placeholder="no-reply@cimolbojot.com" required />
                            <x-form.input label="Mail From Name" name="mail_from_name" value="{{ old('mail_from_name', $config->mail_from_name) }}" placeholder="Payroll Cimol Bojot AA" required />
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-white/[0.02] px-6 py-5 sm:px-8 flex flex-col sm:flex-row items-center justify-end gap-3" x-data>
                    <x-ui.button variant="outline" type="button" class="w-full sm:w-auto" @click="$dispatch('open-test-modal')">Tes Koneksi</x-ui.button>
                    <x-ui.button variant="primary" type="submit" class="w-full sm:w-auto">Simpan Konfigurasi</x-ui.button>
                </div>
            </form>
        </div>

        <!-- Test Connection Modal -->
        <div x-data="{ open: false }" 
             @open-test-modal.window="open = true" 
             @keydown.escape.window="open = false"
             x-show="open" 
             class="relative z-50" 
             style="display: none;">
             
            <!-- Backdrop -->
            <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

            <!-- Modal Panel -->
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div x-show="open" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         @click.away="open = false" 
                         class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
                        
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Kirim Email Tes</h3>
                            <p class="text-sm text-gray-500 mt-1 dark:text-gray-400">Pengujian ini akan menggunakan kredensial yang sedang ada di form Anda saat ini.</p>
                        </div>
                        
                        <!-- Form submits to test route, but copies all values from main smtpForm -->
                        <form action="{{ route('settings.smtp.test') }}" method="POST" id="testEmailForm">
                            @csrf
                            <!-- Hidden inputs to carry main form data -->
                            <input type="hidden" name="mail_mailer" value="" id="t_mailer">
                            <input type="hidden" name="mail_host" value="" id="t_host">
                            <input type="hidden" name="mail_port" value="" id="t_port">
                            <input type="hidden" name="mail_username" value="" id="t_user">
                            <input type="hidden" name="mail_password" value="" id="t_pass">
                            <input type="hidden" name="mail_encryption" value="" id="t_enc">
                            <input type="hidden" name="mail_from_address" value="" id="t_from_addr">
                            <input type="hidden" name="mail_from_name" value="" id="t_from_name">

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Tujuan Tes</label>
                                    <input type="email" name="test_email" required placeholder="email.anda@gmail.com" 
                                           class="w-full h-11 rounded-xl border border-gray-200 bg-white px-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white transition-colors" />
                                </div>
                                <div class="mt-6 flex justify-end gap-3">
                                    <x-ui.button variant="outline" type="button" @click="open = false">Batal</x-ui.button>
                                    <x-ui.button variant="primary" type="submit" 
                                        onclick="
                                            document.getElementById('t_mailer').value = document.querySelector('[name=mail_mailer]').value;
                                            document.getElementById('t_host').value = document.querySelector('[name=mail_host]').value;
                                            document.getElementById('t_port').value = document.querySelector('[name=mail_port]').value;
                                            document.getElementById('t_user').value = document.querySelector('[name=mail_username]').value;
                                            document.getElementById('t_pass').value = document.querySelector('[name=mail_password]').value;
                                            document.getElementById('t_enc').value = document.querySelector('[name=mail_encryption]').value;
                                            document.getElementById('t_from_addr').value = document.querySelector('[name=mail_from_address]').value;
                                            document.getElementById('t_from_name').value = document.querySelector('[name=mail_from_name]').value;
                                        ">
                                        Kirim Tes
                                    </x-ui.button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
