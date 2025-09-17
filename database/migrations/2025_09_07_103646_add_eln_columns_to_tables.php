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
            $table->string('submitted_through')->nullable()->after('molecules');
            $table->string('external_id')->nullable()->after('submitted_through')->comment('External ID from the source platform');
            $table->string('external_url', 2048)->nullable()->comment('External URL from the source platform');
            $table->string('tracking_item_name')->nullable()->comment('Tracking item name from the source platform');
            $table->json('processing_logs')->nullable()->default('[]')->comment('Processing logs of the ELN submission');
        });

        Schema::table('datasets', function (Blueprint $table) {
            $table->string('external_url', 2048)->nullable()->comment('External URL from the source platform');
        });

        Schema::table('file_system_objects', function (Blueprint $table) {
            $table->string('external_url', 2048)->nullable()->comment('External URL from the source platform');
        });

        Schema::table('samples', function (Blueprint $table) {
            $table->string('submitted_through')->nullable()->comment('Platform that submitted the sample');
        });

        Schema::table('drafts', function (Blueprint $table) {
            $table->string('tracking_item_name')->nullable()->comment('Tracking item name from the source platform');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studies', function (Blueprint $table) {
            $table->dropColumn('submitted_through');
            $table->dropColumn('external_id');
            $table->dropColumn('external_url');
            $table->dropColumn('tracking_item_name');
            $table->dropColumn('processing_logs');
        });

        Schema::table('datasets', function (Blueprint $table) {
            $table->dropColumn('external_url');
        });

        Schema::table('file_system_objects', function (Blueprint $table) {
            $table->dropColumn('external_url');
        });

        Schema::table('samples', function (Blueprint $table) {
            $table->dropColumn('submitted_through');
        });

        Schema::table('drafts', function (Blueprint $table) {
            $table->dropColumn('tracking_item_name');
        });
    }
};
