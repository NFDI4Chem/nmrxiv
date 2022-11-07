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
        Schema::table('author_project', function (Blueprint $table){
            $table->unique(['author_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('author_project', function (Blueprint $table){
            $table->dropUnique('author_project_author_id_project_id_unique');
        });
    }
};
