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
        Schema::create('assays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('slug');
            $table->string('assay_type');
            $table->json('info')->nullable()->default('{}');
            $table->json('meta_info')->nullable()->default('{}');
            $table->json('isa')->nullable()->default('{}');
            $table->foreignId('study_id');
            $table->foreignId('project_id');
            $table->foreignId('dataset_id');
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
        Schema::dropIfExists('assays');
    }
};
