<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citation_study', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_id');
            $table->foreignId('citation_id');
            $table->string('user')->nullable();
            $table->timestamps();
            $table->unique(['study_id', 'citation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citation_study');
    }
};
