<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('citations', function (Blueprint $table) {
            $table->string('title_slug')->nullable()->after('title')->index();
        });

        DB::table('citations')
            ->select('id', 'title')
            ->orderBy('id')
            ->chunkById(500, function ($citations): void {
                foreach ($citations as $citation) {
                    DB::table('citations')
                        ->where('id', $citation->id)
                        ->update([
                            'title_slug' => Str::slug((string) $citation->title),
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citations', function (Blueprint $table) {
            $table->dropIndex(['title_slug']);
            $table->dropColumn('title_slug');
        });
    }
};
