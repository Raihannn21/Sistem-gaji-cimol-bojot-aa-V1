<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('team_id')
                ->nullable()
                ->after('phone')
                ->constrained('teams')
                ->nullOnDelete();
        });
        $oldTeams = DB::table('employees')
            ->whereNotNull('team')
            ->where('team', '<>', '')
            ->selectRaw('DISTINCT TRIM(team) as team_name')
            ->pluck('team_name');

        foreach ($oldTeams as $teamName) {
            if (!empty($teamName)) {
                $teamId = DB::table('teams')->where('name', $teamName)->value('id');

                if (!$teamId) {
                    $teamId = DB::table('teams')->insertGetId([
                        'name' => $teamName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('employees')
                    ->whereRaw('TRIM(team) = ?', [$teamName])
                    ->update(['team_id' => $teamId]);
            }
        }
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('team');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('team')->nullable()->after('phone');
        });

        $employees = DB::table('employees')
            ->join('teams', 'employees.team_id', '=', 'teams.id')
            ->select('employees.id', 'teams.name')
            ->get();

        foreach ($employees as $emp) {
            DB::table('employees')
                ->where('id', $emp->id)
                ->update(['team' => $emp->name]);
        }
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });
    }
};
