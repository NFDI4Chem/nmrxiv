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
    public function up(): void
    {
        Schema::table('citations', function (Blueprint $table) {
            $table->longText('abstract')->nullable()->change();
            $table->longText('citation_text')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('citations', function (Blueprint $table) {
            $table->longText('abstract')->nullable(false)->change();
            $table->longText('citation_text')->nullable(false)->change();
        });
    }
};
