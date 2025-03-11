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
        Schema::create('validations', function (Blueprint $table) {
            $table->id();
            $table->jsonb('report')->nullable();
            $table->integer('score')->default(0);
            $table->timestamps();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('validation_id')->nullable();
            $table->boolean('validation_status')->default(0);
        });

        Schema::table('studies', function (Blueprint $table) {
            $table->foreignId('validation_id')->nullable();
            $table->boolean('validation_status')->default(0);
        });

        Schema::table('datasets', function (Blueprint $table) {
            $table->foreignId('validation_id')->nullable();
            $table->boolean('validation_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('validations');
    }
};
