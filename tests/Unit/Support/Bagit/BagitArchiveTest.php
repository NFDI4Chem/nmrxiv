<?php

namespace Tests\Unit\Support\Bagit;

use App\Support\Bagit\BagitArchive;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class BagitArchiveTest extends TestCase
{
    // ------------------------------------------------------------------
    // Loose folder tree
    // ------------------------------------------------------------------

    public function test_open_reads_a_loose_nmrium_file(): void
    {
        Storage::fake('local');
        $disk = Storage::disk('local');

        $disk->put('spectra_parse/S1/data/Sample1/nmrxiv-meta/S1.nmrium', '{"data":{"spectra":[]}}');

        $archive = BagitArchive::open($disk, 'spectra_parse/S1');

        $this->assertNotNull($archive);
        $this->assertSame('{"data":{"spectra":[]}}', $archive->readNmrium());
    }

    public function test_open_returns_null_when_no_nmrium_file_and_no_zip_exist(): void
    {
        Storage::fake('local');
        $disk = Storage::disk('local');

        $disk->put('spectra_parse/S1/bagit.txt', "BagIt-Version: 1.0\n");

        $this->assertNull(BagitArchive::open($disk, 'spectra_parse/S1'));
    }

    public function test_open_returns_null_when_multiple_loose_nmrium_files_are_ambiguous(): void
    {
        Storage::fake('local');
        $disk = Storage::disk('local');

        $disk->put('spectra_parse/S1/data/Sample1/nmrxiv-meta/a.nmrium', '{}');
        $disk->put('spectra_parse/S1/data/Sample1/nmrxiv-meta/b.nmrium', '{}');

        $this->assertNull(BagitArchive::open($disk, 'spectra_parse/S1'));
    }

    public function test_read_image_finds_a_loose_preview_png(): void
    {
        Storage::fake('local');
        $disk = Storage::disk('local');

        $disk->put('spectra_parse/S1/data/Sample1/nmrxiv-meta/S1.nmrium', '{}');
        $disk->put('spectra_parse/S1/data/Sample1/nmrxiv-meta/images/spec-1.png', 'fake-png-bytes');

        $archive = BagitArchive::open($disk, 'spectra_parse/S1');

        $this->assertSame('fake-png-bytes', $archive->readImage('spec-1'));
    }

    public function test_read_image_returns_null_when_loose_preview_png_is_missing(): void
    {
        Storage::fake('local');
        $disk = Storage::disk('local');

        $disk->put('spectra_parse/S1/data/Sample1/nmrxiv-meta/S1.nmrium', '{}');

        $archive = BagitArchive::open($disk, 'spectra_parse/S1');

        $this->assertNull($archive->readImage('missing-id'));
    }

    // ------------------------------------------------------------------
    // Zipped bag (the actual on-disk shape once archived)
    // ------------------------------------------------------------------

    public function test_open_reads_nmrium_from_inside_a_zip(): void
    {
        Storage::fake('local');
        $disk = Storage::disk('local');

        $disk->put('archive/S1/S1.zip', $this->makeBagZip([
            'bagit.txt' => "BagIt-Version: 1.0\n",
            'data/Sample1/nmrxiv-meta/S1.nmrium' => '{"data":{"spectra":[]}}',
        ]));

        $archive = BagitArchive::open($disk, 'archive/S1');

        $this->assertNotNull($archive);
        $this->assertSame('{"data":{"spectra":[]}}', $archive->readNmrium());

        $archive->close();
    }

    public function test_read_image_finds_a_preview_png_inside_the_zip(): void
    {
        Storage::fake('local');
        $disk = Storage::disk('local');

        $disk->put('archive/S1/S1.zip', $this->makeBagZip([
            'data/Sample1/nmrxiv-meta/S1.nmrium' => '{}',
            'data/Sample1/nmrxiv-meta/images/spec-1.png' => 'fake-png-bytes',
        ]));

        $archive = BagitArchive::open($disk, 'archive/S1');
        $this->assertSame('fake-png-bytes', $archive->readImage('spec-1'));

        $archive->close();
    }

    public function test_read_image_returns_null_when_zip_entry_is_missing(): void
    {
        Storage::fake('local');
        $disk = Storage::disk('local');

        $disk->put('archive/S1/S1.zip', $this->makeBagZip([
            'data/Sample1/nmrxiv-meta/S1.nmrium' => '{}',
        ]));

        $archive = BagitArchive::open($disk, 'archive/S1');
        $this->assertNull($archive->readImage('missing-id'));

        $archive->close();
    }

    public function test_open_returns_null_when_zip_has_no_nmrium_entry(): void
    {
        Storage::fake('local');
        $disk = Storage::disk('local');

        $disk->put('archive/S1/S1.zip', $this->makeBagZip([
            'bagit.txt' => "BagIt-Version: 1.0\n",
        ]));

        $this->assertNull(BagitArchive::open($disk, 'archive/S1'));
    }

    public function test_open_returns_null_when_multiple_zip_files_are_ambiguous(): void
    {
        Storage::fake('local');
        $disk = Storage::disk('local');

        $disk->put('archive/S1/S1.zip', $this->makeBagZip(['data/Sample1/nmrxiv-meta/S1.nmrium' => '{}']));
        $disk->put('archive/S1/extra.zip', $this->makeBagZip(['data/Sample1/nmrxiv-meta/S1.nmrium' => '{}']));

        $this->assertNull(BagitArchive::open($disk, 'archive/S1'));
    }

    public function test_open_returns_null_when_the_zip_file_is_corrupt(): void
    {
        Storage::fake('local');
        $disk = Storage::disk('local');

        $disk->put('archive/S1/S1.zip', 'this is not a valid zip file');

        $this->assertNull(BagitArchive::open($disk, 'archive/S1'));
    }

    public function test_loose_file_takes_precedence_over_a_zip_in_the_same_folder(): void
    {
        Storage::fake('local');
        $disk = Storage::disk('local');

        $disk->put('spectra_parse/S1/data/Sample1/nmrxiv-meta/S1.nmrium', '{"source":"loose"}');
        $disk->put('spectra_parse/S1/S1.zip', $this->makeBagZip([
            'data/Sample1/nmrxiv-meta/S1.nmrium' => '{"source":"zip"}',
        ]));

        $archive = BagitArchive::open($disk, 'spectra_parse/S1');

        $this->assertSame('{"source":"loose"}', $archive->readNmrium());
    }

    public function test_close_is_safe_to_call_on_a_loose_archive_and_idempotent(): void
    {
        Storage::fake('local');
        $disk = Storage::disk('local');

        $disk->put('spectra_parse/S1/data/Sample1/nmrxiv-meta/S1.nmrium', '{}');

        $archive = BagitArchive::open($disk, 'spectra_parse/S1');
        $archive->close();
        $archive->close(); // no error calling twice

        $this->assertNotNull($archive->readNmrium());
    }

    public function test_close_removes_the_downloaded_temp_zip_file(): void
    {
        Storage::fake('local');
        $disk = Storage::disk('local');

        $disk->put('archive/S1/S1.zip', $this->makeBagZip([
            'data/Sample1/nmrxiv-meta/S1.nmrium' => '{}',
        ]));

        $before = collect(glob(storage_path('app/bagit_zip_*.zip')));

        $archive = BagitArchive::open($disk, 'archive/S1');
        $duringCount = count(glob(storage_path('app/bagit_zip_*.zip')));

        $archive->close();
        $afterCount = count(glob(storage_path('app/bagit_zip_*.zip')));

        $this->assertGreaterThan($before->count(), $duringCount);
        $this->assertSame($before->count(), $afterCount);
    }

    /**
     * Build a bag zip's raw bytes from a flat [path => contents] map.
     *
     * @param  array<string, string>  $entries
     */
    private function makeBagZip(array $entries): string
    {
        $localPath = storage_path('app/bagit_test_fixture_'.uniqid().'.zip');

        $zip = new ZipArchive;
        $zip->open($localPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach ($entries as $path => $contents) {
            $zip->addFromString($path, $contents);
        }
        $zip->close();

        $bytes = file_get_contents($localPath);
        @unlink($localPath);

        return $bytes;
    }
}
