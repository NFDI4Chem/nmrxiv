<?php

namespace Tests\Unit\Services;

use App\Exceptions\ZipArchiveException;
use App\Services\ZipArchiveExtractor;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use ZipArchive;

class ZipArchiveExtractorTest extends TestCase
{
    private ZipArchiveExtractor $extractor;

    /** @var list<string> */
    private array $tempFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractor = new ZipArchiveExtractor;
    }

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        parent::tearDown();
    }

    public function test_inspect_returns_nested_file_entries_and_skips_directories(): void
    {
        $zip = $this->openZip([
            'sample1/' => null,
            'sample1/1/fid' => 'fid-data',
            'sample1/1/acqus' => 'acqus-data',
            'sample1/notes.txt' => 'notes',
        ]);

        $entries = $this->extractor->inspect($zip);

        $this->assertSame(
            ['sample1/1/fid', 'sample1/1/acqus', 'sample1/notes.txt'],
            array_column($entries, 'relativePath')
        );
        $this->assertSame('fid', $entries[0]['filename']);
        $this->assertSame(strlen('fid-data'), $entries[0]['size']);
        $this->assertFalse($this->extractor->hasRootLevelFiles($entries));
    }

    public function test_inspect_skips_os_metadata_files(): void
    {
        $zip = $this->openZip([
            'data/fid' => 'fid',
            '__MACOSX/data/._fid' => 'resource-fork',
            'data/.DS_Store' => 'finder',
            'Thumbs.db' => 'thumbs',
        ]);

        $entries = $this->extractor->inspect($zip);

        $this->assertSame(['data/fid'], array_column($entries, 'relativePath'));
    }

    public function test_inspect_rejects_parent_directory_traversal(): void
    {
        $zip = $this->openZip(['data/../../evil.txt' => 'evil']);

        $this->expectException(ZipArchiveException::class);
        $this->expectExceptionMessage('outside its root');

        $this->extractor->inspect($zip);
    }

    public function test_inspect_rejects_absolute_paths(): void
    {
        $zip = $this->openZip(['C:/Windows/evil.txt' => 'evil']);

        $this->expectException(ZipArchiveException::class);
        $this->expectExceptionMessage('absolute path');

        $this->extractor->inspect($zip);
    }

    public function test_inspect_rejects_symbolic_links(): void
    {
        $path = $this->createZip(['link' => '/etc/passwd']);
        $zip = new ZipArchive;
        $zip->open($path);
        $zip->setExternalAttributesName('link', ZipArchive::OPSYS_UNIX, 0120777 << 16);
        $zip->close();

        $this->expectException(ZipArchiveException::class);
        $this->expectExceptionMessage('symbolic link');

        $this->extractor->inspect($this->extractor->open($path));
    }

    public function test_inspect_rejects_archives_with_too_many_entries(): void
    {
        Config::set('nmrxiv.zip_extraction.max_entries', 2);

        $zip = $this->openZip(['a' => 'a', 'b' => 'b', 'c' => 'c']);

        $this->expectException(ZipArchiveException::class);
        $this->expectExceptionMessage('the limit is 2');

        $this->extractor->inspect($zip);
    }

    public function test_inspect_rejects_archives_exceeding_the_uncompressed_size_limit(): void
    {
        Config::set('nmrxiv.zip_extraction.max_uncompressed_bytes', 10);

        $zip = $this->openZip(['big.txt' => str_repeat('x', 11)]);

        $this->expectException(ZipArchiveException::class);
        $this->expectExceptionMessage('too large');

        $this->extractor->inspect($zip);
    }

    public function test_inspect_rejects_suspicious_compression_ratios(): void
    {
        Config::set('nmrxiv.zip_extraction.max_compression_ratio', 10);

        $zip = $this->openZip(['zeros.bin' => str_repeat("\0", 11 * 1024 * 1024)]);

        $this->expectException(ZipArchiveException::class);
        $this->expectExceptionMessage('compression ratio');

        $this->extractor->inspect($zip);
    }

    public function test_compression_ratio_is_not_enforced_for_small_archives(): void
    {
        Config::set('nmrxiv.zip_extraction.max_compression_ratio', 10);

        $zip = $this->openZip(['zeros.bin' => str_repeat("\0", 1024 * 1024)]);

        $this->assertCount(1, $this->extractor->inspect($zip));
    }

    public function test_open_rejects_files_that_are_not_zip_archives(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'not_zip_');
        $this->tempFiles[] = $path;
        file_put_contents($path, 'plain text');

        $this->expectException(ZipArchiveException::class);

        $this->extractor->open($path);
    }

    public function test_stream_returns_a_seekable_copy_of_the_entry(): void
    {
        $zip = $this->openZip(['data/fid' => 'fid-contents']);
        $entries = $this->extractor->inspect($zip);

        $stream = $this->extractor->stream($zip, $entries[0]);

        $this->assertTrue(stream_get_meta_data($stream)['seekable']);
        $this->assertSame('fid-contents', stream_get_contents($stream));
        rewind($stream);
        $this->assertSame('fid-contents', stream_get_contents($stream));
        fclose($stream);
    }

    public function test_stream_rejects_corrupt_entries(): void
    {
        $contents = str_repeat('NMRDATA-', 512);
        $path = $this->createZip(['data/fid' => $contents], ZipArchive::CM_STORE);

        $bytes = file_get_contents($path);
        $offset = strpos($bytes, $contents);
        $bytes[$offset] = 'X';
        file_put_contents($path, $bytes);

        $zip = $this->extractor->open($path);
        $entries = $this->extractor->inspect($zip);

        $this->expectException(ZipArchiveException::class);
        $this->expectExceptionMessage('corrupt');

        $this->extractor->stream($zip, $entries[0]);
    }

    public function test_wrapper_folders_around_sample_folders_are_detected(): void
    {
        $this->assertSame('zu kontrollieren/', $this->extractor->wrapperFolderPrefix([
            ['relativePath' => 'zu kontrollieren/sample_a/1/acqus', 'filename' => 'acqus'],
            ['relativePath' => 'zu kontrollieren/sample_a/1/pdata/1/1r', 'filename' => '1r'],
            ['relativePath' => 'zu kontrollieren/sample_b/2/fid', 'filename' => 'fid'],
        ]));

        $this->assertSame('outer/inner/', $this->extractor->wrapperFolderPrefix([
            ['relativePath' => 'outer/inner/sample_a/1/ser', 'filename' => 'ser'],
        ]));
    }

    public function test_a_lone_sample_folder_or_non_bruker_data_is_not_treated_as_a_wrapper(): void
    {
        $this->assertSame('', $this->extractor->wrapperFolderPrefix([
            ['relativePath' => 'benzene/1/acqus', 'filename' => 'acqus'],
            ['relativePath' => 'benzene/2/pdata/1/1r', 'filename' => '1r'],
        ]));

        $this->assertSame('', $this->extractor->wrapperFolderPrefix([
            ['relativePath' => 'wrapper/sample_a/spectra/data.jdx', 'filename' => 'data.jdx'],
        ]));

        $this->assertSame('', $this->extractor->wrapperFolderPrefix([
            ['relativePath' => 'sample_a/1/acqus', 'filename' => 'acqus'],
            ['relativePath' => 'sample_b/1/acqus', 'filename' => 'acqus'],
        ]));

        $this->assertSame('', $this->extractor->wrapperFolderPrefix([
            ['relativePath' => 'wrapper/sample_a/1/acqus', 'filename' => 'acqus'],
            ['relativePath' => 'wrapper/readme.txt', 'filename' => 'readme.txt'],
        ]));
    }

    public function test_has_root_level_files_only_when_a_file_sits_outside_every_folder(): void
    {
        $this->assertTrue($this->extractor->hasRootLevelFiles([
            ['relativePath' => 'fid'],
            ['relativePath' => 'pdata/1/1r'],
        ]));

        $this->assertFalse($this->extractor->hasRootLevelFiles([
            ['relativePath' => 'sample1/fid'],
            ['relativePath' => 'sample2/fid'],
        ]));
    }

    /**
     * @param  array<string, string|null>  $files  Entry name => contents; null adds a directory
     */
    private function openZip(array $files): ZipArchive
    {
        return $this->extractor->open($this->createZip($files));
    }

    /**
     * @param  array<string, string|null>  $files  Entry name => contents; null adds a directory
     */
    private function createZip(array $files, ?int $compressionMethod = null): string
    {
        $path = tempnam(sys_get_temp_dir(), 'zip_test_');
        $this->tempFiles[] = $path;

        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::OVERWRITE);

        foreach ($files as $name => $contents) {
            if ($contents === null) {
                $zip->addEmptyDir($name);

                continue;
            }

            $zip->addFromString($name, $contents);

            if ($compressionMethod !== null) {
                $zip->setCompressionName($name, $compressionMethod);
            }
        }

        $zip->close();

        return $path;
    }
}
