@props(['period', 'employees'])
<template x-teleport="body">
    <div x-show="showRiskModal" 
         x-data="{
            riskRates: @js($employees->pluck('risk_daily_amount', 'id')),
            amount: '',
            onEmployeeChange(val) {
                if (this.riskRates[val]) {
                    this.amount = parseInt(this.riskRates[val]);
                    // Format the input field immediately
                    this.$nextTick(() => {
                        const input = this.$el.querySelector('[data-currency]');
                        if (input) formatCurrency(input);
                    });
                } else {
                    this.amount = '';
                }
            }
         }"
         @change-employee_id="onEmployeeChange($event.detail)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-400/50 backdrop-blur-sm p-4" x-cloak>

        <div @click.away="showRiskModal = false" x-show="showRiskModal"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative w-[500px] max-w-full rounded-3xl bg-white p-6 shadow-xl dark:bg-gray-900 sm:p-8 max-h-[90vh] overflow-y-auto custom-scrollbar">

            <button @click="showRiskModal = false"
                class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 sm:right-6 sm:top-6">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z"
                        fill="currentColor" />
                </svg>
            </button>

            <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Input Tunjangan Risiko</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih karyawan dan tentukan nominal tunjangan risiko.</p>

            <form action="{{ route('payroll.phl.periods.store-risk', $period->id) }}" method="POST" class="mt-8 space-y-5 pb-40">
                @csrf
                <div class="space-y-5">
                    <x-form.select-custom label="Pilih Karyawan" name="employee_id" placeholder="Cari nama atau NRP...">
                        @foreach($employees as $employee)
                            <x-form.select-item value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->emp_no }})</x-form.select-item>
                        @endforeach
                    </x-form.select-custom>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-form.date-picker label="Tanggal" name="risk_date" placeholder="Pilih Tanggal" dateFormat="d-m-Y" :static="true" required />
                        
                        <x-form.input name="amount" label="Nominal (Rp)" prefix="Rp" data-currency placeholder="0" required x-model="amount" @input="formatCurrency($event.target)" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan / Alasan</label>
                        <textarea name="note" placeholder="Contoh: Pekerjaan di area berisiko tinggi..." rows="3"
                            class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:text-white"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                    <x-ui.button variant="outline" type="button" @click="showRiskModal = false">Batal</x-ui.button>
                    <x-ui.button variant="primary" type="submit">Simpan Data</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</template>