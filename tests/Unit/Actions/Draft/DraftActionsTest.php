<?php

namespace Tests\Unit\Actions\Draft;

use App\Actions\Draft\CreateDraft;
use App\Actions\Draft\DraftFiles;
use App\Actions\Draft\GetOrCreateCommunityDraft;
use App\Actions\Draft\UserDrafts;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DraftActionsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->withPersonalTeam()->create();
        $this->team = $this->user->currentTeam;
    }

    public function test_create_draft_action_creates_basic_draft(): void
    {
        $createDraft = new CreateDraft;

        $draft = $createDraft->execute($this->user);

        $this->assertInstanceOf(Draft::class, $draft);
        $this->assertEquals($this->user->id, $draft->owner_id);
        $this->assertEquals($this->team->id, $draft->team_id);
        $this->assertNotNull($draft->key);
        $this->assertNotNull($draft->path);
        $this->assertNotNull($draft->relative_url);
        $this->assertStringContainsString('Untitled Project', $draft->name);
    }

    public function test_create_draft_action_creates_eln_draft(): void
    {
        $createDraft = new CreateDraft;
        $options = [
            'eln' => 'chemotion',
            'external_id' => 'EXT-123',
            'callback_url' => 'https://example.com/callback',
            'zip_url' => 'https://example.com/data.zip',
            'release_date' => '2024-12-31',
        ];

        $draft = $createDraft->execute($this->user, $options);

        $this->assertEquals('chemotion', $draft->eln);
        $this->assertEquals('EXT-123', $draft->external_id);
        $this->assertEquals('https://example.com/callback', $draft->callback_url);
        $this->assertEquals('https://example.com/data.zip', $draft->zip_url);
        $this->assertEquals('2024-12-31', $draft->release_date->format('Y-m-d'));
        $this->assertStringContainsString('ELN Import (CHEMOTION:', $draft->name);
        $this->assertStringContainsString('Draft created from ELN system: chemotion', $draft->description);
    }

    public function test_create_draft_action_finds_existing_by_external_id(): void
    {
        $createDraft = new CreateDraft;
        [$userId, $teamId] = $this->user->getUserTeamData();

        // Create an existing draft
        $existingDraft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
            'external_id' => 'EXT-123',
        ]);

        $foundDraft = $createDraft->findByExternalId('EXT-123', $userId, $teamId);

        $this->assertInstanceOf(Draft::class, $foundDraft);
        $this->assertEquals($existingDraft->id, $foundDraft->id);
        $this->assertEquals('EXT-123', $foundDraft->external_id);
    }

    public function test_create_draft_action_updates_existing_draft(): void
    {
        $createDraft = new CreateDraft;
        [$userId, $teamId] = $this->user->getUserTeamData();

        $draft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
            'name' => 'Original Name',
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ];

        $updatedDraft = $createDraft->update($draft, $updateData);

        $this->assertEquals('Updated Name', $updatedDraft->name);
        $this->assertEquals('Updated Description', $updatedDraft->description);
    }

    public function test_user_drafts_action_finds_default_draft(): void
    {
        $userDrafts = app(UserDrafts::class);
        [$userId, $teamId] = $this->user->getUserTeamData();

        // Create a draft without files (should be considered default)
        $defaultDraft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
        ]);

        $foundDraft = $userDrafts->findDefaultDraft($this->user);

        $this->assertInstanceOf(Draft::class, $foundDraft);
        $this->assertEquals($defaultDraft->id, $foundDraft->id);
    }

    public function test_user_drafts_action_creates_default_draft_if_none_exists(): void
    {
        $userDrafts = app(UserDrafts::class);

        $defaultDraft = $userDrafts->getOrCreateDefaultDraft($this->user);

        $this->assertInstanceOf(Draft::class, $defaultDraft);
        $this->assertEquals($this->user->id, $defaultDraft->owner_id);
        $this->assertEquals($this->team->id, $defaultDraft->team_id);
    }

    public function test_user_drafts_action_excludes_community_drafts_when_requested(): void
    {
        $userDrafts = app(UserDrafts::class);
        [$userId, $teamId] = $this->user->getUserTeamData();

        $publicationDraft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
        ]);
        FileSystemObject::factory()->file()->create([
            'draft_id' => $publicationDraft->id,
        ]);

        $communityDraft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
            'settings' => ['deposition_type' => 'community'],
        ]);
        FileSystemObject::factory()->file()->create([
            'draft_id' => $communityDraft->id,
        ]);

        $allDrafts = $userDrafts->execute($this->user);
        $publicationDrafts = $userDrafts->execute($this->user, true);

        $this->assertCount(2, $allDrafts);
        $this->assertCount(1, $publicationDrafts);
        $this->assertEquals($publicationDraft->id, $publicationDrafts->first()->id);
        $this->assertTrue($userDrafts->isCommunityDraft($communityDraft));
    }

    public function test_user_drafts_action_excludes_deleted_drafts(): void
    {
        $userDrafts = app(UserDrafts::class);
        [$userId, $teamId] = $this->user->getUserTeamData();

        // Create active and deleted drafts
        $activeDraft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
            'is_deleted' => false,
        ]);
        FileSystemObject::factory()->file()->create(['draft_id' => $activeDraft->id]);

        $deletedDraft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
            'is_deleted' => true,
        ]);
        FileSystemObject::factory()->file()->create(['draft_id' => $deletedDraft->id]);

        $drafts = $userDrafts->execute($this->user);

        $this->assertCount(1, $drafts);
        $this->assertEquals($activeDraft->id, $drafts->first()->id);
    }

    public function test_draft_files_action_returns_file_tree(): void
    {
        $draftFiles = new DraftFiles;
        [$userId, $teamId] = $this->user->getUserTeamData();

        $draft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
        ]);

        // Create root level files
        FileSystemObject::factory()->count(3)->file()->create([
            'draft_id' => $draft->id,
            'level' => 0,
        ]);

        $filesData = $draftFiles->files($draft);

        $this->assertArrayHasKey('file', $filesData);
        $this->assertArrayHasKey('missing_files', $filesData);
        $this->assertEquals('/', $filesData['file']['name']);
        $this->assertArrayHasKey('children', $filesData['file']);
        $this->assertCount(3, $filesData['file']['children']);
        $this->assertIsInt($filesData['missing_files']);
    }

    public function test_draft_files_includes_root_folders_with_unassigned_study_id(): void
    {
        $draftFiles = new DraftFiles;
        [$userId, $teamId] = $this->user->getUserTeamData();

        $draft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
        ]);

        $folder = FileSystemObject::factory()->create([
            'draft_id' => $draft->id,
            'level' => 0,
            'type' => 'directory',
            'study_id' => 0,
            'name' => 'sample-a',
        ]);

        $filesData = $draftFiles->files($draft);

        $this->assertTrue($filesData['has_sample_folders']);
        $this->assertCount(1, $filesData['file']['children']);
        $this->assertSame($folder->id, $filesData['file']['children'][0]->id);
    }

    public function test_draft_files_action_counts_missing_files(): void
    {
        $draftFiles = new DraftFiles;
        [$userId, $teamId] = $this->user->getUserTeamData();

        $draft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
        ]);

        // Create files with different statuses
        FileSystemObject::factory()->count(2)->file()->missing()->create([
            'draft_id' => $draft->id,
        ]);
        FileSystemObject::factory()->file()->create([
            'draft_id' => $draft->id,
            'status' => 'present',
        ]);

        $filesData = $draftFiles->files($draft);

        $this->assertEquals(2, $filesData['missing_files']);
    }

    public function test_draft_files_action_returns_missing_files_list(): void
    {
        $draftFiles = new DraftFiles;
        [$userId, $teamId] = $this->user->getUserTeamData();

        $draft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
        ]);

        // Create missing files
        $missingFile1 = FileSystemObject::factory()->file()->missing()->create([
            'draft_id' => $draft->id,
            'relative_url' => '/path/to/missing1.txt',
        ]);
        $missingFile2 = FileSystemObject::factory()->file()->missing()->create([
            'draft_id' => $draft->id,
            'relative_url' => '/path/to/missing2.txt',
        ]);

        $missingData = $draftFiles->missing($draft);

        $this->assertArrayHasKey('missing_files', $missingData);
        $this->assertCount(2, $missingData['missing_files']);
        $this->assertEquals('/path/to/missing1.txt', $missingData['missing_files'][0]->relative_url);
        $this->assertEquals('/path/to/missing2.txt', $missingData['missing_files'][1]->relative_url);
    }

    public function test_draft_files_action_handles_empty_draft(): void
    {
        $draftFiles = new DraftFiles;
        [$userId, $teamId] = $this->user->getUserTeamData();

        $draft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
        ]);

        $filesData = $draftFiles->files($draft);

        $this->assertEquals('/', $filesData['file']['name']);
        $this->assertEmpty($filesData['file']['children']);
        $this->assertEquals(0, $filesData['missing_files']);
    }

    public function test_draft_files_action_paginates_when_many_sample_folders(): void
    {
        $draftFiles = new DraftFiles;
        [$userId, $teamId] = $this->user->getUserTeamData();

        $draft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
        ]);

        $threshold = DraftFiles::SAMPLE_FOLDER_LAZY_THRESHOLD;

        FileSystemObject::factory()->count($threshold + 3)->create([
            'draft_id' => $draft->id,
            'level' => 0,
            'type' => 'directory',
            'has_children' => true,
        ]);

        $filesData = $draftFiles->files($draft);

        $this->assertArrayHasKey('sample_folders', $filesData);
        $this->assertCount(
            DraftFiles::SAMPLE_FOLDER_PAGE_SIZE,
            $filesData['file']['children']
        );
        $this->assertEquals($threshold + 3, $filesData['sample_folders']['total']);
        $this->assertTrue($filesData['sample_folders']['has_more']);

        $pageTwo = $draftFiles->sampleFoldersPage($draft, 2);

        $this->assertCount(3, $pageTwo['folders']);
        $this->assertFalse($pageTwo['sample_folders']['has_more']);
    }

    public function test_draft_path_generation_is_correct(): void
    {
        $createDraft = new CreateDraft;
        $draft = $createDraft->execute($this->user);

        $expectedPathPattern = '/^'.preg_quote(config('app.env', 'local'), '/').'\/'.$this->user->id.'\/drafts\/[a-f0-9\-]{36}$/';
        $this->assertMatchesRegularExpression($expectedPathPattern, $draft->path);
    }

    public function test_draft_relative_url_generation_is_correct(): void
    {
        $createDraft = new CreateDraft;
        $draft = $createDraft->execute($this->user);

        $this->assertStringStartsWith('/', $draft->relative_url);
        $this->assertStringNotContainsString('//', $draft->relative_url);
    }

    public function test_draft_name_generation_for_eln(): void
    {
        $createDraft = new CreateDraft;
        $options = ['eln' => 'chemotion'];

        $draft = $createDraft->execute($this->user, $options);

        $this->assertStringContainsString('ELN Import (CHEMOTION:', $draft->name);
        $this->assertStringContainsString(')', $draft->name);
    }

    public function test_draft_name_generation_for_regular_draft(): void
    {
        $createDraft = new CreateDraft;

        $draft = $createDraft->execute($this->user);

        $this->assertStringContainsString('Untitled Project (Draft:', $draft->name);
        $this->assertStringContainsString(')', $draft->name);
    }

    public function test_create_draft_action_creates_community_draft(): void
    {
        $createDraft = new CreateDraft;

        $draft = $createDraft->execute($this->user, [
            'deposition_type' => 'community',
        ]);

        $this->assertStringContainsString('Community Contribution (Draft:', $draft->name);
        $this->assertSame('community', $draft->settings['deposition_type']);
    }

    public function test_user_drafts_action_finds_community_draft(): void
    {
        $userDrafts = app(UserDrafts::class);
        [$userId, $teamId] = $this->user->getUserTeamData();

        $communityDraft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
            'settings' => ['deposition_type' => 'community'],
        ]);

        $foundDraft = $userDrafts->findCommunityDraft($this->user);

        $this->assertInstanceOf(Draft::class, $foundDraft);
        $this->assertEquals($communityDraft->id, $foundDraft->id);
    }

    public function test_default_draft_lookup_excludes_community_drafts(): void
    {
        $userDrafts = app(UserDrafts::class);
        [$userId, $teamId] = $this->user->getUserTeamData();

        Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
            'settings' => ['deposition_type' => 'community'],
        ]);

        $this->assertNull($userDrafts->findDefaultDraft($this->user));
    }

    public function test_get_or_create_community_draft_reuses_existing_draft(): void
    {
        $action = app(GetOrCreateCommunityDraft::class);

        $firstDraft = $action->execute($this->user);
        $secondDraft = $action->execute($this->user);

        $this->assertSame($firstDraft->id, $secondDraft->id);
        $this->assertSame('community', $secondDraft->settings['deposition_type']);
    }

    public function test_find_community_draft_prefers_draft_with_files(): void
    {
        $userDrafts = app(UserDrafts::class);
        [$userId, $teamId] = $this->user->getUserTeamData();

        $emptyDraft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
            'settings' => ['deposition_type' => 'community'],
        ]);

        $draftWithFiles = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
            'settings' => ['deposition_type' => 'community'],
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $draftWithFiles->id,
        ]);

        $foundDraft = $userDrafts->findCommunityDraft($this->user);

        $this->assertEquals($draftWithFiles->id, $foundDraft->id);
        $this->assertNotEquals($emptyDraft->id, $foundDraft->id);
    }
}
