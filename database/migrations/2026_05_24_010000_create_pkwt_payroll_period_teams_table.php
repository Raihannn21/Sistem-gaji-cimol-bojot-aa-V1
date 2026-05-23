<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pkwt_payroll_period_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkwt_payroll_period_id')
                ->constrained('pkwt_payroll_periods')
                ->cascadeOnDelete();
            $table->foreignId('team_id')
                ->constrained('teams')
                ->cascadeOnDelete();
            $table->json('off_dates')->nullable();
            $table->integer('work_days');

            $table->unique(['pkwt_payroll_period_id', 'team_id'], 'period_team_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkwt_payroll_period_teams');
    }
};
