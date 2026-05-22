@props(['period' => null])
<template x-teleport="body">
    <div x-show="showEditOvertimeModal" 
         x-init="$watch('showEditOvertimeModal', value => value && $nextTick(() => { $el.querySelectorAll('[data-currency]').forEach(input => formatCurrency(input)); }))"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-400/50 backdrop-blur-sm p-4" 
         x-cloak>
        
        <div @click.away="showEditOvertimeModal = false" 
             x-show="showEditOvertimeModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-[700px] rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11 shadow-xl">
            
            <!-- Absolute Close Button -->
            <button @click="showEditOvertimeModal = false" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 sm:right-6 sm:top-6 transition-colors duration-150">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor"/></svg>
            </button>

            <!-- Header -->
            <div class="mb-8 pr-10">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Edit Data Lembur (PKWT)</h3>
                <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-semibold text-brand-600 dark:text-brand-400" x-text="selectedEmployee.name"></span>
                    <span class="mx-2 text-gray-300 dark:text-gray-700">•</span>
                    <span x-text="selectedOvertimeDateFormatted" class="font-medium text-gray-700 dark:text-gray-300"></span>
                </p>
            </div>

            <!-- Form -->
            <form :action="`/payroll/pkwt/periods/{{ $period->id }}/overtime/${selectedOvertimeId}`" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <input type="hidden" name="employee_id" :value="selectedEmployee.id">
                <input type="hidden" name="overtime_date" :value="selectedOvertimeDate">

                <!-- 2-Column Responsive Input Grid -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Jumlah Jam Lembur
                        </label>
                        <input type="number" name="hours" x-model="selectedOvertimeHours" @input="selectedOvertimeAmount = (parseFloat(selectedOvertimeHours) || 0) * (parseFloat(String(selectedOvertimeRate).replace(/\D/g, '')) || 0)" required min="1" class="w-full rounded-xl border border-gray-200 bg-transparent py-3 px-5 text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:text-white dark:focus:border-brand-500 transition duration-150" placeholder="Contoh: 4">
                    </div>

                    <div>
                        <x-form.input name="rate" label="Nominal Per Jam" prefix="Rp" data-currency placeholder="0" required x-model="selectedOvertimeRate" @input="formatCurrency($event.target); selectedOvertimeAmount = (parseFloat(selectedOvertimeHours) || 0) * (parseFloat(String(selectedOvertimeRate).replace(/\D/g, '')) || 0)" />
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Hasil (Otomatis)</label>
                    <div class="relative flex items-center group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <span class="text-sm font-semibold text-gray-550 dark:text-gray-400">Rp</span>
                        </div>
                        <input type="text" :value="new Intl.NumberFormat('id-ID').format(selectedOvertimeAmount)" readonly
                            class="pl-12 px-4 h-11 w-full rounded-lg border border-gray-200 bg-gray-50 dark:bg-gray-800 py-2.5 text-sm text-gray-500 outline-none dark:text-white/70 shadow-theme-xs">
                    </div>
                </div>

                <input type="hidden" name="amount" :value="selectedOvertimeAmount">

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Keterangan / Aktivitas
                    </label>
                    <textarea name="note" x-model="selectedOvertimeNote" rows="3" class="w-full rounded-xl border border-gray-200 bg-transparent py-3 px-5 text-gray-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:text-white dark:focus:border-brand-500 transition duration-150" placeholder="Jelaskan aktivitas lembur..."></textarea>
                </div>

                <!-- Action Footer -->
                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                    <x-ui.button variant="outline" type="button" @click="showEditOvertimeModal = false" className="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">Batal</x-ui.button>
                    <x-ui.button variant="primary" type="submit" className="shadow-sm shadow-brand-500/10 transition-all hover:translate-y-[-1px]">Simpan Perubahan</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</template>