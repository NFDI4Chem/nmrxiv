<?php

namespace Tests\Feature\Draft;

use App\Jobs\ExtractDraftArchive;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\User;
use App\Services\FileSystemObjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DraftArchiveControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Draft $draft;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->withPersonalTeam()->create();

        [$userId, $teamId] = $this->user->getUserTeamData();

        $this->draft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
        ]);
    }

    public function test_extract_requires_authentication(): void
    {
        $this->postJson($this->extractUrl(), ['keys' => ['anything.zip']])
            ->assertStatus(401);
    }

    public function test_extract_is_forbidden_for_users_who_cannot_update_the_draft(): void
    {
        Queue::fake();

        $archive = $this->createDraftFile('sample.zip');

        $this->actingAs(User::factory()->withPersonalTeam()->create())
            ->postJson($this->extractUrl(), ['keys' => [ltrim($archive->path, '/')]])
            ->assertForbidden();

        Queue::assertNothingPushed();
    }

    public function test_extract_validates_keys(): void
    {
        $this->actingAs($this->user)
            ->postJson($this->extractUrl(), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['keys']);
    }

    public function test_extract_queues_only_zip_files_belonging_to_the_draft(): void
    {
        Queue::fake();

        $archive = $this->createDraftFile('sample1/data.ZIP');
        $notAnArchive = $this->createDraftFile('sample1/fid');
        $otherDraft = Draft::factory()->create();
        $foreignArchive = $this->createDraftFile('foreign.zip', $otherDraft);

        $this->actingAs($this->user)
            ->postJson($this->extractUrl(), ['keys' => [
                ltrim($archive->path, '/'),
                ltrim($notAnArchive->path, '/'),
                ltrim($foreignArchive->path, '/'),
            ]])
            ->assertStatus(202)
            ->assertJsonPath('queued', 1)
            ->assertJsonPath('archives.0.id', $archive->id)
            ->assertJsonPath('archives.0.status', FileSystemObject::EXTRACTION_PENDING);

        Queue::assertPushed(ExtractDraftArchive::class, 1);
        Queue::assertPushed(ExtractDraftArchive::class, fn (ExtractDraftArchive $job) => $job->draftId === $this->draft->id
            && $job->fileSystemObjectId === $archive->id);

        $this->assertSame(FileSystemObject::EXTRACTION_PENDING, $archive->fresh()->extraction()['status']);
        $this->assertNull($foreignArchive->fresh()->extraction());
    }

    public function test_extract_keeps_hifsa_export_zips_zipped(): void
    {
        Queue::fake();

        $exportZip = $this->createDraftFile('sample1/analysis/compound_export.zip');
        $hifsaZip = $this->createDraftFile('sample1/HiFSA/report.zip');

        $this->actingAs($this->user)
            ->postJson($this->extractUrl(), ['keys' => [
                ltrim($exportZip->path, '/'),
                ltrim($hifsaZip->path, '/'),
            ]])
            ->assertStatus(202)
            ->assertJsonPath('queued', 0);

        Queue::assertNothingPushed();
        $this->assertNull($exportZip->fresh()->extraction());
        $this->assertNull($hifsaZip->fresh()->extraction());
    }

    public function test_extract_does_not_requeue_archives_already_in_progress(): void
    {
        Queue::fake();

        $archive = $this->createDraftFile('sample.zip');
        $archive->markExtraction(FileSystemObject::EXTRACTION_PROCESSING);

        $this->actingAs($this->user)
            ->postJson($this->extractUrl(), ['keys' => [ltrim($archive->path, '/')]])
            ->assertStatus(202)
            ->assertJsonPath('queued', 0);

        Queue::assertNothingPushed();
    }

    public function test_index_lists_pending_processing_and_failed_archives(): void
    {
        $pending = $this->createDraftFile('pending.zip');
        $pending->markExtraction(FileSystemObject::EXTRACTION_PENDING);
        $failed = $this->createDraftFile('failed.zip');
        $failed->markExtraction(FileSystemObject::EXTRACTION_FAILED, 'Broken archive');
        $processing = $this->createDraftFile('processing.zip');
        $processing->markExtraction(FileSystemObject::EXTRACTION_PROCESSING, null, ['extracted' => 40, 'total' => 120]);
        $this->createDraftFile('untouched.zip');

        $response = $this->actingAs($this->user)
            ->getJson("/dashboard/drafts/{$this->draft->id}/archives")
            ->assertOk()
            ->assertJsonCount(3, 'archives');

        $archives = collect($response->json('archives'))->keyBy('name');

        $this->assertSame(FileSystemObject::EXTRACTION_PENDING, $archives['pending.zip']['status']);
        $this->assertNull($archives['pending.zip']['total']);
        $this->assertSame(FileSystemObject::EXTRACTION_FAILED, $archives['failed.zip']['status']);
        $this->assertSame('Broken archive', $archives['failed.zip']['error']);
        $this->assertSame(FileSystemObject::EXTRACTION_PROCESSING, $archives['processing.zip']['status']);
        $this->assertSame(40, $archives['processing.zip']['extracted']);
        $this->assertSame(120, $archives['processing.zip']['total']);
    }

    public function test_index_is_forbidden_for_users_who_cannot_update_the_draft(): void
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create())
            ->getJson("/dashboard/drafts/{$this->draft->id}/archives")
            ->assertForbidden();
    }

    private function extractUrl(): string
    {
        return "/dashboard/drafts/{$this->draft->id}/archives/extract";
    }

    private function createDraftFile(string $relativePath, ?Draft $draft = null): FileSystemObject
    {
        $draft ??= $this->draft;

        $path = app(FileSystemObjectService::class)->createDraftFileSystemObject($draft, [
            'upload' => ['filename' => basename($relativePath), 'total' => 100],
            'fullPath' => $relativePath,
        ], '/');

        return FileSystemObject::query()
            ->where('draft_id', $draft->id)
            ->where('path', $path)
            ->firstOrFail();
    }
}
