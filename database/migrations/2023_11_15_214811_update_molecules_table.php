<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('molecules', function (Blueprint $table) {
            // $table->renameColumn('INCHI', 'inchi');
            // $table->renameColumn('STANDARD_INCHI', 'standard_inchi');
            // $table->renameColumn('INCHI_KEY', 'inchi_key');
            // $table->renameColumn('STANDARD_INCHI_KEY', 'standard_inchi_key');

            // $table->renameColumn('canonical_smiles', 'canonical_smiles');
            // $table->renameColumn('smiles', 'smiles');
            // $table->renameColumn('SMILES_CHIRAL', 'absolute_smiles');

            // $table->renameColumn('CAS_NUMBER', 'cas');
            // $table->renameColumn('MOLECULAR_WEIGHT', 'molecular_weight');
            // $table->renameColumn('FORMULA', 'molecular_formula');
            // $table->renameColumn('MOL', 'sdf');

            $table->longText('name')->nullable();
            $table->integer('name_trust_level')->default(0)->nullable();
            $table->integer('annotation_level')->default(0)->nullable();

            $table->longText('synonyms')->nullable();
            $table->longText('iupac_name')->nullable();

            $table->json('2d')->nullable();
            $table->json('3d')->nullable();

            $table->longText('structural_comments')->nullable();

            $table->enum('status', ['APPROVED', 'REVOKED'])->default('APPROVED');

            $table->boolean('active')->default(1);
            $table->boolean('has_stereo')->default(0);

            $table->boolean('has_variants')->default(0);
            $table->integer('variants_count')->default(0);

            $table->dropColumn('VIEWS');
            $table->dropColumn('COMMENT');
            $table->dropColumn('DBE');
            $table->dropColumn('SSSR');
            $table->dropColumn('SAR');

            $table->dropColumn('MULTIPLICITY_0');
            $table->dropColumn('MULTIPLICITY_1');
            $table->dropColumn('MULTIPLICITY_2');
            $table->dropColumn('MULTIPLICITY_3');

            $table->dropColumn('fp0');
            $table->dropColumn('fp1');
            $table->dropColumn('fp2');
            $table->dropColumn('fp3');
            $table->dropColumn('fp4');
            $table->dropColumn('fp5');
            $table->dropColumn('fp6');
            $table->dropColumn('fp7');
            $table->dropColumn('fp8');
            $table->dropColumn('fp9');
            $table->dropColumn('fp10');
            $table->dropColumn('fp11');
            $table->dropColumn('fp12');
            $table->dropColumn('fp13');
            $table->dropColumn('fp14');
            $table->dropColumn('fp15');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('molecules', function (Blueprint $table) {
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

            // $table->renameColumn('inchi', 'INCHI');
            // $table->renameColumn('standard_inchi','STANDARD_INCHI');
            // $table->renameColumn('inchi_key', 'INCHI_KEY');
            // $table->renameColumn('standard_inchi_key', 'STANDARD_INCHI_KEY');

            // $table->renameColumn('canonical_smiles','canonical_smiles');
            // $table->renameColumn('smiles', 'SMILES');
            // $table->renameColumn('absolute_smiles', 'SMILES_CHIRAL');

            // $table->renameColumn('cas', 'CAS_NUMBER');
            // $table->renameColumn('molecular_weight', 'MOLECULAR_WEIGHT');
            // $table->renameColumn('molecular_formula', 'FORMULA');

            // $table->renameColumn('comment', 'COMMENT');
            // $table->renameColumn('sdf', 'MOL');

            $table->dropColumn('name');
            $table->dropColumn('name_trust_level');
            $table->dropColumn('annotation_level');

            $table->dropColumn('synonyms');
            $table->dropColumn('iupac_name');

            $table->dropColumn('2d');
            $table->dropColumn('3d');

            $table->dropColumn('structural_comments');

            $table->dropColumn('status');

            $table->dropColumn('active');
            $table->dropColumn('has_stereo');

            $table->dropColumn('has_variants');
            $table->dropColumn('variants_count');
        });
    }
};
