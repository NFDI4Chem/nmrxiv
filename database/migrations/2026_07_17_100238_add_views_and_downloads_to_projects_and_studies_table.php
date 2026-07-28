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
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('views')->default(0)->after('download_url');
            $table->unsignedBigInteger('downloads')->default(0)->after('views');
        });

        Schema::table('studies', function (Blueprint $table) {
            $table->unsignedBigInteger('views')->default(0)->after('download_url');
            $table->unsignedBigInteger('downloads')->default(0)->after('views');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['views', 'downloads']);
        });

        Schema::table('studies', function (Blueprint $table) {
            $table->dropColumn(['views', 'downloads']);
        });
    }
};
