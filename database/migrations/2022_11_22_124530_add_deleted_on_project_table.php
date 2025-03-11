<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to add deleted on column in project table.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->timestamp('deleted_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('deleted_on');
        });
    }
};
