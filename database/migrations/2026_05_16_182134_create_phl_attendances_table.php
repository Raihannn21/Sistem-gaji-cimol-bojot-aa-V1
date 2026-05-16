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
        Schema::create('phl_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phl_payroll_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('scan_in')->nullable();
            $table->time('scan_out')->nullable();
            $table->integer('duration')->default(0)->comment('Durasi kerja normal (max 8 jam)');
            $table->string('note')->nullable();
            $table->timestamps();

            // Mencegah duplikasi absensi karyawan di hari yang sama untuk periode tersebut
            $table->unique(['phl_payroll_period_id', 'employee_id', 'date'], 'phl_attendance_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phl_attendances');
    }
};
