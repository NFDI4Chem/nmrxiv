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
        Schema::table('nmrium', function (Blueprint $table) {
            $table->renameColumn('dataset_id', 'nmriumable_id');
            $table->string('nmriumable_type')->default(\App\Models\Dataset::class);
        });

        Schema::table('studies', function (Blueprint $table) {
            $table->boolean('has_nmrium')->nullable()->default(0);
            $table->boolean('has_nmredata')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nmrium', function (Blueprint $table) {
            $table->renameColumn('nmriumable_id', 'dataset_id');
            $table->dropColumn('nmriumable_type');
        });

        Schema::table('studies', function (Blueprint $table) {
            $table->dropColumn('has_nmrium');
            $table->dropColumn('has_nmredata');
        });
    }
};
