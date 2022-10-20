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
        Schema::create('nmrium', function (Blueprint $table) {
            $table->id();
            $table->jsonb('nmrium_info')->nullable();
            $table->foreignId('dataset_id')->nullable();
            $table->timestamps();
        });

        Schema::table('datasets', function (Blueprint $table) {
            $table->dropColumn('nmrium_info');
            $table->boolean('has_nmrium')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nmrium');
    }
};
