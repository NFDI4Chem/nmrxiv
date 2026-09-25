<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Unguessable code used in the shareable compound library URL (/library/{code}),
     * so workspace libraries cannot be enumerated by team id or username.
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('compound_library_code', 32)->nullable()->after('personal_team');
        });

        DB::table('teams')
            ->whereNull('compound_library_code')
            ->lazyById()
            ->each(function (object $team): void {
                DB::table('teams')
                    ->where('id', $team->id)
                    ->update(['compound_library_code' => Str::random(16)]);
            });

        Schema::table('teams', function (Blueprint $table) {
            $table->string('compound_library_code', 32)->nullable(false)->change();
            $table->unique('compound_library_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropUnique(['compound_library_code']);
            $table->dropColumn('compound_library_code');
        });
    }
};
