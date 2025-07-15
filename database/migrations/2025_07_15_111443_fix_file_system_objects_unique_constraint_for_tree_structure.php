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
        Schema::table('file_system_objects', function (Blueprint $table) {
            // Drop the old restrictive unique constraint
            $table->dropUnique(['name', 'slug', 'description', 'relative_url', 'type', 'key', 'is_root', 'project_id', 'study_id', 'draft_id', 'level']);

            // Add a proper tree-structure-friendly unique constraint
            // This allows: same name in different directories, proper hierarchies, context separation
            $table->unique(['name', 'parent_id', 'type', 'project_id', 'study_id', 'draft_id'], 'fso_tree_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_system_objects', function (Blueprint $table) {
            // Drop the new constraint
            $table->dropUnique('fso_tree_unique');

            // Restore the old constraint (though it was problematic)
            $table->unique(['name', 'slug', 'description', 'relative_url', 'type', 'key', 'is_root', 'project_id', 'study_id', 'draft_id', 'level']);
        });
    }
};
