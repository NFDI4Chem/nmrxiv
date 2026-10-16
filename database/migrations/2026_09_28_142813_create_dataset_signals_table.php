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
        Schema::create('dataset_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('spectrum_index')->default(0);
            $table->string('nucleus', 16);
            $table->decimal('shift', 9, 4);
            $table->string('multiplicity', 16)->nullable();
            $table->decimal('intensity', 8, 5)->nullable();
            $table->boolean('is_solvent')->default(false);
            $table->string('source', 32)->default('auto_detected');

            $table->index(['nucleus', 'shift']);
            $table->index(['dataset_id', 'nucleus']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dataset_signals');
    }
};
