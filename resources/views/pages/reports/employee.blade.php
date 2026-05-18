@extends('layouts.app')

@section('content')
    <script>
        window.employeeData = @json($employees);
    </script>
    <div class="mx-auto max-w-screen-2xl" x-data="{ 
        searchQuery: '',
        selectedEmployee: null,
        showSlipModal: false,
        selectedSlip: {},
        employees: window.employeeData,
        payrollHistory: [],
        isLoadingHistory: false,
            filteredEmployees() {
                var query = this.searchQuery.toLowerCase();
                return this.employees.filter(function(e) {
                    var nameMatch = e.name && e.name.toLowerCase().indexOf(query) !== -1;
                    var nrpMatch = e.emp_no && e.emp_no.toLowerCase().indexOf(query) !== -1;
                    return nameMatch || nrpMatch;
                });
            },
            selectEmployee(emp) {
                this.selectedEmployee = emp;
                if (!emp) {
                    this.payrollHistory = [];
                    return;
                }
                this.isLoadingHistory = true;
                this.payrollHistory = [];
                var self = this;
                fetch('/reports/employee/' + emp.id + '/history')
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        self.payrollHistory = data;
                        self.isLoadingHistory = false;
                    })
                    .catch(function(err) {
                        console.error(err);
                        self.isLoadingHistory = false;
                    });
            }
        }">

        <!-- Header Section -->
        <div class="mb-6 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Laporan Per Karyawan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Rincian riwayat penggajian dan administrasi individu
                    karyawan.</p>
            </div>
        </div>

        <!-- Main Grid: 2 Columns (Employee List & Detailed Report) -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Sidebar: Search & List -->
            <div class="space-y-6">
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wide mb-6">Cari Karyawan
                    </h3>

                    <!-- Search Input -->
                    <div class="relative mb-6">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" x-model="searchQuery" placeholder="Nama atau NRP..."
                            class="block h-12 w-full rounded-xl border border-gray-100 bg-gray-50/50 pl-12 pr-4 text-sm font-medium text-gray-700 outline-none transition-all focus:border-brand-500 focus:bg-white dark:border-gray-800 dark:bg-white/[0.02] dark:text-white">
                    </div>

                    <!-- Employee List -->
                    <div class="space-y-2 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                        <template
                            x-for="emp in filteredEmployees()"
                            :key="emp.id">
                            <div @click="selectEmployee(emp)"
                                :class="selectedEmployee?.id === emp.id ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10' : 'border-gray-50 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.02]'"
                                class="flex items-center gap-4 p-4 rounded-xl border transition-all cursor-pointer group">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 font-bold text-gray-400 dark:bg-gray-800 group-hover:bg-brand-100 group-hover:text-brand-600 transition-colors"
                                    x-text="emp.name.charAt(0)"></div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-800 dark:text-white truncate" x-text="emp.name">
                                    </p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5"
                                        x-text="'NRP. ' + emp.emp_no + ' • ' + emp.employment_type"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Main Content: Detailed Report -->
            <div>
                <template x-if="selectedEmployee">
                    <div class="space-y-6">
                        <!-- Employee Summary Header: Premium Design -->
                        <div
                            class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                            <div class="flex items-center gap-5">
                                <!-- Premium Avatar -->
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-brand-500 text-2xl font-bold text-white shadow-lg shadow-brand-500/10"
                                    x-text="selectedEmployee.name.charAt(0)"></div>

                                <div class="min-w-0 flex-1">
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight truncate"
                                        x-text="selectedEmployee.name"></h2>
                                    <div class="mt-2.5 flex flex-wrap gap-2">
                                        <!-- Location Badge -->
                                        <div
                                            class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-2.5 py-1 text-[10px] font-bold text-brand-600 dark:bg-brand-500/10">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span x-text="selectedEmployee.location || 'Padang, Indonesia'"></span>
                                        </div>
                                        <!-- Status Badge -->
                                        <div
                                            class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-2.5 py-1 text-[10px] font-bold text-brand-600 dark:bg-brand-500/10">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="selectedEmployee.employment_type"></span>
                                        </div>
                                        <!-- NRP Badge -->
                                        <div
                                            class="inline-flex items-center gap-1 rounded-full bg-gray-50 px-2.5 py-1 text-[10px] font-bold text-gray-500 dark:bg-white/5 dark:text-gray-400">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            <span x-text="selectedEmployee.emp_no"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payroll History -->
                        <div
                            class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wide">Riwayat Penggajian</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="bg-gray-50/50 dark:bg-white/[0.01]">
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400 tracking-wide">
                                                Periode</th>
                                            <th
                                                class="px-6 py-4 text-xs font-bold uppercase text-gray-400 tracking-wide text-right">
                                                Kehadiran</th>
                                            <th
                                                class="px-6 py-4 text-xs font-bold uppercase text-gray-400 tracking-wide text-right">
                                                Total Gaji</th>
                                            <th
                                                class="px-6 py-4 text-xs font-bold uppercase text-gray-400 tracking-wide text-center">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                        <!-- Loading state -->
                                        <template x-if="isLoadingHistory">
                                            <tr>
                                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <svg class="animate-spin h-5 w-5 text-brand-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        Memuat riwayat penggajian...
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        
                                        <!-- Empty state -->
                                        <template x-if="!isLoadingHistory && payrollHistory.length === 0">
                                            <tr>
                                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                                    Belum ada riwayat penggajian untuk karyawan ini.
                                                </td>
                                            </tr>
                                        </template>

                                        <!-- Data rows -->
                                        <template x-if="!isLoadingHistory && payrollHistory.length > 0">
                                            <template x-for="p in payrollHistory" :key="p.period_id">
                                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                                    <td class="px-6 py-4 text-sm font-bold text-gray-800 dark:text-white/90" x-text="p.period"></td>
                                                    <td class="px-6 py-4 text-right text-sm font-medium text-gray-600 dark:text-gray-400 tabular-nums" x-text="p.days_worked + ' Hari'"></td>
                                                    <td class="px-6 py-4 text-right text-sm font-bold text-brand-600 tabular-nums" x-text="'Rp ' + p.total"></td>
                                                    <td class="px-6 py-4 text-center">
                                                        <button @click="selectedSlip = { name: selectedEmployee.name, nrp: selectedEmployee.emp_no, total: p.total, type: p.type, period: p.period, detail: p }; showSlipModal = true" 
                                                            class="text-gray-400 hover:text-brand-500 transition-colors p-2 hover:bg-brand-50 rounded-lg dark:hover:bg-brand-500/10 group">
                                                            <svg class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="!selectedEmployee">
                    <div
                        class="flex flex-col items-center justify-center h-[600px] rounded-2xl border-2 border-dashed border-gray-100 dark:border-gray-800">
                        <div
                            class="h-24 w-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 dark:bg-white/5">
                            <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-400 uppercase tracking-wide">Pilih Karyawan</h3>
                        <p class="text-sm text-gray-400 font-medium mt-2">Silahkan pilih karyawan di sisi kiri untuk melihat
                            laporan detail.</p>
                    </div>
                </template>
            </div>
        </div>

        <x-report.employee-slip-modal />
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #374151;
        }
    </style>
@endsection
