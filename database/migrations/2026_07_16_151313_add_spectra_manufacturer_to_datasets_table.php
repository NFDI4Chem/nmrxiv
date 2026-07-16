<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->string('spectra_manufacturer')->nullable()->after('spectra_probe_name');
            $table->index('spectra_manufacturer');
        });
    }

    public function down(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->dropIndex(['spectra_manufacturer']);
            $table->dropColumn('spectra_manufacturer');
        });
    }
};
