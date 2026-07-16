<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->string('spectra_solvent')->nullable();
            $table->decimal('spectra_temperature', 10, 4)->nullable();
            $table->string('spectra_nucleus')->nullable();
            $table->string('spectra_experiment')->nullable();
            $table->string('spectra_pulse_sequence')->nullable();
            $table->decimal('spectra_base_frequency', 10, 4)->nullable();
            $table->unsignedInteger('spectra_number_of_scans')->nullable();
            $table->string('spectra_probe_name')->nullable();
            $table->decimal('spectra_field_strength', 10, 4)->nullable();
            $table->decimal('spectra_spectral_width', 12, 4)->nullable();
            $table->unsignedInteger('spectra_number_of_points')->nullable();
            $table->decimal('spectra_relaxation_time', 12, 6)->nullable();
            $table->unsignedSmallInteger('spectra_dimension')->nullable();
            $table->decimal('spectra_origin_frequency', 10, 4)->nullable();
            $table->string('spectra_type')->nullable();
            $table->string('spectra_name')->nullable();
            $table->string('spectra_title')->nullable();
            $table->string('spectra_creator')->nullable();
            $table->string('spectra_owner')->nullable();
            $table->string('spectra_data_class')->nullable();
            $table->string('spectra_acquisition_mode')->nullable();
            $table->decimal('spectra_frequency_offset', 12, 6)->nullable();
            $table->boolean('spectra_is_ft')->nullable();
            $table->boolean('spectra_is_fid')->nullable();
            $table->text('spectra_search_text')->nullable();
            $table->timestamp('spectra_info_extracted_at')->nullable();

            $table->index('spectra_solvent');
            $table->index('spectra_nucleus');
            $table->index('spectra_experiment');
            $table->index('spectra_base_frequency');
            $table->index('spectra_temperature');
            $table->index('spectra_number_of_scans');
            $table->index(['is_public', 'spectra_info_extracted_at'], 'datasets_public_spectra_extracted_idx');
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
            DB::statement(
                'CREATE INDEX datasets_spectra_search_text_gin_idx ON datasets USING gin (spectra_search_text gin_trgm_ops)'
            );
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS datasets_spectra_search_text_gin_idx');
        }

        Schema::table('datasets', function (Blueprint $table) {
            $table->dropIndex('datasets_public_spectra_extracted_idx');
            $table->dropIndex(['spectra_solvent']);
            $table->dropIndex(['spectra_nucleus']);
            $table->dropIndex(['spectra_experiment']);
            $table->dropIndex(['spectra_base_frequency']);
            $table->dropIndex(['spectra_temperature']);
            $table->dropIndex(['spectra_number_of_scans']);

            $table->dropColumn([
                'spectra_solvent',
                'spectra_temperature',
                'spectra_nucleus',
                'spectra_experiment',
                'spectra_pulse_sequence',
                'spectra_base_frequency',
                'spectra_number_of_scans',
                'spectra_probe_name',
                'spectra_field_strength',
                'spectra_spectral_width',
                'spectra_number_of_points',
                'spectra_relaxation_time',
                'spectra_dimension',
                'spectra_origin_frequency',
                'spectra_type',
                'spectra_name',
                'spectra_title',
                'spectra_creator',
                'spectra_owner',
                'spectra_data_class',
                'spectra_acquisition_mode',
                'spectra_frequency_offset',
                'spectra_is_ft',
                'spectra_is_fid',
                'spectra_search_text',
                'spectra_info_extracted_at',
            ]);
        });
    }
};
