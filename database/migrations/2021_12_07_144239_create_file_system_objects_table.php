<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileSystemObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_system_objects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->string('relative_url', 2048)->nullable();
            $table->string('path', 2048)->nullable();
            $table->enum('type', ['directory', 'file'])->default('file');
            $table->string('key');
            $table->string('compressionInfo')->nullable();
            $table->string('kernelSessionInfo')->nullable();
            $table->string('color')->nullable()->default('#fff');
            $table->boolean('starred')->default(0);
            $table->boolean('is_public')->default(0);
            $table->boolean('is_deleted')->default(0);
            $table->boolean('is_archived')->default(0);
            $table->boolean('is_original')->default(0);
            $table->boolean('is_verified')->default(0);
            $table->boolean('is_processed')->default(0);
            $table->boolean('has_children')->default(0);
            $table->boolean('is_root')->default(0);
            $table->integer('sort_order')->default('1');
            $table->integer('level')->default('0');
            $table->foreignId('owner_id')->nullable();
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('project_id')->nullable();
            $table->foreignId('study_id')->nullable();
            $table->foreignId('version_id')->nullable();
            $table->json('info')->nullable()->default('{}');
            $table->json('settings')->nullable()->default('{}');
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
        Schema::dropIfExists('file_system_objects');
    }
}
