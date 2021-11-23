<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('studies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('color')->nullable();
            $table->boolean('starred')->nullable()->default(0);
            $table->boolean('is_public')->nullable()->default(0);
            $table->boolean('is_deleted')->nullable()->default(0);
            $table->boolean('is_archived')->nullable()->default(0);
            $table->string('location')->nullable();
            $table->string('url', 2048)->nullable();
            $table->longText('description')->nullable();
            $table->integer('sort_order')->default('1');
            $table->string('type')->nullable();
            $table->uuid('uuid')->unique();
            $table->enum('access', ['restricted', 'link'])->default('restricted');
            $table->enum('access_type', ['viewer', 'commentor', 'editor'])->default('viewer');
            $table->foreignId('team_id')->nullable();
            $table->foreignId('owner_id')->nullable();
            $table->foreignId('project_id')->nullable();
            $table->string('study_photo_path', 2048)->nullable();
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
        Schema::dropIfExists('studies');
    }
}
