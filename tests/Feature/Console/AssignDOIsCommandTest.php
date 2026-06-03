<?php

namespace Tests\Feature\Console;

use App\Models\License;
use App\Models\Project;
use App\Models\Ticker;
use App\Models\User;
use App\Services\DOI\DOIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class AssignDOIsCommandTest extends TestCase
{
    use RefreshDatabase;

    private function seedTickers(): void
    {
        foreach (['project', 'study', 'dataset', 'sample', 'assay', 'molecule'] as $type) {
            Ticker::query()->create([
                'type' => $type,
                'index' => 0,
            ]);
        }
    }

    public function test_assign_dois_fails_when_project_not_found(): void
    {
        $this->seedTickers();

        $this->artisan('nmrxiv:assign-dois', ['--project' => '99999'])
            ->expectsOutputToContain('No project found with id')
            ->assertFailed();
    }

    public function test_assign_dois_fails_when_study_not_found(): void
    {
        $this->seedTickers();

        $this->artisan('nmrxiv:assign-dois', ['--study' => '88888'])
            ->expectsOutputToContain('No study found with id')
            ->assertFailed();
    }

    public function test_assign_dois_skips_non_public_project(): void
    {
        $this->seedTickers();

        $user = User::factory()->withPersonalTeam()->create();
        $license = License::factory()->create();

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'license_id' => $license->id,
            'is_public' => false,
            'doi' => null,
            'identifier' => null,
            'validation_id' => null,
            'draft_id' => null,
        ]);

        $this->artisan('nmrxiv:assign-dois', ['--project' => (string) $project->id])
            ->expectsOutputToContain('is not public')
            ->assertSuccessful();
    }

    public function test_assign_dois_targets_public_project_and_sets_doi(): void
    {
        $this->seedTickers();

        $this->mock(DOIService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createDOI')
                ->atLeast()->once()
                ->andReturn(['data' => ['id' => '10.5281/nmrxiv.test-project']]);
            $mock->shouldReceive('updateDOI')
                ->atLeast()->once()
                ->andReturn(['data' => ['attributes' => []]]);
        });

        $user = User::factory()->withPersonalTeam()->create();
        $license = License::factory()->create();

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'license_id' => $license->id,
            'is_public' => true,
            'doi' => null,
            'identifier' => null,
            'validation_id' => null,
            'draft_id' => null,
        ]);

        $this->artisan('nmrxiv:assign-dois', ['--project' => (string) $project->id])
            ->assertSuccessful();

        $project->refresh();
        $this->assertSame('10.5281/nmrxiv.test-project', $project->doi);
    }

    public function test_assign_dois_batch_reports_when_empty(): void
    {
        $this->seedTickers();

        $this->artisan('nmrxiv:assign-dois')
            ->expectsOutputToContain('No public projects or studies without a DOI')
            ->assertSuccessful();
    }
}
