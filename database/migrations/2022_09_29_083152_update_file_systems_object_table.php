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
        Schema::table('file_system_objects', function (Blueprint $table) {
            $table->string('status', 2048)->default('present');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_system_objects', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
