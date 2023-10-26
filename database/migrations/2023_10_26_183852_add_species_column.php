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
        Schema::table('studies', function (Blueprint $table) {
            $table->longText('species')->nullable();
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->longText('species')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studies', function (Blueprint $table) {
            $table->dropColumn('species');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('species');
        });
    }
};
