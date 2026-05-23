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
        Schema::table('pkwt_attendances', function (Blueprint $table) {
            $table->string('late_time')->nullable()->after('scan_out');
            $table->string('early_time')->nullable()->after('late_time');
        });

        Schema::table('phl_attendances', function (Blueprint $table) {
            $table->string('late_time')->nullable()->after('scan_out');
            $table->string('early_time')->nullable()->after('late_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkwt_attendances', function (Blueprint $table) {
            $table->dropColumn(['late_time', 'early_time']);
        });

        Schema::table('phl_attendances', function (Blueprint $table) {
            $table->dropColumn(['late_time', 'early_time']);
        });
    }
};
