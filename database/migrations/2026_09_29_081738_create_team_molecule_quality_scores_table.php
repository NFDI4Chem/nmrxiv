<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Team-scoped molecule quality scores so a workspace is only credited for
     * spectra from its own public studies.
     */
    public function up(): void
    {
        Schema::create('team_molecule_quality_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('molecule_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('tier')->default(0);
            $table->json('breakdown')->nullable();
            $table->unsignedSmallInteger('rubric_version')->nullable();
            $table->timestamp('scored_at')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'molecule_id']);
            $table->index(['team_id', 'tier']);
            $table->index('rubric_version');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_molecule_quality_scores');
    }
};
