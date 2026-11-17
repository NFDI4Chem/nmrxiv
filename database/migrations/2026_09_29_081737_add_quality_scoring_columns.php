<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Persist data-completeness scores (annotation_level) with a self-describing
     * breakdown and the rubric version that produced them.
     */
    public function up(): void
    {
        Schema::table('molecules', function (Blueprint $table) {
            $table->json('quality_breakdown')->nullable()->after('annotation_level');
            $table->unsignedSmallInteger('quality_rubric_version')->nullable()->after('quality_breakdown');
            $table->timestamp('quality_scored_at')->nullable()->after('quality_rubric_version');
            $table->index('annotation_level', 'molecules_annotation_level_index');
            $table->index('quality_rubric_version', 'molecules_quality_rubric_version_index');
        });
    }

    public function down(): void
    {
        Schema::table('molecules', function (Blueprint $table) {
            $table->dropIndex('molecules_annotation_level_index');
            $table->dropIndex('molecules_quality_rubric_version_index');
            $table->dropColumn([
                'quality_breakdown',
                'quality_rubric_version',
                'quality_scored_at',
            ]);
        });
    }
};
