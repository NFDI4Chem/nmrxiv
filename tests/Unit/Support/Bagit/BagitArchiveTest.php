<?php

namespace Tests\Unit\Support\Bagit;

use App\Support\Bagit\BagitArchive;
use Illuminate\Support\Facades\Storage;
use Tests\Support\WritesBagitZip;
use Tests\TestCase;

class BagitArchiveTest extends TestCase
{
    use WritesBagitZip;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        config(['filesystems.default_public' => 'local']);
    }

    public function test_archive_key_is_a_zip_directly_under_archive(): void
    {
        $this->assertSame('archive/S1.zip', BagitArchive::archiveKey('S1'));
    }

    public function test_for_study_reads_nmrium_from_the_zip(): void
    {
        $this->putBagZip('S1', [
            'bagit.txt' => "BagIt-Version: 1.0\n",
            'data/Sample1/nmrxiv-meta/S1.nmrium' => '{"data":{"spectra":[]}}',
        ]);

        $archive = BagitArchive::forStudy('S1');

        $this->assertNotNull($archive);
        $this->assertSame('{"data":{"spectra":[]}}', $archive->readNmrium());

        $archive->close();
    }

    public function test_read_image_finds_a_preview_png_inside_the_zip(): void
    {
        $this->putBagZip('S1', [
            'data/Sample1/nmrxiv-meta/S1.nmrium' => '{}',
            'data/Sample1/nmrxiv-meta/images/spec-1.png' => 'fake-png-bytes',
        ]);

        $archive = BagitArchive::forStudy('S1');
        $this->assertSame('fake-png-bytes', $archive->readImage('spec-1'));

        $archive->close();
    }

    public function test_read_image_returns_null_when_zip_entry_is_missing(): void
    {
        $this->putBagZip('S1', ['data/Sample1/nmrxiv-meta/S1.nmrium' => '{}']);

        $archive = BagitArchive::forStudy('S1');
        $this->assertNull($archive->readImage('missing-id'));

        $archive->close();
    }

    public function test_for_study_returns_null_when_the_study_has_no_zip(): void
    {
        $this->assertNull(BagitArchive::forStudy('S404'));
    }

    public function test_for_study_ignores_the_old_per_study_folder_layout(): void
    {
        $this->putBagZip('S2', ['data/Sample1/nmrxiv-meta/S2.nmrium' => '{}']);
        Storage::disk('local')->move('archive/S2.zip', 'archive/S2/S2.zip');
        Storage::disk('local')->put('archive/S3/data/Sample1/nmrxiv-meta/S3.nmrium', '{}');

        $this->assertNull(BagitArchive::forStudy('S2'));
        $this->assertNull(BagitArchive::forStudy('S3'));
    }

    public function test_for_study_returns_null_when_zip_has_no_nmrium_entry(): void
    {
        $this->putBagZip('S1', ['bagit.txt' => "BagIt-Version: 1.0\n"]);

        $this->assertNull(BagitArchive::forStudy('S1'));
        $this->assertEmpty(glob(storage_path('app/bagit_zip_*.zip')));
    }

    public function test_for_study_returns_null_when_the_zip_is_corrupt(): void
    {
        Storage::disk('local')->put('archive/S1.zip', 'this is not a valid zip file');

        $this->assertNull(BagitArchive::forStudy('S1'));
        $this->assertEmpty(glob(storage_path('app/bagit_zip_*.zip')));
    }

    public function test_close_is_idempotent_and_removes_the_downloaded_temp_zip(): void
    {
        $this->putBagZip('S1', ['data/Sample1/nmrxiv-meta/S1.nmrium' => '{}']);

        $before = count(glob(storage_path('app/bagit_zip_*.zip')));

        $archive = BagitArchive::forStudy('S1');
        $during = count(glob(storage_path('app/bagit_zip_*.zip')));

        $archive->close();
        $archive->close();
        $after = count(glob(storage_path('app/bagit_zip_*.zip')));

        $this->assertGreaterThan($before, $during);
        $this->assertSame($before, $after);
    }

    public function test_study_folders_lists_only_study_zips_at_the_archive_root(): void
    {
        $disk = Storage::disk('local');
        $disk->put('archive/S1.zip', 'zip');
        $disk->put('archive/S2.zip', 'zip');
        $disk->put('archive/S3/S3.zip', 'ignored: inside a folder');
        $disk->put('archive/S4/bagit.txt', 'ignored: loose bag');
        $disk->put('archive/notes.txt', 'ignored');

        $this->assertEqualsCanonicalizing(['S1', 'S2'], BagitArchive::studyFolders()->all());
    }
}
