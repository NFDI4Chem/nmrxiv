<?php

namespace App\Services;

use App\Support\Nmr\NmriumSignalExtractor;
use App\Support\Nmr\NmrkitSpectraParser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Detects peaks in a spectrum uploaded on the public Spectra search tab.
 *
 * The file is stored briefly under `nmrxiv.spectra_search.temp_prefix` at an
 * unguessable path and NMRKit reads it through its public URL: NMRKit drops
 * the query string, so pre-signed URLs are refused by S3. The object is
 * removed as soon as NMRKit answers; `nmrxiv:clean-spectra-search-uploads`
 * removes any leftovers.
 */
class UploadedSpectraParser
{
    public function __construct(
        private NmrkitSpectraParser $nmrkit,
        private NmriumSignalExtractor $extractor,
    ) {}

    /**
     * @return list<array{index: int, name: ?string, nucleus: mixed, experiment: ?string, solvent: ?string, frequency: ?float, supported: bool, signals: list<array{shift: float, multiplicity: ?string, intensity: ?float, is_solvent: bool}>}>
     */
    public function parse(UploadedFile $file): array
    {
        $disk = Storage::disk(config('filesystems.default'));
        $directory = trim((string) config('nmrxiv.spectra_search.temp_prefix'), '/').'/'.Str::uuid();
        $path = $disk->putFileAs($directory, $file, $this->storedName($file), ['visibility' => 'public']);

        try {
            $spectra = $this->nmrkit->parseUrl($disk->url($path));
        } finally {
            $disk->deleteDirectory($directory);
        }

        $nuclei = config('nmrxiv.spectra_search.nuclei', ['1H', '13C']);

        return array_map(function (array $spectrum, int $index) use ($nuclei): array {
            $extracted = $this->extractor->fromSpectrum($spectrum);
            $supported = $extracted !== null && in_array($extracted['nucleus'], $nuclei, true);

            return [
                'index' => $index,
                'name' => $this->spectrumName($spectrum),
                'nucleus' => $extracted['nucleus'] ?? data_get($spectrum, 'info.nucleus'),
                'experiment' => $extracted['experiment'] ?? data_get($spectrum, 'info.experiment'),
                'solvent' => $extracted['solvent'] ?? null,
                'frequency' => $extracted['frequency'] ?? null,
                'supported' => $supported,
                'signals' => $supported ? $extracted['signals'] : [],
            ];
        }, $spectra, array_keys($spectra));
    }

    private function storedName(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'spectrum';

        return $extension === '' ? $name : "{$name}.{$extension}";
    }

    /**
     * @param  array<string, mixed>  $spectrum
     */
    private function spectrumName(array $spectrum): ?string
    {
        $name = data_get($spectrum, 'info.name')
            ?? data_get($spectrum, 'display.name')
            ?? data_get($spectrum, 'sourceSelector.files.0')
            ?? data_get($spectrum, 'info.title');
        if (! is_string($name) || $name === '') {
            return null;
        }

        if (str_contains($name, '.zip/')) {
            return Str::after($name, '.zip/');
        }

        return str_contains($name, '/') ? basename($name) : $name;
    }
}
