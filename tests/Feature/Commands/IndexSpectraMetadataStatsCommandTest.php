<?php

namespace Tests\Feature\Commands;

use App\Models\Dataset;
use App\Models\License;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\SpectraMetadataStatsIndex;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use App\Support\Nmr\DatasetSpectraInfoExtractor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schedule;
use Tests\TestCase;

class IndexSpectraMetadataStatsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_persists_statistics_index(): void
    {
        $this->createIndexedPublicDataset([
            'solvent' => 'CDCl3',
            'nucleus' => ['1H'],
            'experiment' => 'proton',
            'dimension' => 1,
        ]);

        $this->artisan('nmrxiv:index-spectra-metadata-stats')
            ->expectsOutputToContain('Indexed metadata statistics for [public_indexed]')
            ->assertSuccessful();

        $index = SpectraMetadataStatsIndex::query()->first();

        $this->assertNotNull($index);
        $this->assertSame('public_indexed', $index->scope);
        $this->assertSame(1, $index->totals['spectra_indexed']);
        $this->assertSame('1D', $index->distributions['dimension'][0]['value']);
    }

    public function test_command_is_scheduled_daily(): void
    {
        $event = collect(Schedule::events())->first(
            fn ($scheduled) => str_contains((string) $scheduled->command, 'nmrxiv:index-spectra-metadata-stats')
        );

        $this->assertNotNull($event);
        $this->assertSame('0 0 * * *', $event->expression);
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
