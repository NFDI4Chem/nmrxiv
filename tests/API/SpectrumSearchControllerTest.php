<?php

namespace Tests\API;

use App\Models\Dataset;
use App\Models\DatasetSignal;
use App\Models\License;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class SpectrumSearchControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    private License $license;

    private Validation $validation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->team = Team::factory()->create(['user_id' => $this->user->id]);
        $this->license = License::factory()->create();
        $this->validation = Validation::factory()->create();
    }

    public function test_returns_only_public_datasets(): void
    {
        $public = $this->datasetWithSignals(['1H' => [3.75, 2.10, 1.25]]);
        $this->datasetWithSignals(['1H' => [3.75, 2.10, 1.25]], publicDataset: false);

        $this->search(['peaks' => ['1H' => '3.75, 2.10'], 'group' => 'dataset'])
            ->assertOk()
            ->assertJsonPath('results.meta.total', 1)
            ->assertJsonPath('results.data.0.dataset.id', $public->id)
            ->assertJsonPath('results.data.0.similarity', 1);
    }

    public function test_contains_mode_requires_every_must_have_peak(): void
    {
        $both = $this->datasetWithSignals(['1H' => [3.75, 2.10]]);
        $oneOnly = $this->datasetWithSignals(['1H' => [3.75]]);

        $this->search(['peaks' => ['1H' => '3.75; 2.10'], 'group' => 'dataset'])
            ->assertOk()
            ->assertJsonPath('results.meta.total', 1)
            ->assertJsonPath('results.data.0.dataset.id', $both->id);

        $this->search(['peaks' => ['1H' => '3.75; ?2.10'], 'group' => 'dataset'])
            ->assertOk()
            ->assertJsonPath('results.meta.total', 2)
            ->assertJsonPath('results.data.0.dataset.id', $both->id)
            ->assertJsonPath('results.data.1.dataset.id', $oneOnly->id)
            ->assertJsonPath('results.data.1.missing', [1]);
    }

    public function test_must_not_have_region_excludes_spectra(): void
    {
        $this->datasetWithSignals(['1H' => [3.75, 9.8]]);
        $withoutAldehyde = $this->datasetWithSignals(['1H' => [3.75, 7.0]]);

        $this->search(['peaks' => ['1H' => '3.75; !9.5-10.5'], 'group' => 'dataset'])
            ->assertOk()
            ->assertJsonPath('results.meta.total', 1)
            ->assertJsonPath('results.data.0.dataset.id', $withoutAldehyde->id);
    }

    public function test_whole_spectrum_mode_ranks_identical_spectra_first(): void
    {
        $identical = $this->datasetWithSignals(['13C' => [20.0, 40.0, 60.0]]);
        $larger = $this->datasetWithSignals(['13C' => [20.0, 40.0, 60.0, 80.0, 100.0]]);

        $response = $this->search(['peaks' => ['13C' => '?20; ?40; ?60'], 'mode' => 'whole', 'group' => 'dataset'])
            ->assertOk()
            ->assertJsonPath('results.data.0.dataset.id', $identical->id)
            ->assertJsonPath('results.data.0.similarity', 1)
            ->assertJsonPath('results.data.1.dataset.id', $larger->id);

        $this->assertEqualsWithDelta(0.6, $response->json('results.data.1.similarity'), 0.0001);
    }

    public function test_proton_and_carbon_peaks_must_match_the_same_sample(): void
    {
        $study = $this->publicStudy();
        $this->datasetWithSignals(['1H' => [3.75]], $study);
        $this->datasetWithSignals(['13C' => [55.1]], $study);
        $this->datasetWithSignals(['1H' => [3.75]]);
        $this->datasetWithSignals(['13C' => [55.1]]);

        $this->search(['peaks' => ['1H' => '3.75', '13C' => '55.1']])
            ->assertOk()
            ->assertJsonPath('results.meta.total', 1)
            ->assertJsonPath('results.data.0.sample.id', $study->id)
            ->assertJsonCount(2, 'results.data.0.spectra')
            ->assertJsonPath('results.data.0.spectra.0.nucleus', '1H')
            ->assertJsonPath('results.data.0.spectra.1.nucleus', '13C');
    }

    public function test_groups_matches_by_compound(): void
    {
        $molecule = Molecule::factory()->create();
        $first = $this->publicStudy($molecule);
        $second = $this->publicStudy($molecule);
        $this->datasetWithSignals(['1H' => [3.75, 2.10]], $first);
        $this->datasetWithSignals(['1H' => [3.76, 2.10]], $second);

        $this->search(['peaks' => ['1H' => '3.75; 2.10']])
            ->assertOk()
            ->assertJsonPath('results.meta.total', 1)
            ->assertJsonPath('results.data.0.molecules.0.id', $molecule->id)
            ->assertJsonPath('results.data.0.sample.id', $first->id)
            ->assertJsonPath('results.data.0.found_in_samples', 2)
            ->assertJsonPath('results.data.0.found_in_datasets', 2);

        $this->search(['peaks' => ['1H' => '3.75; 2.10'], 'group' => 'dataset'])
            ->assertOk()
            ->assertJsonPath('results.meta.total', 2);
    }

    public function test_ignores_solvent_peaks_in_the_query_and_stored_spectra(): void
    {
        $dataset = $this->datasetWithSignals(['1H' => [3.75, 2.10]]);
        DatasetSignal::factory()->for($dataset)->create(['nucleus' => '1H', 'shift' => 7.26, 'is_solvent' => true]);

        $this->search(['peaks' => ['1H' => '7.26; 3.75'], 'solvent' => 'CDCl3', 'mode' => 'whole', 'group' => 'dataset'])
            ->assertOk()
            ->assertJsonPath('query.ignored_peaks.1H.0.from', 7.26)
            ->assertJsonPath('results.data.0.record_count', 2);
    }

    public function test_allows_a_calibration_offset_only_when_enabled(): void
    {
        $this->datasetWithSignals(['1H' => [1.08, 2.08, 3.08]]);

        $response = $this->search(['peaks' => ['1H' => '1; 2; 3'], 'group' => 'dataset'])->assertOk();
        $this->assertEqualsWithDelta(0.08, $response->json('results.data.0.offset'), 0.0001);

        $this->search(['peaks' => ['1H' => '1; 2; 3'], 'allow_offset' => 0])->assertNotFound();
    }

    public function test_paginates_results(): void
    {
        foreach (range(1, 3) as $index) {
            $this->datasetWithSignals(['1H' => [3.75]]);
        }

        $this->search(['peaks' => ['1H' => '3.75'], 'group' => 'dataset', 'per_page' => 2, 'page' => 2])
            ->assertOk()
            ->assertJsonPath('results.meta.total', 3)
            ->assertJsonPath('results.meta.per_page', 2)
            ->assertJsonPath('results.meta.current_page', 2)
            ->assertJsonPath('results.meta.last_page', 2)
            ->assertJsonCount(1, 'results.data');
    }

    public function test_rejects_invalid_peaks(): void
    {
        $this->search(['peaks' => ['1H' => '7.2x']])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['peaks.1H' => '“7.2x” is not a number']);

        $this->search(['peaks' => ['1H' => '!9.5-10.5']])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['peaks' => 'Add at least one peak to search for.']);

        $this->search([])->assertUnprocessable()->assertJsonValidationErrors('peaks');
        $this->search(['peaks' => ['19F' => '-120']])->assertUnprocessable()->assertJsonValidationErrors('peaks');
    }

    public function test_returns_not_found_when_nothing_matches(): void
    {
        $this->datasetWithSignals(['1H' => [3.75]]);

        $this->search(['peaks' => ['1H' => '8.5']])
            ->assertNotFound()
            ->assertJsonPath('message', 'No spectra found matching your peaks.');
    }

    public function test_example_peaks_come_from_a_public_sample_and_find_it(): void
    {
        $study = $this->publicStudy();
        $this->datasetWithSignals(['1H' => [7.26, 3.75, 2.10, 1.25, 0.9, 5.1, 6.2]], $study);
        $this->datasetWithSignals(['13C' => [170.1, 60.5, 14.2]], $study);
        $this->datasetWithSignals(['1H' => [8.0, 8.1, 8.2]], publicDataset: false);

        $example = $this->getJson('/api/v1/search/spectra/example')
            ->assertOk()
            ->assertJsonCount(5, 'nuclei.1H.peaks')
            ->assertJsonCount(3, 'nuclei.13C.peaks')
            ->assertJsonPath('sample.name', $study->name);

        $encode = fn (string $nucleus): string => collect($example->json("nuclei.{$nucleus}.peaks"))->pluck('from')->implode(';');

        $this->search(['peaks' => ['1H' => $encode('1H'), '13C' => $encode('13C')]])
            ->assertOk()
            ->assertJsonPath('results.data.0.sample.id', $study->id)
            ->assertJsonPath('results.data.0.similarity', 1);
    }

    public function test_example_peaks_are_rounded_like_reported_shifts(): void
    {
        $study = $this->publicStudy();
        $this->datasetWithSignals(['1H' => [7.2634, 3.7481, 2.1049]], $study);
        $this->datasetWithSignals(['13C' => [170.1234, 60.4871]], $study);

        $this->getJson('/api/v1/search/spectra/example')
            ->assertOk()
            ->assertJsonPath('nuclei.1H.peaks.*.from', [7.26, 3.75, 2.1])
            ->assertJsonPath('nuclei.13C.peaks.*.from', [170.1, 60.5]);
    }

    public function test_example_is_not_found_without_detected_peaks(): void
    {
        $this->getJson('/api/v1/search/spectra/example')->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function search(array $params): TestResponse
    {
        return $this->getJson('/api/v1/search/spectra?'.http_build_query($params));
    }

    private function publicStudy(?Molecule $molecule = null): Study
    {
        $project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $study = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'is_public' => true,
            'is_archived' => false,
        ]);

        if ($molecule !== null) {
            Sample::factory()->create(['study_id' => $study->id, 'project_id' => $project->id])
                ->molecules()->attach($molecule);
        }

        return $study;
    }

    /**
     * @param  array<string, list<float>>  $shiftsByNucleus
     */
    private function datasetWithSignals(array $shiftsByNucleus, ?Study $study = null, bool $publicDataset = true): Dataset
    {
        $study ??= $this->publicStudy();

        $dataset = Dataset::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $study->project_id,
            'study_id' => $study->id,
            'is_public' => $publicDataset,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        foreach ($shiftsByNucleus as $nucleus => $shifts) {
            foreach ($shifts as $shift) {
                DatasetSignal::factory()->for($dataset)->create([
                    'nucleus' => $nucleus,
                    'shift' => $shift,
                    'multiplicity' => null,
                    'intensity' => null,
                ]);
            }
        }

        return $dataset;
    }
}
