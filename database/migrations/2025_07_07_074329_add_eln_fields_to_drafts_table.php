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
        Schema::table('drafts', function (Blueprint $table) {
            $table->string('eln')->nullable()->comment('ELN system type (chemotion, labkey, etc.)');
            $table->string('external_id')->nullable()->index()->comment('External system identifier for ELN integration');
            $table->string('callback_url')->nullable()->comment('URL to callback after processing');
            $table->string('zip_url')->nullable()->comment('URL to ZIP file for download');
            $table->date('release_date')->nullable()->comment('Date when the draft should be released');
            $table->string('status')->nullable()->comment('Processing status of the ELN submission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drafts', function (Blueprint $table) {
            $table->dropColumn(['eln', 'external_id', 'callback_url', 'zip_url', 'release_date', 'status']);
        });
    }
};
