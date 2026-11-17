<?php

namespace Tests\Feature\Study;

use App\Enums\AssignmentSource;
use App\Enums\AssignmentValidationStatus;
use App\Models\AssignmentValidation;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Support\Nmr\Assignments\AssignmentSetResolver;
use App\Support\Nmr\Assignments\NmrkitAssignmentValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\Unit\Support\Nmr\Assignments\NmriumAssignmentReaderTest;

class StudyAssignmentValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected Study $study;

    protected function setUp(): void
    {
        parent::setUp();

        config(['nmrxiv.assignment_validation.url' => 'https://nmrkit.test/latest/validate/assignments']);

        $this->owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $this->owner->id]);
        $project = Project::factory()->create(['team_id' => $team->id, 'owner_id' => $this->owner->id]);
        $this->study = Study::factory()->create([
            'project_id' => $project->id,
            'team_id' => $team->id,
            'owner_id' => $this->owner->id,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function report(string $verdict = 'accept', string $assignmentResult = 'consistent'): array
    {
        return [
            'engine' => ['name' => 'prediction', 'source' => 'shift prediction', 'url' => 'https://prediction.test'],
            'solvent' => 'Chloroform-D1 (CDCl3)',
            'verdict' => $verdict,
            'reports' => [
                '13C' => ['mark' => 10, 'mark_is_approximate' => true, 'result' => 'accept', 'atoms' => []],
                '1H' => ['mark' => 9, 'mark_is_approximate' => true, 'result' => 'accept', 'atoms' => []],
            ],
            'assignment_check' => ['result' => $assignmentResult, 'rows' => [], 'suggestions' => [], 'issues' => []],
            'adjustments' => [],
            'cached' => false,
        ];
    }

    private function mnovaUpload(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            'trimethoxybenzaldehyde.sdf',
            file_get_contents(base_path('tests/Fixtures/Assignments/trimethoxybenzaldehyde-mnova.sdf'))
        );
    }

    private function giveStudyNmriumAssignments(): NMRium
    {
        return NMRium::factory()->forStudy($this->study)->create([
            'nmrium_info' => NmriumAssignmentReaderTest::nicotineState(),
        ]);
    }

    public function test_uploaded_mnova_file_is_checked_and_the_report_stored(): void
    {
        Http::fake(['nmrkit.test/*' => Http::response(self::report())]);

        $this->actingAs($this->owner)
            ->post(route('dashboard.studies.assignment-validation.store', $this->study), [
                'source' => 'file',
                'file' => $this->mnovaUpload(),
            ], ['Accept' => 'application/json'])
            ->assertAccepted()
            ->assertJsonPath('validation.status', 'completed')
            ->assertJsonPath('validation.source', 'mnova_sdf')
            ->assertJsonPath('validation.verdict', 'accept')
            ->assertJsonPath('validation.marks.13C', 10)
            ->assertJsonPath('validation.marks.1H', 9);

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://nmrkit.test/latest/validate/assignments'
                && $request['structure']['source'] === 'mnova_sdf'
                && $request['conditions']['solvent'] === 'Chloroform'
                && count($request['assignments']) === 11;
        });

        $validation = $this->study->latestAssignmentValidation;
        $this->assertSame($this->owner->id, $validation->requested_by);
        $this->assertSame('consistent', $validation->assignment_result);
        $this->assertSame(64, strlen($validation->input_hash));
    }

    public function test_nmrium_assignments_of_the_sample_are_checked(): void
    {
        Http::fake(['nmrkit.test/*' => Http::response(self::report())]);
        $this->giveStudyNmriumAssignments();

        $this->actingAs($this->owner)
            ->getJson(route('dashboard.studies.assignment-validation.show', $this->study))
            ->assertOk()
            ->assertJsonPath('nmrium.available', true)
            ->assertJsonPath('nmrium.assigned', 5)
            ->assertJsonPath('nmrium.nuclei', ['13C', '1H'])
            ->assertJsonPath('validation', null);

        $this->actingAs($this->owner)
            ->postJson(route('dashboard.studies.assignment-validation.store', $this->study), ['source' => 'nmrium'])
            ->assertAccepted()
            ->assertJsonPath('validation.source', 'nmrium')
            ->assertJsonPath('validation.status', 'completed');

        Http::assertSent(fn (Request $request): bool => $request['assignments'][3]['atoms'] === [22]
            && $request['unassigned_peaks'][0]['kind'] === 'solvent');
    }

    public function test_nmrium_without_assignments_is_a_validation_error(): void
    {
        Http::fake();

        $this->actingAs($this->owner)
            ->postJson(route('dashboard.studies.assignment-validation.store', $this->study), ['source' => 'nmrium'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('source');

        Http::assertNothingSent();
    }

    public function test_manual_rows_need_at_least_one_assigned_atom(): void
    {
        Http::fake();

        $this->actingAs($this->owner)
            ->postJson(route('dashboard.studies.assignment-validation.store', $this->study), [
                'source' => 'manual',
                'molfile' => file_get_contents(base_path('tests/Fixtures/Assignments/nicotine.mol')),
                'assignments' => [['nucleus' => '13C', 'atoms' => [], 'shift' => 40.3]],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('assignments');

        $this->actingAs($this->owner)
            ->postJson(route('dashboard.studies.assignment-validation.store', $this->study), [
                'source' => 'manual',
                'molfile' => 'not a molfile',
                'assignments' => [['nucleus' => '15N', 'atoms' => [2], 'shift' => 40.3]],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['molfile', 'assignments.0.nucleus']);

        Http::assertNothingSent();
    }

    public function test_rejected_input_marks_the_check_failed_with_nmrkit_message(): void
    {
        Http::fake(['nmrkit.test/*' => Http::response(
            ['detail' => ['error' => 'invalid_input', 'message' => 'Only V2000 molfiles are supported']],
            422
        )]);

        $this->actingAs($this->owner)
            ->post(route('dashboard.studies.assignment-validation.store', $this->study), [
                'source' => 'file',
                'file' => $this->mnovaUpload(),
            ], ['Accept' => 'application/json'])
            ->assertAccepted()
            ->assertJsonPath('validation.status', 'failed')
            ->assertJsonPath('validation.error', 'Only V2000 molfiles are supported');
    }

    public function test_unavailable_servlet_fails_the_check_after_the_last_try(): void
    {
        config(['nmrxiv.assignment_validation.job_tries' => 1]);
        Http::fake(['nmrkit.test/*' => Http::response(['detail' => ['message' => 'Upstream prediction servlet is unavailable']], 503)]);

        $this->actingAs($this->owner)
            ->post(route('dashboard.studies.assignment-validation.store', $this->study), [
                'source' => 'file',
                'file' => $this->mnovaUpload(),
            ], ['Accept' => 'application/json'])
            ->assertAccepted()
            ->assertJsonPath('validation.status', 'failed')
            ->assertJsonPath('validation.error', NmrkitAssignmentValidator::UNAVAILABLE_MESSAGE);
    }

    public function test_only_study_editors_can_queue_or_view_checks(): void
    {
        Http::fake();
        $stranger = User::factory()->create();

        $this->actingAs($stranger)
            ->post(route('dashboard.studies.assignment-validation.store', $this->study), [
                'source' => 'file',
                'file' => $this->mnovaUpload(),
            ], ['Accept' => 'application/json'])
            ->assertForbidden();

        $this->actingAs($stranger)
            ->getJson(route('dashboard.studies.assignment-validation.show', $this->study))
            ->assertForbidden();

        Http::assertNothingSent();
    }

    public function test_author_confirms_a_consistent_check(): void
    {
        $validation = AssignmentValidation::factory()->completed()->create(['study_id' => $this->study->id]);

        $this->actingAs($this->owner)
            ->postJson(route('dashboard.studies.assignment-validation.confirm', [$this->study, $validation]))
            ->assertOk()
            ->assertJsonPath('validation.confirmed', true)
            ->assertJsonPath('validation.confirmed_by', $this->owner->name)
            ->assertJsonPath('validation.confirmation_note', null);

        $this->assertSame($this->owner->id, $validation->fresh()->confirmed_by);
    }

    public function test_confirming_an_inconsistent_check_requires_a_note(): void
    {
        $validation = AssignmentValidation::factory()
            ->completed('reject', 'inconsistent', 6)
            ->create(['study_id' => $this->study->id]);

        $this->actingAs($this->owner)
            ->postJson(route('dashboard.studies.assignment-validation.confirm', [$this->study, $validation]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('note');

        $this->actingAs($this->owner)
            ->postJson(route('dashboard.studies.assignment-validation.confirm', [$this->study, $validation]), [
                'note' => 'C-4 confirmed by HMBC from H-2/H-6; new skeleton outside the database.',
            ])
            ->assertOk()
            ->assertJsonPath('validation.confirmed', true)
            ->assertJsonPath('validation.requires_confirmation_note', true);
    }

    public function test_pending_or_failed_checks_cannot_be_confirmed(): void
    {
        $failed = AssignmentValidation::factory()->failed()->create(['study_id' => $this->study->id]);

        $this->actingAs($this->owner)
            ->postJson(route('dashboard.studies.assignment-validation.confirm', [$this->study, $failed]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('validation');
    }

    public function test_check_of_changed_nmrium_assignments_is_stale_and_cannot_be_confirmed(): void
    {
        Http::fake(['nmrkit.test/*' => Http::response(self::report())]);
        $nmrium = $this->giveStudyNmriumAssignments();

        $this->actingAs($this->owner)
            ->postJson(route('dashboard.studies.assignment-validation.store', $this->study), ['source' => 'nmrium'])
            ->assertAccepted();
        $validation = $this->study->latestAssignmentValidation;
        $this->assertSame(AssignmentSource::Nmrium, $validation->source);

        $state = $nmrium->nmrium_info;
        $state['data']['spectra'][0]['ranges']['values'][0]['signals'][0]['delta'] = 150.5;
        $nmrium->update(['nmrium_info' => $state]);

        $this->actingAs($this->owner)
            ->getJson(route('dashboard.studies.assignment-validation.show', $this->study))
            ->assertOk()
            ->assertJsonPath('validation.stale', true);

        $this->actingAs($this->owner)
            ->postJson(route('dashboard.studies.assignment-validation.confirm', [$this->study, $validation]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('validation');
    }

    public function test_identical_pending_request_reuses_the_queued_check(): void
    {
        Http::fake();
        $this->giveStudyNmriumAssignments();
        $set = app(AssignmentSetResolver::class)->fromStudyNmrium($this->study);
        $pending = AssignmentValidation::queueFor($this->study, $set, $this->owner);

        $this->actingAs($this->owner)
            ->postJson(route('dashboard.studies.assignment-validation.store', $this->study), ['source' => 'nmrium'])
            ->assertAccepted()
            ->assertJsonPath('validation.id', $pending->id)
            ->assertJsonPath('validation.status', AssignmentValidationStatus::Queued->value);

        $this->assertSame(1, $this->study->assignmentValidations()->count());
        Http::assertNothingSent();
    }
}
