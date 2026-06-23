@props(['period' => null, 'employees' => []])
<template x-teleport="body">
    <div x-show="showOthersModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-400/50 backdrop-blur-sm p-4" 
         x-cloak>

        <div @click.away="showOthersModal = false" 
             x-show="showOthersModal"
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-[700px] rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11 shadow-xl max-h-[90vh] overflow-y-auto custom-scrollbar">

            <!-- Absolute Close Button -->
            <button @click="showOthersModal = false"
                class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 sm:right-6 sm:top-6 transition-colors duration-150">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z"
                        fill="currentColor" />
                </svg>
            </button>

            <!-- Header -->
            <div class="mb-8 pr-10">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Input Tunjangan Lain (PKWT)</h3>
                <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">Tambahkan tunjangan manual seperti THR, Bonus, atau insentif untuk karyawan kontrak.</p>
            </div>

            <!-- Form -->
            <form action="{{ $period ? url('/payroll/pkwt/periods/' . $period->id . '/other-allowance') : '#' }}" method="POST" class="space-y-6 pb-32"
                @submit="validateForm($event)"
                @change-employee_id.window="if($event.detail) delete errors.employee_id"
                novalidate
                x-data="{
                    errors: {},
                    validateForm(e) {
                        this.errors = {};
                        let hasError = false;
                        
                        const employee_id = this.$el.querySelector('[name=employee_id]').value;
                        const allowance_type = this.$el.querySelector('[name=allowance_type]').value;
                        const amount = this.$el.querySelector('[name=amount]').value;

                        if (!employee_id) { this.errors.employee_id = 'Karyawan wajib dipilih.'; hasError = true; }
                        if (!allowance_type.trim()) { this.errors.allowance_type = 'Jenis tunjangan wajib diisi.'; hasError = true; }
                        if (!amount || parseFloat(String(amount).replace(/\D/g, '')) <= 0) { this.errors.amount = 'Nominal wajib diisi.'; hasError = true; }

                        if (hasError) {
                            e.preventDefault();
                            return false;
                        }
                        return true;
                    }
                }">
                @csrf
                <div class="space-y-6">
                    <x-form.select-custom label="Pilih Karyawan" name="employee_id" placeholder="Cari nama atau ID...">
                        @foreach($employees as $employee)
                            <x-form.select-item value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->no_id }})</x-form.select-item>
                        @endforeach
                    </x-form.select-custom>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Tunjangan</label>
                            <input type="text" name="allowance_type" placeholder="Contoh: THR, Bonus, dll"
                                @input="delete errors.allowance_type;"
                                :class="errors.allowance_type ? 'border-red-500 ring-4 ring-red-500/10' : 'border-gray-200'"
                                class="w-full rounded-xl border bg-transparent px-4 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:text-white dark:focus:border-brand-500 transition duration-150">
                            <p x-show="errors.allowance_type" x-text="errors.allowance_type" class="mt-1.5 text-xs text-red-500 font-medium"></p>
                        </div>
                        <x-form.input name="amount" label="Nominal (Rp)" prefix="Rp" placeholder="0" @input="formatCurrency($event.target); delete errors.amount;" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan / Catatan</label>
                        <textarea name="note" placeholder="Masukkan detail keterangan tunjangan..." rows="3"
                            class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:text-white dark:focus:border-brand-500 transition duration-150"></textarea>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                    <x-ui.button variant="outline" type="button" @click="showOthersModal = false" className="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">Batal</x-ui.button>
                    <x-ui.button variant="primary" type="submit" className="shadow-sm shadow-brand-500/10 transition-all hover:translate-y-[-1px]">Simpan Tunjangan</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</template>
