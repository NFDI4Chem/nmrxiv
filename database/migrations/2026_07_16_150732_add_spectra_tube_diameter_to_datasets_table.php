<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->string('spectra_tube_diameter', 10)->nullable()->after('spectra_temperature');
            $table->index('spectra_tube_diameter');
        });
    }

    public function down(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->dropIndex(['spectra_tube_diameter']);
            $table->dropColumn('spectra_tube_diameter');
        });
    }
};
