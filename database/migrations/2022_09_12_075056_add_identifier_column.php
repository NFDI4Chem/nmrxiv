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
            $table->string('doi', 2048)->nullable();
            $table->json('datacite_schema')->nullable();
            $table->bigInteger('identifier')->nullable()->unique()->default(null);
        });

        Schema::table('studies', function (Blueprint $table) {
            $table->string('doi', 2048)->nullable();
            $table->json('datacite_schema')->nullable();
            $table->bigInteger('identifier')->nullable()->unique()->default(null);
        });

        Schema::table('datasets', function (Blueprint $table) {
            $table->string('doi', 2048)->nullable();
            $table->json('datacite_schema')->nullable();
            $table->bigInteger('identifier')->nullable()->unique()->default(null);
        });

        Schema::table('samples', function (Blueprint $table) {
            $table->string('doi', 2048)->nullable();
            $table->json('datacite_schema')->nullable();
            $table->bigInteger('identifier')->nullable()->unique()->default(null);
        });

        Schema::table('molecules', function (Blueprint $table) {
            $table->string('doi', 2048)->nullable();
            $table->json('datacite_schema')->nullable();
            $table->bigInteger('identifier')->nullable()->unique()->default(null);
        });

        Schema::table('assays', function (Blueprint $table) {
            $table->string('doi', 2048)->nullable();
            $table->json('datacite_schema')->nullable();
            $table->bigInteger('identifier')->nullable()->unique()->default(null);
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
            $table->dropColumn('doi');
            $table->dropColumn('datacite_schema');
            $table->dropColumn('identifier');
        });

        Schema::table('studies', function (Blueprint $table) {
            $table->dropColumn('doi');
            $table->dropColumn('datacite_schema');
            $table->dropColumn('identifier');
        });

        Schema::table('datasets', function (Blueprint $table) {
            $table->dropColumn('doi');
            $table->dropColumn('datacite_schema');
            $table->dropColumn('identifier');
        });

        Schema::table('samples', function (Blueprint $table) {
            $table->dropColumn('doi');
            $table->dropColumn('datacite_schema');
            $table->dropColumn('identifier');
        });

        Schema::table('molecules', function (Blueprint $table) {
            $table->dropColumn('doi');
            $table->dropColumn('datacite_schema');
            $table->dropColumn('identifier');
        });

        Schema::table('assays', function (Blueprint $table) {
            $table->dropColumn('doi');
            $table->dropColumn('datacite_schema');
            $table->dropColumn('identifier');
        });
    }
};
