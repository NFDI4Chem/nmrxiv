<?php

namespace Tests\Unit\Actions\Citation;

use App\Actions\Citation\SyncCitationPivot;
use App\Models\Citation;
use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncCitationPivotTest extends TestCase
{
    use RefreshDatabase;

    public function test_merge_project_citations_onto_study_preserves_existing_study_citations(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);
        $license = License::factory()->create();
        $validation = Validation::factory()->create();
        $project = Project::factory()->create([
            'team_id' => $team->id,
            'owner_id' => $user->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);
        $study = Study::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);

        $onlyOnStudy = Citation::factory()->create(['doi' => '10.1111/study-only']);
        $onlyOnProject = Citation::factory()->create(['doi' => '10.2222/project-only']);

        $study->linkedCitations()->attach($onlyOnStudy->id, ['user' => (string) $user->id]);
        $project->citations()->attach($onlyOnProject->id, ['user' => (string) $user->id]);

        $project->load('citations');

        app(SyncCitationPivot::class)->mergeProjectCitationsOntoStudy($study, $project->citations);

        $study->refresh();
        $study->load('linkedCitations');

        $this->assertTrue($study->linkedCitations->contains('id', $onlyOnStudy->id));
        $this->assertTrue($study->linkedCitations->contains('id', $onlyOnProject->id));
        $this->assertCount(2, $study->linkedCitations);
    }
}
