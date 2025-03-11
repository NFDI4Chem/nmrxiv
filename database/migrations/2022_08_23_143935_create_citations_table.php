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
        Schema::create('citations', function (Blueprint $table) {
            $table->id();
            $table->string('doi')->nullable();
            $table->string('title');
            $table->longText('authors');
            $table->longText('abstract');
            $table->longText('citation_text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citations');
    }
};
