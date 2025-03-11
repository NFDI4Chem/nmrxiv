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
        Schema::create('drafts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->string('relative_url', 2048)->nullable();
            $table->string('path', 2048)->nullable();
            $table->integer('current_step')->default('1');
            $table->string('key');
            $table->boolean('is_deleted')->default(0);
            $table->foreignId('owner_id')->nullable();
            $table->foreignId('team_id')->nullable();
            $table->json('info')->nullable()->default('{}');
            $table->json('settings')->nullable()->default('{}');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drafts');
    }
};
