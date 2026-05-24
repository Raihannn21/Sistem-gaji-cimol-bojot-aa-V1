<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pkwt_attendances', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
        });

        Schema::table('phl_attendances', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
        });

        // Back-fill existing records
        DB::statement('UPDATE pkwt_attendances SET team_id = (SELECT team_id FROM employees WHERE employees.id = pkwt_attendances.employee_id) WHERE team_id IS NULL');
        DB::statement('UPDATE phl_attendances SET team_id = (SELECT team_id FROM employees WHERE employees.id = phl_attendances.employee_id) WHERE team_id IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkwt_attendances', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });

        Schema::table('phl_attendances', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });
    }
};
