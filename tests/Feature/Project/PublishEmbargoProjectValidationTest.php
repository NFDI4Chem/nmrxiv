<?php

namespace Tests\Feature\Project;

use App\Models\Citation;
use App\Models\License;
use App\Models\Project;
use App\Models\User;
use App\Models\Validation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublishEmbargoProjectValidationTest extends ProjectFeatureTestCase
{
    use RefreshDatabase;

    public function test_failed_publish_now_restores_validation_report_for_original_release_date(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $team = $owner->currentTeam;
        $license = License::factory()->create();
        $validation = Validation::factory()->create();

        $futureReleaseDate = now()->addDays(2)->startOfDay()->toDateString();

        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'draft_id' => null,
            'is_public' => false,
            'is_archived' => false,
            'is_deleted' => false,
            'status' => 'embargo',
            'doi' => '10.1234/embargo',
            'release_date' => $futureReleaseDate,
            'validation_id' => $validation->id,
        ]);

        $project->users()->attach($owner, ['role' => 'creator']);

        $citation = Citation::factory()->create(['doi' => null]);
        $project->citations()->attach($citation);

        $project->validation->process();
        $baselineReport = $project->validation->fresh()->report;

        $this->assertSame(
            'true|skipped-future-release',
            $baselineReport['project']['citations_detail'][0]['doi'] ?? null
        );

        $response = $this->actingAs($owner)
            ->putJson("/dashboard/projects/{$project->id}/releaseNow");

        $response->assertStatus(422);
        $response->assertJsonPath(
            'validation.report.project.citations_detail.0.doi',
            'false|required'
        );

        $project->refresh();
        $this->assertSame($futureReleaseDate, Carbon::parse($project->release_date)->startOfDay()->toDateString());

        $restoredReport = $project->validation->fresh()->report;
        $this->assertSame(
            'true|skipped-future-release',
            $restoredReport['project']['citations_detail'][0]['doi'] ?? null
        );
    }
}
