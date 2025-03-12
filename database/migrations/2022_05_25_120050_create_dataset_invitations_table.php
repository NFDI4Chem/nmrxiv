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
        Schema::create('dataset_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('role')->nullable();
            $table->string('message')->nullable();
            $table->timestamps();

            $table->unique(['dataset_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dataset_invitations');
    }
};
