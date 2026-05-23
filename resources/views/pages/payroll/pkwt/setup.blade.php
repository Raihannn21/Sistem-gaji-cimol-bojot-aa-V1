@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl" x-data="setupManager()">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 dark:border-gray-800 pb-5">
                <div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('payroll.pkwt.periods.show', $period->id) }}" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-brand-500 transition-colors dark:bg-white/[0.03] dark:border-gray-800">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        </a>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Setup Tim & Hari Libur</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Atur tim yang aktif dan tandai hari libur mereka untuk periode ini.</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-brand-50/50 dark:bg-brand-500/5 px-4 py-2.5 rounded-2xl border border-brand-100/50 dark:border-brand-500/10">
                    <svg class="h-5 w-5 text-brand-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Periode</p>
                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $period->title }} ({{ $period->start_date->format('d/m/Y') }} - {{ $period->end_date->format('d/m/Y') }})</p>
                    </div>
                </div>
            </div>

            <!-- Form Setup -->
            <form action="{{ route('payroll.pkwt.periods.save-setup', $period->id) }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Kiri: Pemilihan Tim -->
                    <div class="space-y-6 lg:col-span-1">
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                            <h3 class="text-md font-bold text-gray-800 dark:text-white/90 mb-4 flex items-center gap-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-500/10 text-brand-500 text-xs font-bold">1</span>
                                Pilih Tim Aktif
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Centang Tim yang akan dimasukkan ke dalam payroll periode ini.</p>
                            
                            <div class="space-y-3">
                                @foreach($teams as $team)
                                    <label class="flex items-center justify-between p-4 rounded-xl border border-gray-100 dark:border-gray-800 hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-all cursor-pointer"
                                           :class="teamsState[{{ $team->id }}]?.selected ? 'border-brand-500 bg-brand-50/10 dark:border-brand-500/35' : ''">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" name="teams[]" value="{{ $team->id }}" 
                                                   x-model="teamsState[{{ $team->id }}].selected"
                                                   class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                                            <div>
                                                <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $team->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $team->employees_count }} Karyawan Aktif</p>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                            <h3 class="text-md font-bold text-gray-800 dark:text-white/90 mb-4 flex items-center gap-2">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Panduan Kalender
                            </h3>
                            <div class="space-y-3 text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 rounded-full bg-emerald-500 shrink-0"></span>
                                    <span><strong>Hari Kerja (Hijau):</strong> Karyawan masuk & dihitung tarif hariannya.</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 rounded-full bg-red-500 shrink-0"></span>
                                    <span><strong>Hari Libur (Merah):</strong> Hari libur terjadwal tim. Mengurangi total hari kerja efektif.</span>
                                </div>
                                <div class="border-t border-gray-100 dark:border-gray-800 pt-3 mt-2">
                                    <p>Silakan klik tanggal pada kalender tim di sebelah kanan untuk mengubah status hari (Kerja ⇆ Libur).</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kanan: Kalender Hari Libur per Tim -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                            <h3 class="text-md font-bold text-gray-800 dark:text-white/90 mb-6 flex items-center gap-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-500/10 text-brand-500 text-xs font-bold">2</span>
                                Tentukan Jadwal & Hari Libur
                            </h3>

                            <!-- Fallback jika tidak ada tim yang dicentang -->
                            <div x-show="!hasSelectedTeams()" class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-white/5 text-gray-400 mb-4">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white">Pilih Tim Terlebih Dahulu</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Silakan centang tim di panel kiri untuk membuka kalender konfigurasi hari libur.</p>
                            </div>

                            <!-- Kalender per Tim -->
                            <div class="space-y-8 divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($teams as $team)
                                    <div x-show="teamsState[{{ $team->id }}].selected" class="pt-6 first:pt-0 space-y-4">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                            <div>
                                                <h4 class="text-md font-extrabold text-brand-500">{{ $team->name }}</h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Atur kalender libur terjadwal untuk {{ $team->name }}.</p>
                                            </div>
                                            
                                            <!-- Ringkasan Hari -->
                                            <div class="flex gap-2">
                                                <div class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-gray-800 px-3 py-1.5 rounded-xl text-center">
                                                    <p class="text-[9px] font-bold uppercase tracking-wider text-gray-400">Total Periode</p>
                                                    <p class="text-sm font-bold text-gray-800 dark:text-white">{{ count($dates) }} Hari</p>
                                                </div>
                                                <div class="bg-emerald-50/50 dark:bg-emerald-500/5 border border-emerald-100/50 dark:border-emerald-500/10 px-3 py-1.5 rounded-xl text-center">
                                                    <p class="text-[9px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-500">Hari Kerja</p>
                                                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-500" x-text="calculateWorkDays({{ $team->id }}) + ' Hari'"></p>
                                                </div>
                                                <div class="bg-red-50/50 dark:bg-red-500/5 border border-red-100/50 dark:border-red-500/10 px-3 py-1.5 rounded-xl text-center">
                                                    <p class="text-[9px] font-bold uppercase tracking-wider text-red-500">Hari Libur</p>
                                                    <p class="text-sm font-bold text-red-500" x-text="teamsState[{{ $team->id }}].offDates.length + ' Hari'"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Grid Kalender -->
                                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2">
                                            @foreach($dates as $dateStr)
                                                @php
                                                    $dateObj = \Carbon\Carbon::parse($dateStr);
                                                    $dayName = $dateObj->isoFormat('dddd');
                                                    $dayShort = substr($dayName, 0, 3);
                                                    $dayNum = $dateObj->format('d');
                                                    $monthName = $dateObj->isoFormat('MMM');
                                                @endphp
                                                <button type="button" 
                                                        @click="toggleOffDate({{ $team->id }}, '{{ $dateStr }}')"
                                                        class="flex flex-col items-center justify-between p-3 rounded-xl border transition-all text-left group"
                                                        :class="isOffDate({{ $team->id }}, '{{ $dateStr }}') 
                                                            ? 'bg-red-50 border-red-200 text-red-800 dark:bg-red-500/10 dark:border-red-500/30 dark:text-red-400' 
                                                            : 'bg-emerald-50/30 border-emerald-100 text-emerald-800 hover:bg-emerald-50/60 dark:bg-emerald-500/5 dark:border-emerald-500/10 dark:text-emerald-400 dark:hover:bg-emerald-500/10'">
                                                    
                                                    <div class="w-full flex justify-between items-center mb-2">
                                                        <span class="text-[10px] font-bold uppercase tracking-wider opacity-85" 
                                                              :class="isOffDate({{ $team->id }}, '{{ $dateStr }}') ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'">
                                                            {{ $dayShort }}
                                                        </span>
                                                        
                                                        <span class="rounded px-1.5 py-0.5 text-[8px] font-bold uppercase tracking-wider"
                                                              :class="isOffDate({{ $team->id }}, '{{ $dateStr }}') ? 'bg-red-200/55 dark:bg-red-500/20 text-red-700 dark:text-red-300' : 'bg-emerald-100/70 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300'">
                                                            <span x-show="isOffDate({{ $team->id }}, '{{ $dateStr }}')">Libur</span>
                                                            <span x-show="!isOffDate({{ $team->id }}, '{{ $dateStr }}')">Kerja</span>
                                                        </span>
                                                    </div>
                                                    <div class="w-full">
                                                        <p class="text-lg font-black tracking-tight leading-none">{{ $dayNum }}</p>
                                                        <p class="text-[9px] font-medium opacity-70 mt-1">{{ $monthName }} {{ $dateObj->format('Y') }}</p>
                                                    </div>
                                                </button>
                                            @endforeach
                                        </div>

                                        <!-- Hidden Inputs untuk form submit tanggal libur -->
                                        <template x-for="date in teamsState[{{ $team->id }}].offDates" :key="date">
                                            <input type="hidden" :name="'off_dates[' + {{ $team->id }} + '][]'" :value="date">
                                        </template>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Action Footer -->
                            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                                <a href="{{ route('payroll.pkwt.periods.show', $period->id) }}" class="flex justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] transition-colors">
                                    Kembali
                                </a>
                                <button type="submit" 
                                        :disabled="!hasSelectedTeams()"
                                        class="flex justify-center rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-brand-500/10 transition-all">
                                    Simpan Setup Periode
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setupManager() {
            // Mapping existing setup jika ada
            const initialTeamsState = {};
            const totalPeriodDays = {{ count($dates) }};
            
            // Loop teams dari backend untuk menginisialisasi state di Alpine
            @foreach($teams as $team)
                @php
                    $periodTeam = $period->periodTeams->where('team_id', $team->id)->first();
                    $isSelected = $periodTeam ? true : false;
                    $offDatesArr = $periodTeam && $periodTeam->off_dates ? $periodTeam->off_dates : [];
                @endphp
                initialTeamsState[{{ $team->id }}] = {
                    selected: {{ $isSelected ? 'true' : 'false' }},
                    offDates: @json($offDatesArr)
                };
            @endforeach

            return {
                teamsState: initialTeamsState,
                totalPeriodDays: totalPeriodDays,

                hasSelectedTeams() {
                    return Object.values(this.teamsState).some(t => t.selected);
                },

                isOffDate(teamId, dateStr) {
                    return this.teamsState[teamId]?.offDates.includes(dateStr);
                },

                toggleOffDate(teamId, dateStr) {
                    if (!this.teamsState[teamId]) return;
                    
                    const index = this.teamsState[teamId].offDates.indexOf(dateStr);
                    if (index > -1) {
                        // Jika sudah ada, hapus (jadikan hari masuk)
                        this.teamsState[teamId].offDates.splice(index, 1);
                    } else {
                        // // Jika belum ada, tambahkan (jadikan hari libur)
                        this.teamsState[teamId].offDates.push(dateStr);
                    }
                },

                calculateWorkDays(teamId) {
                    const offDaysCount = this.teamsState[teamId]?.offDates.length || 0;
                    return Math.max(0, this.totalPeriodDays - offDaysCount);
                }
            }
        }
    </script>
@endsection
