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
        Schema::create('embargo_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->integer('days_before_release'); // 7, 3, or 1
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(['project_id', 'days_before_release']);
            $table->index(['project_id', 'days_before_release']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('embargo_reminders');
    }
};
