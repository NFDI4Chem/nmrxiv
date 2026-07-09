<?php

namespace Tests\Feature\Dataset;

use App\Models\Dataset;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatasetAssignmentsTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected Team $team;

    protected Project $project;

    protected Study $study;

    protected Dataset $dataset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();
        $this->team = Team::factory()->create(['user_id' => $this->owner->id]);
        $this->project = Project::factory()->create([
            'team_id' => $this->team->id,
            'owner_id' => $this->owner->id,
        ]);
        $this->study = Study::factory()->create([
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->owner->id,
        ]);
        $this->dataset = Dataset::factory()->create([
            'study_id' => $this->study->id,
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->owner->id,
        ]);

        // The validator dereferences `$study->sample->molecules`, so the
        // study must have at least an empty sample for the report path to
        // execute without NPEs.
        $sample = Sample::factory()->create(['study_id' => $this->study->id]);
        $this->study->sample()->save($sample);
    }

    public function test_endpoint_persists_acs_string_and_marks_dataset_as_assigned(): void
    {
        $this->actingAs($this->owner)
            ->putJson(
                route('dashboard.datasets.assignments.update', $this->dataset),
                ['acs' => '1H NMR (CDCl3): 7.42 (s, 1H), 3.65 (q, 2H)']
            )
            ->assertOk()
            ->assertJsonPath('has_assignments', true)
            ->assertJsonPath('assignments.source', 'manual')
            ->assertJsonPath(
                'assignments.acs',
                '1H NMR (CDCl3): 7.42 (s, 1H), 3.65 (q, 2H)'
            );

        $this->dataset->refresh();
        $this->assertTrue($this->dataset->hasAssignments());
        $this->assertSame(
            '1H NMR (CDCl3): 7.42 (s, 1H), 3.65 (q, 2H)',
            $this->dataset->assignments['acs']
        );
    }

    public function test_endpoint_persists_atom_peaks_rows(): void
    {
        $this->actingAs($this->owner)
            ->putJson(
                route('dashboard.datasets.assignments.update', $this->dataset),
                [
                    'atom_peaks' => [
                        ['atom' => 'C1', 'peak' => 7.42, 'label' => 'aromatic'],
                        ['atom' => 'C2', 'peak' => 3.65],
                    ],
                ]
            )
            ->assertOk()
            ->assertJsonPath('has_assignments', true)
            ->assertJsonCount(2, 'assignments.atom_peaks');

        $this->dataset->refresh();
        $this->assertCount(2, $this->dataset->assignments['atom_peaks']);
        $this->assertSame('C1', $this->dataset->assignments['atom_peaks'][0]['atom']);
    }

    public function test_empty_payload_clears_existing_assignments(): void
    {
        $this->dataset->assignments = [
            'acs' => 'will-be-cleared',
            'atom_peaks' => [],
            'source' => 'manual',
            'updated_at' => now()->toIso8601String(),
        ];
        $this->dataset->save();

        $this->actingAs($this->owner)
            ->putJson(
                route('dashboard.datasets.assignments.update', $this->dataset),
                ['acs' => '   ']
            )
            ->assertOk()
            ->assertJsonPath('has_assignments', false)
            ->assertJsonPath('assignments', null);

        $this->assertNull($this->dataset->fresh()->assignments);
    }

    public function test_endpoint_rejects_unauthorised_user(): void
    {
        $stranger = User::factory()->create();

        $this->actingAs($stranger)
            ->putJson(
                route('dashboard.datasets.assignments.update', $this->dataset),
                ['acs' => 'should not save']
            )
            ->assertForbidden();

        $this->assertNull($this->dataset->fresh()->assignments);
    }

    public function test_validation_report_marks_assignments_satisfied_only_when_saved(): void
    {
        // Empty -> validator must report assignments as failing.
        $report = $this->validationReportFor($this->dataset);
        $this->assertSame('false|array|min:1', $report['assignments']);
        $this->assertFalse($report['status']);

        // Non-empty acs -> validator must report assignments as passing.
        $this->dataset->assignments = [
            'acs' => 'fake assignment',
            'atom_peaks' => [],
            'source' => 'manual',
            'updated_at' => now()->toIso8601String(),
        ];
        $this->dataset->save();

        $report = $this->validationReportFor($this->dataset);
        $this->assertSame('true|array|min:1', $report['assignments']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validationReportFor(Dataset $dataset): array
    {
        $validation = new Validation;
        $validation->save();
        $this->project->validation()->associate($validation)->save();
        foreach ($this->project->studies as $study) {
            $study->validation()->associate($validation)->save();
            foreach ($study->datasets as $ds) {
                $ds->validation()->associate($validation)->save();
            }
        }
        $validation->process();
        $validation->refresh();

        foreach ($validation->report['project']['studies'] ?? [] as $study) {
            foreach ($study['datasets'] ?? [] as $ds) {
                if ((int) ($ds['id'] ?? 0) === $dataset->id) {
                    return $ds;
                }
            }
        }

        $this->fail('Dataset '.$dataset->id.' was not present in the validation report.');
    }
}
