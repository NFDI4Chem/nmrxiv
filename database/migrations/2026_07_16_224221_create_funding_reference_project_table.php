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
        Schema::create('funding_reference_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->foreignId('funding_reference_id');
            $table->string('user')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'funding_reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funding_reference_project');
    }
};
