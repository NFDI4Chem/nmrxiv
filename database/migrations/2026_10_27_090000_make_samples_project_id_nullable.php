<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

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
        // Samples detached from their project cannot satisfy NOT NULL again and the
        // original project is not recorded, so refuse instead of inventing a project id.
        if (DB::table('samples')->whereNull('project_id')->exists()) {
            throw new RuntimeException(
                'Cannot make samples.project_id NOT NULL while samples without a project exist.'
            );
        }

        Schema::table('samples', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable(false)->change();
        });
    }
};
