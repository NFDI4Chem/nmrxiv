<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spectra_metadata_stats_index', function (Blueprint $table) {
            $table->id();
            $table->string('scope')->unique();
            $table->json('totals');
            $table->json('distributions');
            $table->json('missing');
            $table->timestamp('computed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spectra_metadata_stats_index');
    }
};
