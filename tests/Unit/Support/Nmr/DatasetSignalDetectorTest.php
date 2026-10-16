<?php

namespace Tests\Unit\Support\Nmr;

use App\Models\Dataset;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use App\Support\Nmr\DatasetSignalDetector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DatasetSignalDetectorTest extends TestCase
{
    use RefreshDatabase;

    private const ARCHIVE_URL = 'https://s3.test/nmrxiv/local/archive/abc/sample.zip';

    private const PARSER_URL = 'https://nmrkit.test/latest/spectra/parse/url';

    private Study $study;

    protected function setUp(): void
    {
        parent::setUp();

        config(['nmrxiv.spectra_parsing.nmrkit_api_url' => self::PARSER_URL]);

        $project = Project::factory()->create();
        $this->study = Study::factory()->for($project)->create([
            'is_public' => true,
            'download_url' => self::ARCHIVE_URL,
        ]);
        FileSystemObject::factory()->asStudyRoot($this->study)->create([
            'name' => 'sample',
            'key' => 'sample',
            'relative_url' => '/sample',
        ]);
    }

    public function test_stores_detected_1d_ranges_on_matching_datasets(): void
    {
        $protonDataset = $this->createDataset('/sample/1', 'directory');
        $carbonDataset = $this->createDataset('/sample/sample-13c.jdx', 'file');
        $hsqcDataset = $this->createDataset('/sample/5', 'directory');
        $untouchedDataset = $this->createDataset('/sample/9', 'directory');

        Http::fake([self::PARSER_URL => Http::response(['nmriumState' => ['data' => ['spectra' => [
            $this->spectrum(1, '1H', '1d', 'sample/1/pdata/1/1r', ranges: [['from' => 7.4, 'to' => 7.5]]),
            $this->spectrum(1, '13C', '1d', 'sample/sample-13c.jdx', ranges: [['from' => 128.2, 'to' => 128.3]]),
            $this->spectrum(2, ['1H', '13C'], 'hsqc', 'sample/5/acqu2s', zones: [['id' => 'z1']]),
            $this->spectrum(1, '1H', '1d', 'unrelated/2/fid', ranges: [['from' => 1.0, 'to' => 1.1]]),
        ]]]])]);

        $updated = app(DatasetSignalDetector::class)->detect($this->study);

        $this->assertSame(3, $updated);

        $proton = $protonDataset->fresh()->auto_detected_signals;
        $this->assertSame(self::PARSER_URL, $proton['source']);
        $this->assertNotNull($proton['detected_at']);
        $this->assertCount(1, $proton['spectra']);
        $this->assertSame('1H', $proton['spectra'][0]['nucleus']);
        $this->assertSame('sample/1/pdata/1/1r', $proton['spectra'][0]['file']);
        $this->assertSame([['from' => 7.4, 'to' => 7.5]], $proton['spectra'][0]['ranges']);

        $carbon = $carbonDataset->fresh()->auto_detected_signals;
        $this->assertSame([['from' => 128.2, 'to' => 128.3]], $carbon['spectra'][0]['ranges']);

        $this->assertSame([], $hsqcDataset->fresh()->auto_detected_signals['spectra']);
        $this->assertNull($untouchedDataset->fresh()->auto_detected_signals);

        $this->assertEqualsWithDelta(7.45, $protonDataset->signals()->sole()->shift, 0.0001);
        $this->assertEqualsWithDelta(128.25, $carbonDataset->signals()->sole()->shift, 0.0001);
        $this->assertSame(0, $hsqcDataset->signals()->count());

        Http::assertSent(fn (Request $request): bool => $request->url() === self::PARSER_URL
            && $request['url'] === self::ARCHIVE_URL
            && $request['auto_processing'] === true
            && $request['auto_detection'] === true);
    }

    public function test_leaves_nmrium_info_untouched_and_skips_audit_trail(): void
    {
        $dataset = $this->createDataset('/sample/1', 'directory');
        $auditCountBefore = $dataset->audits()->count();

        Http::fake([self::PARSER_URL => Http::response(['nmriumState' => ['data' => ['spectra' => [
            $this->spectrum(1, '1H', '1d', 'sample/1/fid', ranges: [['from' => 7.4, 'to' => 7.5]]),
        ]]]])]);

        app(DatasetSignalDetector::class)->detect($this->study);

        $this->assertNull($dataset->fresh()->nmrium);
        $this->assertSame($auditCountBefore, $dataset->audits()->count());
    }

    public function test_throws_when_nmrkit_fails(): void
    {
        $dataset = $this->createDataset('/sample/1', 'directory');
        Http::fake([self::PARSER_URL => Http::response('Gateway Timeout', 504)]);

        $this->expectException(RequestException::class);

        try {
            app(DatasetSignalDetector::class)->detect($this->study);
        } finally {
            $this->assertNull($dataset->fresh()->auto_detected_signals);
        }
    }

    public function test_skips_studies_without_archive_url(): void
    {
        $this->study->update(['download_url' => null]);
        Http::fake();

        $this->assertSame(0, app(DatasetSignalDetector::class)->detect($this->study));

        Http::assertNothingSent();
    }

    private function createDataset(string $relativeUrl, string $type): Dataset
    {
        $fsObject = FileSystemObject::factory()->forStudy($this->study)->create([
            'type' => $type,
            'relative_url' => $relativeUrl,
        ]);

        return Dataset::factory()->for($this->study)->create([
            'project_id' => $this->study->project_id,
            'fs_id' => $fsObject->id,
        ]);
    }

    /**
     * @param  string|array<int, string>  $nucleus
     * @param  array<int, array<string, mixed>>  $ranges
     * @param  array<int, array<string, mixed>>  $zones
     * @return array<string, mixed>
     */
    private function spectrum(int $dimension, string|array $nucleus, string $experiment, string $entry, array $ranges = [], array $zones = []): array
    {
        $spectrum = [
            'info' => ['dimension' => $dimension, 'nucleus' => $nucleus, 'experiment' => $experiment],
            'sourceSelector' => ['files' => ['/nmrxiv/local/archive/abc/sample.zip/'.$entry]],
        ];

        return $dimension === 1
            ? $spectrum + ['ranges' => ['values' => $ranges], 'peaks' => ['values' => []]]
            : $spectrum + ['zones' => ['values' => $zones]];
    }
}
