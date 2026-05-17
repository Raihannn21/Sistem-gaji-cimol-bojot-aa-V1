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
        Schema::create('phl_risk_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phl_payroll_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('amount', 12, 2);
            $table->string('note')->nullable();
            $table->timestamps();
            $table->unique(['phl_payroll_period_id', 'employee_id', 'date'], 'phl_risk_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phl_risk_allowances');
    }
};
