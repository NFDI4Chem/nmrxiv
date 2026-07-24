<?php

namespace Tests\Feature\Draft;

use App\Models\Dataset;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DraftHifsaPdfTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    private Draft $draft;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->withPersonalTeam()->create();
        $this->team = $this->user->currentTeam;
        $this->draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);
        $this->project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'draft_id' => $this->draft->id,
        ]);
    }

    private function makeStudyWithHifsaSibling(): array
    {
        $sampleRoot = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'level' => 0,
            'name' => 'Compound',
        ]);

        $study = Study::factory()->create([
            'name' => 'Compound',
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'draft_id' => $this->draft->id,
            'is_public' => false,
        ]);

        $studyFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $sampleRoot->id,
            'level' => 1,
            'name' => 'raw',
            'model_type' => 'study',
            'study_id' => $study->id,
        ]);
        $study->update(['fs_id' => $studyFolder->id]);

        Dataset::factory()->create([
            'study_id' => $study->id,
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
        ]);

        $hifsaFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $sampleRoot->id,
            'level' => 1,
            'name' => 'hifsa',
            'instrument_type' => 'hifsa',
        ]);

        $pdf = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $hifsaFolder->id,
            'name' => 'report.pdf',
            'path' => '/drafts/compound/hifsa/report.pdf',
        ]);

        return [$study, $pdf];
    }

    public function test_info_exposes_hifsa_pdf_url_for_a_study_with_a_hifsa_sibling(): void
    {
        [$study, $pdf] = $this->makeStudyWithHifsaSibling();

        $response = $this->actingAs($this->user)
            ->getJson("/dashboard/drafts/{$this->draft->id}/info");

        $response->assertStatus(200);

        $studies = $response->json('studies');
        $this->assertCount(1, $studies);
        $this->assertStringContainsString(
            "/dashboard/drafts/{$this->draft->id}/hifsa/{$pdf->id}",
            $studies[0]['hifsa_pdf_url']
        );
    }

    public function test_info_hifsa_pdf_url_is_null_without_a_hifsa_folder(): void
    {
        $study = Study::factory()->create([
            'name' => 'Plain',
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'draft_id' => $this->draft->id,
            'is_public' => false,
        ]);
        $studyFolder = FileSystemObject::factory()->directory()->rootLevel()->create([
            'draft_id' => $this->draft->id,
            'name' => 'Plain',
            'model_type' => 'study',
            'study_id' => $study->id,
        ]);
        $study->update(['fs_id' => $studyFolder->id]);
        Dataset::factory()->create([
            'study_id' => $study->id,
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/dashboard/drafts/{$this->draft->id}/info");

        $response->assertStatus(200);
        $this->assertNull($response->json('studies.0.hifsa_pdf_url'));
    }

    public function test_hifsa_file_streams_pdf_inline(): void
    {
        Storage::fake(config('filesystems.default'));

        [, $pdf] = $this->makeStudyWithHifsaSibling();

        Storage::disk(config('filesystems.default'))
            ->put('drafts/compound/hifsa/report.pdf', '%PDF-1.4 fake');

        $response = $this->actingAs($this->user)
            ->get("/dashboard/drafts/{$this->draft->id}/hifsa/{$pdf->id}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
    }

    public function test_hifsa_file_rejects_non_pdf(): void
    {
        $nonPdf = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'analysis.blob',
            'path' => '/drafts/compound/hifsa/analysis.blob',
        ]);

        $response = $this->actingAs($this->user)
            ->get("/dashboard/drafts/{$this->draft->id}/hifsa/{$nonPdf->id}");

        $response->assertStatus(404);
    }

    public function test_hifsa_file_rejects_fso_from_another_draft(): void
    {
        $otherDraft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);

        $pdf = FileSystemObject::factory()->file()->create([
            'draft_id' => $otherDraft->id,
            'name' => 'report.pdf',
            'path' => '/drafts/other/report.pdf',
        ]);

        $response = $this->actingAs($this->user)
            ->get("/dashboard/drafts/{$this->draft->id}/hifsa/{$pdf->id}");

        $response->assertStatus(403);
    }
}
