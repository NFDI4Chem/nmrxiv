<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Quickcheck runs of a sample's 1H/13C assignments, with the
     * author's confirmation. `input` is the assignment set sent to NMRKit and
     * `report` its response, both stored as-is.
     */
    public function up(): void
    {
        Schema::create('assignment_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source', 32);
            $table->jsonb('input');
            $table->string('input_hash', 64);
            $table->string('status', 16)->default('queued');
            $table->jsonb('report')->nullable();
            $table->string('verdict', 16)->nullable();
            $table->string('assignment_result', 16)->nullable();
            $table->unsignedTinyInteger('mark_13c')->nullable();
            $table->unsignedTinyInteger('mark_1h')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('confirmation_note')->nullable();
            $table->timestamps();

            $table->index(['study_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_validations');
    }
};
