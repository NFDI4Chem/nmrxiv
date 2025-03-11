<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('citation_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->foreignId('citation_id');
            $table->string('user')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'citation_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('citation_project');
    }
};
