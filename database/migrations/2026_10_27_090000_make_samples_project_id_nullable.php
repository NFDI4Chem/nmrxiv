<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Samples published on their own (e.g. ELN submissions) are detached from the
     * staging project, which is deleted afterwards, so the column must allow NULL.
     */
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Rows detached from their project cannot satisfy NOT NULL again.
        DB::table('samples')->whereNull('project_id')->update(['project_id' => 0]);

        Schema::table('samples', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable(false)->change();
        });
    }
};
