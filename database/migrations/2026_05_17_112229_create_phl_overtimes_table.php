<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('phl_overtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phl_payroll_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('hours');
            $table->decimal('amount', 12, 2);
            $table->string('note')->nullable();
            $table->timestamps();

            // Mencegah duplikasi data lembur karyawan di tanggal yang sama untuk periode tersebut
            $table->unique(['phl_payroll_period_id', 'employee_id', 'date'], 'phl_overtime_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phl_overtimes');
    }
};
