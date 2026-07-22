<?php

namespace Tests\Feature\Commands;

use App\Models\Dataset;
use App\Models\License;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use App\Support\Nmr\DatasetSpectraInfoExtractor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpectraMetadataStatsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_outputs_distribution_tables(): void
    {
        $this->createIndexedPublicDataset([
            'solvent' => 'CDCl3',
            'nucleus' => ['1H'],
            'experiment' => 'proton',
            'dimension' => 1,
        ]);

        $this->artisan('nmrxiv:index-spectra-metadata-stats')->assertSuccessful();

        $this->artisan('nmrxiv:spectra-metadata-stats')
            ->expectsOutputToContain('Public indexed spectra metadata')
            ->expectsOutputToContain('Dimension (1D / 2D)')
            ->expectsOutputToContain('1D')
            ->assertSuccessful();
    }

    /**
     * @param  array<string, mixed>  $info
     */
    private function createIndexedPublicDataset(array $info): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);
        $license = License::factory()->create();
        $validation = Validation::factory()->create();

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $study = Study::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
            'project_id' => $project->id,
            'is_public' => true,
            'is_archived' => false,
        ]);

        $dataset = Dataset::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
            'project_id' => $project->id,
            'study_id' => $study->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        NMRium::factory()->forDataset($dataset)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        ['info' => $info],
                    ],
                ],
            ],
        ]);

        (new DatasetSpectraInfoExtractor)->syncDataset($dataset->refresh());
    }
}
