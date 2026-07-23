<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Speed up public compound browse/search:
     * - molecules.created_at for ORDER BY recent
     * - molecule_sample.molecule_id for the public-spectra EXISTS lookup
     */
    public function up(): void
    {
        Schema::table('molecules', function (Blueprint $table) {
            $table->index('created_at', 'molecules_created_at_index');
        });

        Schema::table('molecule_sample', function (Blueprint $table) {
            $table->index('molecule_id', 'molecule_sample_molecule_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('molecules', function (Blueprint $table) {
            $table->dropIndex('molecules_created_at_index');
        });

        Schema::table('molecule_sample', function (Blueprint $table) {
            $table->dropIndex('molecule_sample_molecule_id_index');
        });
    }
};
