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
        Schema::create('licenses', function (Blueprint $table) {
            $table->string('id');
            $table->string('name');
            $table->string('spdx_id')->nullable();
            $table->string('url')->nullable();
            $table->string('node_id')->nullable();
            $table->string('html_url')->nullable();
            $table->longText('description')->nullable();
            $table->string('implementation')->nullable();
            $table->char('permissions')->nullable();
            $table->longText('body')->nullable();
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
        Schema::dropIfExists('licenses');
    }
};
