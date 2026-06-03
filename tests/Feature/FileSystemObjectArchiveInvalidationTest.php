<?php

namespace Tests\Feature;

use App\Jobs\ArchiveStudy;
use App\Models\Dataset;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use App\Services\FileSystemObjectService;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileSystemObjectArchiveInvalidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Pin the cache to the in-process `array` driver. `config/cache.php`
        // resolves the default driver from `env('CACHE_DRIVER')`, which in
        // the developer's .env may point at Redis — phpunit.xml's CACHE_STORE
        // override is silently ignored. Without an array driver here,
        // ShouldBeUnique's lock check for ArchiveStudy hits a real (or
        // unavailable) Redis and the dispatch is silently dropped, which
        // makes the assertions below misleadingly fail.
        Config::set('cache.default', 'array');
        $manager = Cache::getFacadeRoot();
        if (method_exists($manager, 'forgetDriver')) {
            $manager->forgetDriver();
        }
        // `cache.store` and the Repository contract are singletons resolved
        // from the previous (Redis) driver. Forget them so the next
        // resolution rebuilds against the array driver above.
        app()->forgetInstance('cache.store');
        app()->forgetInstance(Repository::class);
        $store = Cache::store('array')->getStore();
        if (property_exists($store, 'locks')) {
            $store->locks = [];
        }

        Bus::fake();
    }

    private function makeStudyWithFso(?string $downloadUrl = 'https://s3.example.com/nmrxiv/local/archive/abc/study.zip', bool $hasNmrium = true): array
    {
        $user = User::factory()->withPersonalTeam()->create();
        $draft = Draft::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
        ]);
        $project = Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
        ]);
        // Create the study before any download_url / has_nmrium is set so the
        // FSO `created` event below cannot trigger the invalidation observer
        // — we want a clean slate before the test's actual mutation.
        $study = Study::factory()->create([
            'project_id' => $project->id,
            'draft_id' => $draft->id,
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'download_url' => null,
            'has_nmrium' => false,
        ]);

        $fso = FileSystemObject::factory()->directory()->create([
            'study_id' => $study->id,
            'draft_id' => $draft->id,
            'project_id' => $project->id,
        ]);

        // Use direct property writes for download_url / has_nmrium because
        // Study::$fillable intentionally excludes them; mass-assignment via
        // update() would be silently dropped by Laravel.
        $study->fs_id = $fso->id;
        $study->download_url = $downloadUrl;
        $study->has_nmrium = $hasNmrium;
        $study->save();

        // Discard any jobs the observer dispatched while this scaffolding
        // was being built — each test only cares about the dispatches its
        // own mutation triggers. The cache lock acquired by the
        // ShouldBeUniqueUntilProcessing dispatcher is reset alongside the
        // BusFake so the next dispatch can re-acquire.
        Bus::fake();
        $store = Cache::store('array')->getStore();
        if (property_exists($store, 'locks')) {
            $store->locks = [];
        }

        return [$study->fresh(), $fso];
    }

    public function test_updating_structural_field_clears_download_url(): void
    {
        [$study, $fso] = $this->makeStudyWithFso();

        $fso->name = 'renamed-folder';
        $fso->save();

        $fresh = $study->fresh();
        $this->assertNull($fresh->download_url);
        $this->assertFalse((bool) $fresh->has_nmrium);
        Bus::assertDispatched(ArchiveStudy::class);
    }

    public function test_updating_bookkeeping_field_does_not_clear_download_url(): void
    {
        [$study, $fso] = $this->makeStudyWithFso();
        $originalUrl = $study->download_url;

        $fso->is_processed = true;
        $fso->save();

        $this->assertSame($originalUrl, $study->fresh()->download_url);
        Bus::assertNotDispatched(ArchiveStudy::class);
    }

    public function test_creating_a_child_under_a_study_clears_download_url(): void
    {
        [$study, $fso] = $this->makeStudyWithFso();

        FileSystemObject::factory()->file()->create([
            'parent_id' => $fso->id,
            'study_id' => $study->id,
            'draft_id' => $fso->draft_id,
            'project_id' => $fso->project_id,
        ]);

        $fresh = $study->fresh();
        $this->assertNull($fresh->download_url);
        $this->assertFalse((bool) $fresh->has_nmrium);
        Bus::assertDispatched(ArchiveStudy::class);
    }

    public function test_deleting_an_fso_clears_download_url(): void
    {
        [$study, $fso] = $this->makeStudyWithFso();

        $child = FileSystemObject::factory()->file()->create([
            'parent_id' => $fso->id,
            'study_id' => $study->id,
            'draft_id' => $fso->draft_id,
            'project_id' => $fso->project_id,
        ]);
        $study->update(['download_url' => 'https://s3.example.com/nmrxiv/local/archive/xyz/study.zip']);

        $child->delete();

        $this->assertNull($study->fresh()->download_url);
        Bus::assertDispatched(ArchiveStudy::class);
    }

    public function test_deleting_an_fso_resets_full_study_state(): void
    {
        // Deletion must trigger the same Study-side reset as the right-click
        // "Refresh" action: download_url, has_nmrium, internal_status, plus
        // dataset has_nmrium and any persisted NMRium polymorphic rows.
        // Without this, Upload.vue's polling loop sees a stale 'complete'
        // marker and runs autoImport before ArchiveStudy has rebuilt the zip.
        [$study, $fso] = $this->makeStudyWithFso();
        $study->internal_status = 'complete';
        $study->save();

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'project_id' => $fso->project_id,
            'draft_id' => $fso->draft_id,
            'team_id' => $study->team_id,
            'owner_id' => $study->owner_id,
        ]);
        $dataset->has_nmrium = true;
        $dataset->save();

        NMRium::factory()->create([
            'nmriumable_id' => $study->id,
            'nmriumable_type' => Study::class,
        ]);
        NMRium::factory()->create([
            'nmriumable_id' => $dataset->id,
            'nmriumable_type' => Dataset::class,
        ]);

        $child = FileSystemObject::factory()->file()->create([
            'parent_id' => $fso->id,
            'study_id' => $study->id,
            'draft_id' => $fso->draft_id,
            'project_id' => $fso->project_id,
        ]);

        $child->delete();

        $fresh = $study->fresh();
        $this->assertNull($fresh->download_url);
        $this->assertFalse((bool) $fresh->has_nmrium);
        $this->assertNull($fresh->internal_status);

        $this->assertFalse((bool) $dataset->fresh()->has_nmrium);
        $this->assertSame(0, NMRium::where('nmriumable_type', Study::class)
            ->where('nmriumable_id', $study->id)
            ->count());
        $this->assertSame(0, NMRium::where('nmriumable_type', Dataset::class)
            ->where('nmriumable_id', $dataset->id)
            ->count());

        Bus::assertDispatched(ArchiveStudy::class);
    }

    public function test_invalidates_when_download_url_already_null_but_nmrium_was_imported(): void
    {
        [$study, $fso] = $this->makeStudyWithFso(downloadUrl: null, hasNmrium: true);

        $fso->name = 'renamed-folder';
        $fso->save();

        $fresh = $study->fresh();
        $this->assertFalse((bool) $fresh->has_nmrium);
        Bus::assertDispatched(ArchiveStudy::class);
    }

    public function test_dispatches_archive_rebuild_even_when_no_prior_archive(): void
    {
        // Even if a study has never been archived yet, a structural change
        // must enqueue ArchiveStudy. Otherwise, a rapid sequence of uploads
        // (where an in-flight build has already nulled `download_url`) would
        // leave the second batch of files unarchived because we'd skip the
        // dispatch on the "already invalidated" follow-up.
        [$study, $fso] = $this->makeStudyWithFso(downloadUrl: null, hasNmrium: false);

        $fso->name = 'renamed-folder';
        $fso->save();

        Bus::assertDispatched(ArchiveStudy::class);
    }

    public function test_invalidation_clears_model_type_on_sample_folder_fso(): void
    {
        // The sample-folder FSO is the one that anchors the study
        // (Study::fs_id). When files change underneath it, processFolder
        // must re-walk the tree and re-tag — but it skips folders that still
        // have a `model_type` set, so the observer needs to clear it.
        [$study, $fso] = $this->makeStudyWithFso();
        $fso->model_type = 'study';
        $fso->is_processed = true;
        $fso->saveQuietly();

        FileSystemObject::factory()->file()->create([
            'parent_id' => $fso->id,
            'study_id' => $study->id,
            'draft_id' => $fso->draft_id,
            'project_id' => $fso->project_id,
        ]);

        $freshFso = $fso->fresh();
        $this->assertNull($freshFso->model_type);
        $this->assertFalse((bool) $freshFso->is_processed);
    }

    public function test_bulk_delete_through_service_resets_full_study_state(): void
    {
        // FileSystemObjectService::deleteFileSystemObject does a mass
        // `Builder::delete()` which Eloquent intentionally skips model
        // events for. Without an explicit invalidation hook in the service,
        // user-driven deletions (the actual production code path triggered
        // from Upload.vue's trash icon) would silently leave the study
        // with a stale download_url / has_nmrium / internal_status and
        // the autoImport flow would race ArchiveStudy on the next "Proceed
        // to Step 2".
        [$study, $fso] = $this->makeStudyWithFso();
        $study->internal_status = 'complete';
        $study->save();

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'project_id' => $fso->project_id,
            'draft_id' => $fso->draft_id,
            'team_id' => $study->team_id,
            'owner_id' => $study->owner_id,
        ]);
        $dataset->has_nmrium = true;
        $dataset->save();

        NMRium::factory()->create([
            'nmriumable_id' => $study->id,
            'nmriumable_type' => Study::class,
        ]);

        $child = FileSystemObject::factory()->file()->create([
            'parent_id' => $fso->id,
            'study_id' => $study->id,
            'draft_id' => $fso->draft_id,
            'project_id' => $fso->project_id,
        ]);

        // Discard observer dispatches from the per-row factory create above
        // — the test target is the mass-delete path that follows. Clearing
        // the array-cache locks alongside Bus::fake() is required because
        // ArchiveStudy is ShouldBeUniqueUntilProcessing; without resetting
        // the cache lock, the next dispatch is silently no-op'd by the
        // unique-job middleware.
        Bus::fake();
        $store = Cache::store('array')->getStore();
        if (property_exists($store, 'locks')) {
            $store->locks = [];
        }

        app(FileSystemObjectService::class)->deleteFileSystemObject($child);

        $this->assertDatabaseMissing('file_system_objects', ['id' => $child->id]);

        $fresh = $study->fresh();
        $this->assertNull($fresh->download_url);
        $this->assertFalse((bool) $fresh->has_nmrium);
        $this->assertNull($fresh->internal_status);
        $this->assertFalse((bool) $dataset->fresh()->has_nmrium);
        $this->assertSame(0, NMRium::where('nmriumable_type', Study::class)
            ->where('nmriumable_id', $study->id)
            ->count());

        Bus::assertDispatched(ArchiveStudy::class);
    }

    public function test_attempts_to_delete_zip_from_configured_disk(): void
    {
        Storage::fake(config('filesystems.default'));

        $disk = Storage::disk(config('filesystems.default'));
        $bucket = config('filesystems.disks.'.config('filesystems.default').'.bucket') ?? 'nmrxiv';
        $key = 'local/archive/abc/study.zip';
        $disk->put($key, 'fake-zip-bytes');

        [$study, $fso] = $this->makeStudyWithFso('https://s3.example.com/'.$bucket.'/'.$key);

        $fso->name = 'renamed';
        $fso->save();

        $this->assertFalse($disk->exists($key));
        $this->assertNull($study->fresh()->download_url);
        Bus::assertDispatched(ArchiveStudy::class);
    }
}
