<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `study_photo_path` used to hold a single SVG path (string) written by
     * the legacy per-study snapshot capture. It now holds a JSON array of
     * dataset photo paths, combined by nmrxiv:backfill-dataset-photo.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // Wrap any existing single-path string into a one-element JSON
            // array so no data is silently dropped by the type change.
            DB::statement(<<<'SQL'
                ALTER TABLE studies
                ALTER COLUMN study_photo_path TYPE jsonb
                USING CASE
                    WHEN study_photo_path IS NULL OR study_photo_path = '' THEN NULL
                    ELSE to_jsonb(ARRAY[study_photo_path])
                END
            SQL);
        } else {
            Schema::table('studies', function (Blueprint $table) {
                $table->json('study_photo_path')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(<<<'SQL'
                ALTER TABLE studies
                ALTER COLUMN study_photo_path TYPE varchar(2048)
                USING (study_photo_path ->> 0)
            SQL);
        } else {
            Schema::table('studies', function (Blueprint $table) {
                $table->string('study_photo_path', 2048)->nullable()->change();
            });
        }
    }
};
