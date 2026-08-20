<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Persist public-catalog membership and card badges on molecules so browse/search
     * does not re-run the nested studies/datasets/nmrium EXISTS on every request.
     */
    public function up(): void
    {
        Schema::table('molecules', function (Blueprint $table) {
            $table->boolean('has_public_spectra')->default(false);
            $table->unsignedInteger('public_samples_count')->default(0);
            $table->json('public_experiment_type_counts')->nullable();
            $table->timestamp('public_catalog_indexed_at')->nullable();
            $table->index(['has_public_spectra', 'created_at'], 'molecules_public_catalog_recent_index');
        });
    }

    public function down(): void
    {
        Schema::table('molecules', function (Blueprint $table) {
            $table->dropIndex('molecules_public_catalog_recent_index');
            $table->dropColumn([
                'has_public_spectra',
                'public_samples_count',
                'public_experiment_type_counts',
                'public_catalog_indexed_at',
            ]);
        });
    }
};
