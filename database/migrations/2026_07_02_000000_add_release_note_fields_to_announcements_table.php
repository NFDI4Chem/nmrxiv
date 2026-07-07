<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('type')->default('announcement')->after('message');
            $table->string('release_version')->nullable()->after('type');
            $table->text('release_notes')->nullable()->after('release_version');
        });

        DB::table('announcements')
            ->whereRaw('LOWER(title) IN (?, ?)', ['whats new', "what's new"])
            ->update([
                'type' => 'whats_new',
                'status' => 'inactive',
            ]);
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['type', 'release_version', 'release_notes']);
        });
    }
};
