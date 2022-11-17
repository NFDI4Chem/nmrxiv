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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('schema_version', 2048)->nullable();
            $table->string('internal_status', 2048)->nullable();
        });

        Schema::table('studies', function (Blueprint $table) {
            $table->string('internal_status', 2048)->nullable();
        });

        Schema::table('datasets', function (Blueprint $table) {
            $table->string('internal_status', 2048)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('schema_version');
            $table->dropColumn('internal_status');
        });

        Schema::table('studies', function (Blueprint $table) {
            $table->dropColumn('internal_status');
        });

        Schema::table('datasets', function (Blueprint $table) {
            $table->dropColumn('internal_status');
        });
    }
};
