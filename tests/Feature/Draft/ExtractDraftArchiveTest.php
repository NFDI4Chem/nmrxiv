<?php

namespace Tests\Feature\Draft;

use App\Exceptions\ZipArchiveException;
use App\Jobs\ExtractDraftArchive;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\User;
use App\Services\FileSystemObjectService;
use App\Services\ZipArchiveExtractor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;
use Throwable;
use ZipArchive;

class ExtractDraftArchiveTest extends TestCase
{
    use RefreshDatabase;

    private Draft $draft;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('filesystems.default'));

        $user = User::factory()->withPersonalTeam()->create();

        [$userId, $teamId] = $user->getUserTeamData();

        $this->draft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
        ]);
    }

    public function test_archive_with_a_single_top_level_folder_is_extracted_next_to_the_zip(): void
    {
        $archive = $this->uploadArchive('upload.zip', [
            'sample1/1/fid' => 'fid-data',
            'sample1/1/pdata/1/1r' => '1r-data',
        ]);

        ExtractDraftArchive::dispatchSync($this->draft->id, $archive->id);

        $sample = $this->findByRelativeUrl('/sample1');
        $experiment = $this->findByRelativeUrl('/sample1/1');
        $fid = $this->findByRelativeUrl('/sample1/1/fid');
        $processed = $this->findByRelativeUrl('/sample1/1/pdata/1/1r');

        $this->assertSame('directory', $sample->type);
        $this->assertNull($sample->parent_id);
        $this->assertSame(0, (int) $sample->level);
        $this->assertSame(1, (int) $sample->is_root);

        $this->assertSame($sample->id, $experiment->parent_id);
        $this->assertSame(1, (int) $experiment->level);

        $this->assertSame('file', $fid->type);
        $this->assertSame($experiment->id, $fid->parent_id);
        $this->assertSame(2, (int) $fid->level);
        $this->assertSame(0, (int) $fid->is_root);
        $this->assertSame('/'.$this->draft->path.'/sample1/1/fid', $fid->path);
        $this->assertSame(strlen('fid-data'), json_decode($fid->info, true)['size']);

        Storage::assertExists(ltrim($fid->path, '/'));
        Storage::assertExists(ltrim($processed->path, '/'));
        $this->assertSame('fid-data', Storage::get(ltrim($fid->path, '/')));

        $this->assertNull(FileSystemObject::find($archive->id));
        Storage::assertMissing(ltrim($archive->path, '/'));
    }

    public function test_archive_with_several_top_level_folders_extracts_each_as_a_root_sample(): void
    {
        $archive = $this->uploadArchive('project-samples.zip', [
            'serin/1/fid' => 'serin-fid',
            'sorbic_acid/1/fid' => 'sorbic-fid',
        ]);

        ExtractDraftArchive::dispatchSync($this->draft->id, $archive->id);

        foreach (['/serin', '/sorbic_acid'] as $sampleUrl) {
            $sample = $this->findByRelativeUrl($sampleUrl);
            $this->assertNull($sample->parent_id);
            $this->assertSame(0, (int) $sample->level);
            $this->assertSame(1, (int) $sample->is_root);
        }

        $this->assertNull($this->findByRelativeUrl('/project-samples', required: false));
        $this->assertSame('serin-fid', Storage::get($this->draft->path.'/serin/1/fid'));
    }

    public function test_lone_wrapper_folder_around_sample_folders_is_dropped(): void
    {
        $archive = $this->uploadArchive('sample1.zip', [
            'zu kontrollieren/3-Pentanon/1/acqus' => 'acqus-data',
            'zu kontrollieren/3-Pentanon/1/fid' => 'fid-data',
            'zu kontrollieren/o-xylol/2/fid' => 'xylol-fid',
        ]);

        ExtractDraftArchive::dispatchSync($this->draft->id, $archive->id);

        foreach (['/3-Pentanon', '/o-xylol'] as $sampleUrl) {
            $sample = $this->findByRelativeUrl($sampleUrl);
            $this->assertNull($sample->parent_id);
            $this->assertSame(0, (int) $sample->level);
        }

        $this->assertNull($this->findByRelativeUrl('/zu kontrollieren', required: false));
        $this->assertSame('xylol-fid', Storage::get($this->draft->path.'/o-xylol/2/fid'));
    }

    public function test_archive_with_files_at_its_root_is_extracted_into_a_folder_named_after_it(): void
    {
        $archive = $this->uploadArchive('samples/benzene.zip', [
            'fid' => 'fid-data',
            'pdata/1/1r' => '1r-data',
        ]);

        ExtractDraftArchive::dispatchSync($this->draft->id, $archive->id);

        $folder = $this->findByRelativeUrl('/samples/benzene');
        $fid = $this->findByRelativeUrl('/samples/benzene/fid');

        $this->assertSame('directory', $folder->type);
        $this->assertSame($this->findByRelativeUrl('/samples')->id, $folder->parent_id);
        $this->assertSame($folder->id, $fid->parent_id);
        $this->assertSame('fid-data', Storage::get(ltrim($fid->path, '/')));
        $this->assertNotNull($this->findByRelativeUrl('/samples/benzene/pdata/1/1r'));
        $this->assertNull(FileSystemObject::find($archive->id));
    }

    public function test_rejected_archive_is_kept_with_the_error_and_nothing_is_extracted(): void
    {
        Config::set('nmrxiv.zip_extraction.max_entries', 1);

        $archive = $this->uploadArchive('too-many.zip', [
            'data/a' => 'a',
            'data/b' => 'b',
        ]);

        ExtractDraftArchive::dispatchSync($this->draft->id, $archive->id);

        $archive->refresh();

        $this->assertSame(FileSystemObject::EXTRACTION_FAILED, $archive->extraction()['status']);
        $this->assertStringContainsString('the limit is 1', $archive->extraction()['error']);
        Storage::assertExists(ltrim($archive->path, '/'));
        $this->assertSame(
            1,
            FileSystemObject::query()->where('draft_id', $this->draft->id)->count()
        );
    }

    public function test_partially_extracted_files_are_removed_when_extraction_fails(): void
    {
        $existing = $this->createDraftFile('data/existing.txt');

        $archive = $this->uploadArchive('broken.zip', [
            'data/existing.txt' => 'replacement',
            'data/new/first.txt' => 'first',
            'data/new/second.txt' => 'second',
        ]);
        $this->failStreamingEntry('data/new/second.txt', new ZipArchiveException('Failed to read archive entry.'));

        ExtractDraftArchive::dispatchSync($this->draft->id, $archive->id);

        $archive->refresh();

        $this->assertSame(FileSystemObject::EXTRACTION_FAILED, $archive->extraction()['status']);
        $this->assertSame('Failed to read archive entry.', $archive->extraction()['error']);
        $this->assertArrayNotHasKey('total', $archive->extraction());
        $this->assertNotNull(FileSystemObject::find($existing->id));
        $this->assertNull($this->findByRelativeUrl('/data/new/first.txt', required: false));
        $this->assertNull($this->findByRelativeUrl('/data/new/second.txt', required: false));
        $this->assertNull($this->findByRelativeUrl('/data/new', required: false));
        Storage::assertMissing($this->draft->path.'/data/new/first.txt');
    }

    public function test_unexpected_errors_mark_the_archive_failed_with_a_generic_message_and_are_rethrown(): void
    {
        $archive = $this->uploadArchive('upload.zip', ['sample1/fid' => 'fid']);
        $this->failStreamingEntry('sample1/fid', new RuntimeException('Disk exploded'));

        try {
            ExtractDraftArchive::dispatchSync($this->draft->id, $archive->id);
            $this->fail('Expected the unexpected error to be rethrown.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Disk exploded', $exception->getMessage());
        }

        $archive->refresh();

        $this->assertSame(FileSystemObject::EXTRACTION_FAILED, $archive->extraction()['status']);
        $this->assertSame('The archive could not be extracted.', $archive->extraction()['error']);
        $this->assertNull($this->findByRelativeUrl('/sample1', required: false));
    }

    public function test_job_does_nothing_for_a_file_in_another_draft(): void
    {
        $archive = $this->uploadArchive('upload.zip', ['sample1/fid' => 'fid']);
        $otherDraft = Draft::factory()->create();

        ExtractDraftArchive::dispatchSync($otherDraft->id, $archive->id);

        $this->assertNull($archive->fresh()->extraction());
        $this->assertNull($this->findByRelativeUrl('/sample1', required: false));
    }

    /**
     * @param  array<string, string>  $files
     */
    private function uploadArchive(string $relativePath, array $files): FileSystemObject
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'draft_archive_test_');

        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::OVERWRITE);
        foreach ($files as $name => $contents) {
            $zip->addFromString($name, $contents);
            $zip->setCompressionName($name, ZipArchive::CM_STORE);
        }
        $zip->close();

        $archive = $this->createDraftFile($relativePath, filesize($zipPath));
        Storage::put(ltrim($archive->path, '/'), file_get_contents($zipPath));
        unlink($zipPath);

        return $archive;
    }

    private function failStreamingEntry(string $relativePath, Throwable $exception): void
    {
        $this->app->instance(ZipArchiveExtractor::class, new class($relativePath, $exception) extends ZipArchiveExtractor
        {
            public function __construct(private string $failingPath, private Throwable $failure) {}

            public function stream(ZipArchive $zip, array $entry)
            {
                if ($entry['relativePath'] === $this->failingPath) {
                    throw $this->failure;
                }

                return parent::stream($zip, $entry);
            }
        });
    }

    private function createDraftFile(string $relativePath, int $size = 10): FileSystemObject
    {
        $path = app(FileSystemObjectService::class)->createDraftFileSystemObject($this->draft, [
            'upload' => ['filename' => basename($relativePath), 'total' => $size],
            'fullPath' => $relativePath,
        ], '/');

        return FileSystemObject::query()
            ->where('draft_id', $this->draft->id)
            ->where('path', $path)
            ->firstOrFail();
    }

    private function findByRelativeUrl(string $relativeUrl, bool $required = true): ?FileSystemObject
    {
        $query = FileSystemObject::query()
            ->where('draft_id', $this->draft->id)
            ->where('relative_url', $relativeUrl);

        return $required ? $query->firstOrFail() : $query->first();
    }
}
