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
        Schema::table('datasets', function (Blueprint $table) {
            $table->longText('solvent')->nullable();
            $table->longText('experiment')->nullable();
            $table->longText('probe')->nullable();
            $table->longText('temperature')->nullable();
            $table->longText('nucleus')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->dropColumn('solvent');
            $table->dropColumn('experiment');
            $table->dropColumn('probe');
            $table->dropColumn('temperature');
            $table->dropColumn('nucleus');
        });
    }
};
