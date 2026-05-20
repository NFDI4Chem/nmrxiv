<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stores the user-provided spectrum assignment data per dataset.
     *
     * Shape (defensive on read; only the keys actually used are populated):
     *   {
     *     "acs":         "<free-form ACS string pasted by the user>",
     *     "atom_peaks":  [ { "atom": "C1", "peak": 7.42, "label": "..." }, ... ],
     *     "source":      "manual" | "nmrium",
     *     "updated_at":  "ISO-8601 timestamp"
     *   }
     *
     * Stored on the dataset (not in a separate table) because each spectrum
     * has at most one assignment block and the data is always read together
     * with the dataset. Nullable so existing rows stay untouched.
     */
    public function up(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->json('assignments')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->dropColumn('assignments');
        });
    }
};
