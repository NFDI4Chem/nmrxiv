<?php

use App\Enums\MixtureCompositionBasis;
use App\Enums\MixtureDeterminationMethod;
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
        Schema::create('mixture_compositions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->enum('basis', MixtureCompositionBasis::values());
            $table->enum('determination_method', MixtureDeterminationMethod::values())
                ->default(MixtureDeterminationMethod::Qnmr->value);
            $table->string('nucleus', 32)->nullable();
            $table->decimal('relaxation_delay_s', 10, 3)->nullable();
            $table->boolean('has_residual')->default(false);
            $table->timestamps();

            $table->unique('sample_id');
            $table->foreign('sample_id')->references('id')->on('samples')->onDelete('cascade');
        });

        Schema::create('mixture_components', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->unsignedBigInteger('molecule_id')->nullable();
            $table->decimal('value', 12, 4);
            $table->string('integrated_signal')->nullable();
            $table->unsignedSmallInteger('n_nuclei')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('samples')->onDelete('cascade');
            $table->foreign('molecule_id')->references('id')->on('molecules')->nullOnDelete();
            $table->index(['sample_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mixture_components');
        Schema::dropIfExists('mixture_compositions');
    }
};
