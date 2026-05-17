<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pkwt_risk_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkwt_payroll_period_id')->constrained('pkwt_payroll_periods')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('amount', 12, 2);
            $table->string('note')->nullable();
            $table->timestamps();
            $table->unique(['pkwt_payroll_period_id', 'employee_id', 'date'], 'pkwt_risk_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkwt_risk_allowances');
    }
};
