<?php

namespace Tests\Unit\Support;

use App\Models\Dataset;
use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use App\Support\Search\PublicDatasetScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicDatasetScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_includes_public_dataset_without_project_when_study_is_public(): void
    {
        $context = $this->createContext();

        $dataset = Dataset::factory()->create([
            'owner_id' => $context['user']->id,
            'team_id' => $context['team']->id,
            'license_id' => $context['license']->id,
            'validation_id' => $context['validation']->id,
            'project_id' => null,
            'study_id' => $context['study']->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
            'spectra_info_extracted_at' => now(),
        ]);

        $ids = Dataset::query()
            ->tap(fn ($query) => PublicDatasetScope::apply($query))
            ->pluck('id')
            ->all();

        $this->assertContains($dataset->id, $ids);
    }

    public function test_excludes_dataset_when_project_exists_but_is_not_public(): void
    {
        $context = $this->createContext();

        $privateProject = Project::factory()->create([
            'owner_id' => $context['user']->id,
            'team_id' => $context['team']->id,
            'license_id' => $context['license']->id,
            'validation_id' => $context['validation']->id,
            'is_public' => false,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $dataset = Dataset::factory()->create([
            'owner_id' => $context['user']->id,
            'team_id' => $context['team']->id,
            'license_id' => $context['license']->id,
            'validation_id' => $context['validation']->id,
            'project_id' => $privateProject->id,
            'study_id' => $context['study']->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
            'spectra_info_extracted_at' => now(),
        ]);

        $ids = Dataset::query()
            ->tap(fn ($query) => PublicDatasetScope::apply($query))
            ->pluck('id')
            ->all();

        $this->assertNotContains($dataset->id, $ids);
    }

    /**
     * @return array{user: User, team: Team, license: License, validation: Validation, study: Study}
     */
    private function createContext(): array
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);
        $license = License::factory()->create();
        $validation = Validation::factory()->create();

        $study = Study::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
            'project_id' => null,
            'is_public' => true,
            'is_archived' => false,
        ]);

        return compact('user', 'team', 'license', 'validation', 'study');
    }
}
