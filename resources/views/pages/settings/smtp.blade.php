@extends('layouts.app')

@php
    $title = 'Konfigurasi SMTP';
@endphp

@section('content')
    <div class="mx-auto max-w-screen-xl">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Konfigurasi Email (SMTP)</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Atur kredensial SMTP untuk pengiriman slip gaji dan notifikasi sistem.</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="rounded-3xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            <!-- Card Header -->
            <div class="border-b border-gray-100 dark:border-gray-800 px-6 py-5 sm:px-8">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Pengaturan Server</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pastikan pengaturan ini sesuai dengan penyedia layanan email Anda.</p>
            </div>

            <!-- Card Body -->
            <form action="#" method="POST">
                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Grid 1: Basic Config -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.input label="Mail Mailer" name="mail_mailer" value="smtp" placeholder="smtp" required />
                        <x-form.input label="Mail Host" name="mail_host" value="smtp.gmail.com" placeholder="smtp.gmail.com" required />
                    </div>

                    <!-- Grid 2: Port & Encryption -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.input label="Mail Port" type="number" name="mail_port" value="587" placeholder="587" required />
                        
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Mail Encryption</label>
                            <div class="relative">
                                <select name="mail_encryption" class="w-full h-11 rounded-xl border border-gray-200 bg-white px-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white appearance-none transition-colors">
                                    <option value="tls" selected>TLS</option>
                                    <option value="ssl">SSL</option>
                                    <option value="">None</option>
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
                            <x-form.input label="Mail Username" name="mail_username" placeholder="email@perusahaan.com" required />
                            <x-form.input label="Mail Password" type="password" name="mail_password" placeholder="••••••••••••" required />
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-800">

                    <!-- Grid 4: Sender Info -->
                    <div>
                        <h4 class="text-base font-bold text-gray-800 dark:text-white/90 mb-4">Informasi Pengirim</h4>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <x-form.input label="Mail From Address" type="email" name="mail_from_address" placeholder="no-reply@cimolbojot.com" required />
                            <x-form.input label="Mail From Name" name="mail_from_name" placeholder="Payroll Cimol Bojot AA" required />
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-white/[0.02] px-6 py-5 sm:px-8 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <x-ui.button variant="outline" type="button" class="w-full sm:w-auto">Tes Koneksi</x-ui.button>
                    <x-ui.button variant="primary" type="submit" class="w-full sm:w-auto">Simpan Konfigurasi</x-ui.button>
                </div>
            </form>
        </div>
    </div>
@endsection
