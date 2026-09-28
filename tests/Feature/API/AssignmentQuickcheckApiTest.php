<?php

namespace Tests\Feature\API;

use App\Models\AssignmentValidation;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use App\Support\Nmr\Assignments\NmrkitAssignmentValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\Feature\Study\StudyAssignmentValidationTest;
use Tests\TestCase;

class AssignmentQuickcheckApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['nmrxiv.assignment_validation.url' => 'https://nmrkit.test/latest/validate/assignments']);
        RateLimiter::clear('assignment-quickcheck');
    }

    /**
     * @return array<string, mixed>
     */
    private function manualPayload(): array
    {
        return [
            'source' => 'manual',
            'solvent' => 'CDCl3',
            'molfile' => file_get_contents(base_path('tests/Fixtures/Assignments/nicotine.mol')),
            'assignments' => [
                ['nucleus' => '13C', 'atoms' => [12], 'label' => 'C-2', 'shift' => 149.91],
                ['nucleus' => '1H', 'atoms' => [3], 'label' => "H-5'a", 'shift' => 2.31, 'n_h' => 1],
            ],
        ];
    }

    private function study(bool $public): Study
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create(['owner_id' => $user->id, 'is_public' => $public]);

        return Study::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $user->id,
            'is_public' => $public,
            'identifier' => 31,
        ]);
    }

    public function test_quickcheck_returns_the_report_without_storing_anything(): void
    {
        Http::fake(['nmrkit.test/*' => Http::response(StudyAssignmentValidationTest::report())]);

        $this->postJson(route('api.quickcheck'), $this->manualPayload())
            ->assertOk()
            ->assertJsonPath('report.verdict', 'accept')
            ->assertJsonPath('input.structure.source', 'manual')
            ->assertJsonPath('input.assignments.1.label', "H-5'a");

        Http::assertSent(fn (Request $request): bool => $request['conditions']['solvent'] === 'CDCl3'
            && $request['assignments'][1] === ['nucleus' => '1H', 'atoms' => [3], 'shift' => 2.31, 'label' => "H-5'a", 'n_h' => 1]);
        $this->assertSame(0, AssignmentValidation::count());
    }

    public function test_quickcheck_maps_nmrkit_errors(): void
    {
        Http::fakeSequence('nmrkit.test/*')
            ->push(['detail' => ['message' => 'Only V2000 molfiles are supported']], 422)
            ->push(['detail' => ['message' => 'Upstream prediction servlet is unavailable']], 503);

        $this->postJson(route('api.quickcheck'), $this->manualPayload())
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Only V2000 molfiles are supported');

        $this->postJson(route('api.quickcheck'), $this->manualPayload())
            ->assertServiceUnavailable()
            ->assertJsonPath('message', NmrkitAssignmentValidator::UNAVAILABLE_MESSAGE);
    }

    public function test_quickcheck_is_rate_limited_per_ip(): void
    {
        config(['nmrxiv.assignment_validation.public_per_minute' => 2]);
        Http::fake(['nmrkit.test/*' => Http::response(StudyAssignmentValidationTest::report())]);

        $this->postJson(route('api.quickcheck'), $this->manualPayload())->assertOk();
        $this->postJson(route('api.quickcheck'), $this->manualPayload())->assertOk();
        $this->postJson(route('api.quickcheck'), $this->manualPayload())->assertTooManyRequests();

        Http::assertSentCount(2);
    }

    public function test_public_sample_exposes_its_latest_completed_check(): void
    {
        $study = $this->study(public: true);
        AssignmentValidation::factory()->completed('accept', 'consistent', 9)->create([
            'study_id' => $study->id,
            'confirmed_by' => $study->owner_id,
            'confirmed_at' => now(),
        ]);
        AssignmentValidation::factory()->failed()->create(['study_id' => $study->id]);

        $this->getJson('/api/v1/samples/S31/assignment-validation')
            ->assertOk()
            ->assertJsonPath('validation.status', 'completed')
            ->assertJsonPath('validation.marks.13C', 9)
            ->assertJsonPath('validation.confirmed', true)
            ->assertJsonPath('validation.stale', false)
            ->assertJsonPath('validation.report.assignment_check.result', 'consistent');

        $this->getJson('/api/v1/samples/'.$study->id.'/assignment-validation')
            ->assertOk()
            ->assertJsonPath('validation.molfile', fn (string $molfile): bool => str_contains($molfile, 'M  END'));
    }

    public function test_private_or_unchecked_samples_are_not_found(): void
    {
        $private = $this->study(public: false);
        AssignmentValidation::factory()->completed()->create(['study_id' => $private->id]);

        $this->getJson(route('api.samples.assignment-validation', $private->id))->assertNotFound();

        $public = Study::factory()->create(['project_id' => $private->project_id, 'owner_id' => $private->owner_id, 'is_public' => true]);
        $this->getJson(route('api.samples.assignment-validation', $public->id))
            ->assertNotFound()
            ->assertJsonPath('message', 'No Quickcheck has been run for this sample.');
    }

    public function test_quickcheck_page_renders(): void
    {
        $this->get(route('quickcheck'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Quickcheck')->where('quickcheckUrl', route('api.quickcheck')));
    }
}
