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
            $table->json('authors')->nullable()->comment('Authors information from ELN metadata');
            $table->json('citations')->nullable()->comment('Citations information from ELN metadata');
            $table->json('molecules')->nullable()->comment('Molecules information from ELN metadata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studies', function (Blueprint $table) {
            $table->dropColumn(['authors', 'citations', 'molecules']);
        });
    }
};
