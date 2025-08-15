<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the old restrictive unique constraint. On PostgreSQL the default
        // constraint name can be truncated, so we detect it dynamically.
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::unprepared(<<<'SQL'
DO $$
DECLARE r record;
BEGIN
    FOR r IN (
        SELECT c.conname
        FROM pg_constraint c
        JOIN pg_class t ON t.oid = c.conrelid
        JOIN pg_namespace n ON n.oid = t.relnamespace
        WHERE n.nspname = current_schema()
          AND t.relname = 'file_system_objects'
          AND c.contype = 'u'
          AND pg_get_constraintdef(c.oid) ILIKE '%(name, slug, description, relative_url, type, key, is_root, project_id, study_id, draft_id, level)%'
    ) LOOP
        EXECUTE format('ALTER TABLE %I DROP CONSTRAINT %I', 'file_system_objects', r.conname);
    END LOOP;
END $$;
SQL);
        } else {
            Schema::table('file_system_objects', function (Blueprint $table) {
                $table->dropUnique(['name', 'slug', 'description', 'relative_url', 'type', 'key', 'is_root', 'project_id', 'study_id', 'draft_id', 'level']);
            });
        }

        Schema::table('file_system_objects', function (Blueprint $table) {
            // Add a proper tree-structure-friendly unique constraint
            $table->unique(['name', 'parent_id', 'type', 'project_id', 'study_id', 'draft_id'], 'fso_tree_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new constraint by name if it exists
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE file_system_objects DROP CONSTRAINT IF EXISTS fso_tree_unique');
        } else {
            Schema::table('file_system_objects', function (Blueprint $table) {
                $table->dropUnique('fso_tree_unique');
            });
        }

        // Restore the old constraint under an explicit, short name for portability
        Schema::table('file_system_objects', function (Blueprint $table) {
            $table->unique([
                'name', 'slug', 'description', 'relative_url', 'type', 'key', 'is_root', 'project_id', 'study_id', 'draft_id', 'level',
            ], 'fso_legacy_unique');
        });
    }
};
