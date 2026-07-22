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
        Schema::create('funding_references', function (Blueprint $table) {
            $table->id();
            $table->string('funder_name');
            $table->string('funder_identifier')->nullable();
            $table->string('funder_identifier_type')->nullable();
            $table->string('award_number', 100)->nullable();
            $table->string('award_title', 500)->nullable();
            $table->string('award_uri', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funding_references');
    }
};
