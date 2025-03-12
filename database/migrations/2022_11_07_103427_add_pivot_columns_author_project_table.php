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
        Schema::table('author_project', function (Blueprint $table) {
            $table->enum('contributor_type', ['ContactPerson', 'DataCollector', 'DataCurator', 'DataManager', 'Distributor', 'Editor', 'HostingInstitution', 'Producer', 'ProjectLeader', 'ProjectManager', 'ProjectMember', 'RegistrationAgency', 'RegistrationAuthority', 'RelatedPerson', 'Researcher', 'ResearchGroup', 'RightsHolder', 'Sponsor', 'Supervisor', 'WorkPackageLeader', 'Other'])->default('Researcher');
            $table->tinyInteger('sort_order')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('author_project', function (Blueprint $table) {
            $table->dropColumn('contributor_type');
            $table->dropColumn('sort_order');
        });
    }
};
