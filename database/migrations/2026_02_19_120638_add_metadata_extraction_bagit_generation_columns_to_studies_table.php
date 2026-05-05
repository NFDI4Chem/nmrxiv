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
            $table->string('metadata_bagit_generation_status')->nullable()->after('is_public');
            $table->json('metadata_bagit_generation_logs')->nullable()->after('metadata_bagit_generation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studies', function (Blueprint $table) {
            $table->dropColumn(['metadata_bagit_generation_status', 'metadata_bagit_generation_logs']);
        });
    }
};
