<?php

namespace Tests\Feature\Draft;

use App\Jobs\ArchiveStudy;
use App\Models\Dataset;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ResetSampleFolderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    private Draft $draft;

    private Project $project;

    private Study $study;

    private FileSystemObject $sampleFolder;

    protected function setUp(): void
    {
        parent::setUp();

        // Pin the cache to the in-process `array` driver so any unique-job
        // dispatch checks happen in memory and don't leak across tests.
        // (See FileSystemObjectArchiveInvalidationTest for context.)
        Config::set('cache.default', 'array');
        $manager = Cache::getFacadeRoot();
        if (method_exists($manager, 'forgetDriver')) {
            $manager->forgetDriver();
        }
        app()->forgetInstance('cache.store');
        app()->forgetInstance(Repository::class);

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
        $this->study = Study::factory()->create([
            'name' => 'sample-1',
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
        ]);
        // The default Study factory sets `fs_id => 1` (a stale placeholder).
        // Combined with the auto-incremented id of the FSO we create below
        // also being 1, the FileSystemObjectObserver's `created` event would
        // immediately clear `model_type` and `is_processed` on the new FSO,
        // undoing the test scaffolding. Use saveQuietly() throughout to stay
        // clear of the observer while wiring everything up.
        $this->sampleFolder = FileSystemObject::factory()->directory()->rootLevel()->create([
            'name' => 'sample-1',
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'study_id' => $this->study->id,
        ]);

        // download_url / has_nmrium are not on Study::$fillable, so we set
        // them via property assignment to mirror production code paths.
        $this->study->fs_id = $this->sampleFolder->id;
        $this->study->download_url = 'https://s3.example.com/nmrxiv/local/archive/abc/study.zip';
        $this->study->has_nmrium = true;
        $this->study->internal_status = 'complete';
        $this->study->is_archived = true;
        $this->study->save();

        // Set the sample-folder tag *after* the observer-firing scaffolding,
        // bypassing the observer so the tag survives to the test mutation.
        $this->sampleFolder->model_type = 'study';
        $this->sampleFolder->is_processed = true;
        $this->sampleFolder->saveQuietly();
        $this->sampleFolder->refresh();

        // Now that the scaffolding is built, fake the bus so we can assert
        // that the reset endpoint does NOT dispatch ArchiveStudy itself —
        // Step 2's pipeline is the source of truth for re-archiving.
        Bus::fake();
    }

    public function test_owner_can_reset_a_sample_folder(): void
    {
        // Build a small descendant subtree carrying instrument tagging that
        // mirrors what FileSystemController::processFolder would have set on
        // a Bruker dataset folder. These are the tags the user reported as
        // "surviving" the refresh — they must be cleared so the next
        // ProcessDraft run re-detects them from scratch.
        $brukerDir = FileSystemObject::factory()->directory()->childLevel(1)->make([
            'parent_id' => $this->sampleFolder->id,
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'study_id' => $this->study->id,
            'instrument_type' => 'bruker',
            'is_processed' => true,
        ]);
        $brukerDir->saveQuietly();

        $acqus = FileSystemObject::factory()->file()->childLevel(2)->make([
            'parent_id' => $brukerDir->id,
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'study_id' => $this->study->id,
            'is_processed' => true,
        ]);
        $acqus->saveQuietly();

        $dataset = Dataset::factory()->create([
            'study_id' => $this->study->id,
            'project_id' => $this->project->id,
            'draft_id' => $this->draft->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
        ]);
        $dataset->has_nmrium = true;
        $dataset->save();

        NMRium::factory()->create([
            'nmriumable_id' => $this->study->id,
            'nmriumable_type' => Study::class,
        ]);
        NMRium::factory()->create([
            'nmriumable_id' => $dataset->id,
            'nmriumable_type' => Dataset::class,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('dashboard.draft.sample-folder.reset', [
                'draft' => $this->draft->id,
                'filesystemobject' => $this->sampleFolder->id,
            ]));

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('study_id', $this->study->id);

        $freshFolder = $this->sampleFolder->fresh();
        $this->assertNull($freshFolder->model_type);
        $this->assertFalse((bool) $freshFolder->is_processed);

        // Descendants must lose instrument_type / is_processed too, otherwise
        // the Bruker / Varian / JCAMP icons survive a page reload and the
        // next ProcessDraft run skips re-detection on already-tagged folders.
        $freshBruker = $brukerDir->fresh();
        $this->assertNull($freshBruker->instrument_type);
        $this->assertNull($freshBruker->model_type);
        $this->assertFalse((bool) $freshBruker->is_processed);
        $this->assertFalse((bool) $acqus->fresh()->is_processed);

        $freshStudy = $this->study->fresh();
        $this->assertNull($freshStudy->download_url);
        $this->assertFalse((bool) $freshStudy->has_nmrium);
        // internal_status must be reset so Upload.vue's polling waits for
        // ArchiveStudy to rebuild the zip before NMRium auto-import runs.
        $this->assertNull($freshStudy->internal_status);
        // is_archived must be flipped back so a previously hidden study
        // becomes visible again once its underlying files change.
        $this->assertFalse((bool) $freshStudy->is_archived);

        $this->assertFalse((bool) $dataset->fresh()->has_nmrium);

        $this->assertSame(0, NMRium::where('nmriumable_type', Study::class)
            ->where('nmriumable_id', $this->study->id)
            ->count());
        $this->assertSame(0, NMRium::where('nmriumable_type', Dataset::class)
            ->where('nmriumable_id', $dataset->id)
            ->count());

        // Reset should NOT dispatch ArchiveStudy — Step 2 owns that work.
        Bus::assertNotDispatched(ArchiveStudy::class);
    }

    public function test_non_owner_is_forbidden(): void
    {
        $intruder = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($intruder)
            ->postJson(route('dashboard.draft.sample-folder.reset', [
                'draft' => $this->draft->id,
                'filesystemobject' => $this->sampleFolder->id,
            ]));

        $response->assertForbidden();

        $freshFolder = $this->sampleFolder->fresh();
        $this->assertSame('study', $freshFolder->model_type);
    }

    public function test_rejects_fso_that_belongs_to_a_different_draft(): void
    {
        $otherDraft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('dashboard.draft.sample-folder.reset', [
                'draft' => $otherDraft->id,
                'filesystemobject' => $this->sampleFolder->id,
            ]));

        $response->assertStatus(403);
        $response->assertJsonPath('ok', false);

        $this->assertSame('study', $this->sampleFolder->fresh()->model_type);
    }

    public function test_rejects_fso_that_is_not_a_sample_folder(): void
    {
        // Build a non-study FSO directly without traversing the observer
        // (which would otherwise also clear the parent sample folder's
        // model_type — that's tested separately in
        // FileSystemObjectArchiveInvalidationTest).
        $childFile = FileSystemObject::factory()->file()->childLevel(1)->make([
            'parent_id' => $this->sampleFolder->id,
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'study_id' => $this->study->id,
        ]);
        $childFile->saveQuietly();

        $response = $this->actingAs($this->user)
            ->postJson(route('dashboard.draft.sample-folder.reset', [
                'draft' => $this->draft->id,
                'filesystemobject' => $childFile->id,
            ]));

        $response->assertStatus(422);
        $response->assertJsonPath('ok', false);
        $response->assertJsonPath('message', 'Only sample folders can be reset.');
    }
}
