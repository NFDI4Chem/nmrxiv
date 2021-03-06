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
        Schema::create('molecule_sample', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_id');
            $table->foreignId('molecule_id');
            $table->string('percentage_composition')->nullable();
            $table->unique(['sample_id', 'molecule_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('molecule_sample');
    }
};
