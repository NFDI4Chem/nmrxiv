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
        Schema::create('molecules', function (Blueprint $table) {
            $table->id();
            $table->longText('CAS_NUMBER')->nullable();
            $table->longText('FORMULA');
            $table->float('MOLECULAR_WEIGHT')->nullable();
            $table->longText('SMILES')->nullable();
            $table->longText('SMILES_CHIRAL')->nullable();
            $table->longText('CANONICAL_SMILES')->nullable();
            $table->longText('INCHI')->nullable();
            $table->longText('STANDARD_INCHI')->unique();
            $table->longText('INCHI_KEY')->nullable();
            $table->longText('STANDARD_INCHI_KEY')->nullable();
            $table->bigInteger('fp0')->nullable();
            $table->bigInteger('fp1')->nullable();
            $table->bigInteger('fp2')->nullable();
            $table->bigInteger('fp3')->nullable();
            $table->bigInteger('fp4')->nullable();
            $table->bigInteger('fp5')->nullable();
            $table->bigInteger('fp6')->nullable();
            $table->bigInteger('fp7')->nullable();
            $table->bigInteger('fp8')->nullable();
            $table->bigInteger('fp9')->nullable();
            $table->bigInteger('fp10')->nullable();
            $table->bigInteger('fp11')->nullable();
            $table->bigInteger('fp12')->nullable();
            $table->bigInteger('fp13')->nullable();
            $table->bigInteger('fp14')->nullable();
            $table->bigInteger('fp15')->nullable();
            $table->float('DBE')->nullable();
            $table->integer('SSSR')->nullable();
            $table->integer('SAR')->nullable();
            $table->json('COMMENT')->nullable();
            $table->longText('MOL')->nullable();
            $table->integer('MULTIPLICITY_0')->nullable();
            $table->integer('MULTIPLICITY_1')->nullable();
            $table->integer('MULTIPLICITY_2')->nullable();
            $table->integer('MULTIPLICITY_3')->nullable();
            $table->integer('VIEWS')->nullable();
            $table->string('DOI')->nullable();
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
        Schema::dropIfExists('molecules');
    }
};
