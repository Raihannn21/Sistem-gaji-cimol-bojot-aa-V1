<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no')->unique();
            $table->string('no_id')->unique();
            $table->string('nik')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('team')->nullable();
            $table->string('location')->nullable();
            $table->enum('employment_type', ['PHL', 'PKWT']);
            $table->enum('status', ['Aktif', 'Resign', 'SPHK'])->default('Aktif');
            $table->decimal('salary_daily', 14, 2)->nullable();
            $table->decimal('salary_monthly', 14, 2)->nullable();
            $table->decimal('risk_daily_amount', 14, 2)->nullable();
            $table->decimal('bpjs_health', 14, 2)->nullable();
            $table->decimal('bpjs_tk', 14, 2)->nullable();
            $table->decimal('pph21', 14, 2)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
