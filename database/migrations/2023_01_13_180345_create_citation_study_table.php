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
    public function up()
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('citation_study');
    }
};
