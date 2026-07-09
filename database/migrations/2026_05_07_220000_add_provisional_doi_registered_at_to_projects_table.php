<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Marks the moment a project's provisional DOI was registered with
     * DataCite as a real findable record (and bidirectionally linked to
     * the canonical DOI via IsIdenticalTo). Used by `linkProvisionalDoi`
     * to short-circuit on reruns and avoid duplicate DataCite traffic.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->timestamp('provisional_doi_registered_at')->nullable()->after('provisional_doi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('provisional_doi_registered_at');
        });
    }
};
