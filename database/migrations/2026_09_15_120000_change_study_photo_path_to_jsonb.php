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

            return;
        }

        // MySQL/sqlite: the column is still a plain varchar here, so it
        // accepts any text. Wrap existing single-path strings into a
        // one-element JSON array *before* narrowing the column to `json` —
        // MySQL validates the existing values as JSON as part of that
        // ALTER, and a bare path string (e.g. "/projects/x/y.svg") is not
        // valid JSON, which would make the type change fail outright.
        DB::table('studies')
            ->whereNotNull('study_photo_path')
            ->where('study_photo_path', '!=', '')
            ->orderBy('id')
            ->chunkById(200, function ($studies) {
                foreach ($studies as $study) {
                    DB::table('studies')
                        ->where('id', $study->id)
                        ->update(['study_photo_path' => json_encode([$study->study_photo_path])]);
                }
            });

        Schema::table('studies', function (Blueprint $table) {
            $table->json('study_photo_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(<<<'SQL'
                ALTER TABLE studies
                ALTER COLUMN study_photo_path TYPE varchar(2048)
                USING (study_photo_path ->> 0)
            SQL);

            return;
        }

        // Widen to `text` first (a safe, lossless reinterpretation of the
        // existing JSON as plain text) so we can freely write an unwrapped,
        // non-JSON string back — MySQL enforces JSON validity on writes to
        // a `json` column, which a bare path string would fail.
        Schema::table('studies', function (Blueprint $table) {
            $table->text('study_photo_path')->nullable()->change();
        });

        DB::table('studies')
            ->whereNotNull('study_photo_path')
            ->where('study_photo_path', '!=', '')
            ->orderBy('id')
            ->chunkById(200, function ($studies) {
                foreach ($studies as $study) {
                    $decoded = json_decode($study->study_photo_path, true);
                    $value = is_array($decoded) ? ($decoded[0] ?? null) : $study->study_photo_path;

                    DB::table('studies')
                        ->where('id', $study->id)
                        ->update(['study_photo_path' => $value]);
                }
            });

        Schema::table('studies', function (Blueprint $table) {
            $table->string('study_photo_path', 2048)->nullable()->change();
        });
    }
};
