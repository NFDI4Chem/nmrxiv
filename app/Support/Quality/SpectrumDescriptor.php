<?php

declare(strict_types=1);

namespace App\Support\Quality;

/**
 * Structured view of one NMRium spectrum used by the quality rubric.
 */
final readonly class SpectrumDescriptor
{
    /**
     * @param  list<string>  $nuclei
     * @param  list<string>  $sourceFiles
     */
    public function __construct(
        public ?int $dimension,
        public array $nuclei,
        public ?string $experiment,
        public ?int $datasetId = null,
        public ?int $studyId = null,
        public array $sourceFiles = [],
    ) {}

    /**
     * Build a descriptor from a raw NMRium spectrum payload.
     *
     * @param  array<string, mixed>  $spectrum
     */
    public static function fromNmriumSpectrum(
        array $spectrum,
        ?int $datasetId = null,
        ?int $studyId = null,
        ?callable $guessDimension = null,
    ): self {
        $info = is_array($spectrum['info'] ?? null) ? $spectrum['info'] : [];

        $experiment = isset($info['experiment']) && is_string($info['experiment']) && $info['experiment'] !== ''
            ? strtolower(trim($info['experiment']))
            : null;

        $nuclei = [];
        $nucleus = $info['nucleus'] ?? null;
        if (is_array($nucleus)) {
            foreach ($nucleus as $value) {
                if (is_string($value) && $value !== '') {
                    $nuclei[] = $value;
                }
            }
        } elseif (is_string($nucleus) && $nucleus !== '') {
            $nuclei[] = $nucleus;
        }

        $dimension = null;
        if (isset($info['dimension']) && is_numeric($info['dimension'])) {
            $dimension = (int) $info['dimension'];
        } elseif ($guessDimension !== null) {
            $guessed = $guessDimension($spectrum);
            $dimension = is_int($guessed) ? $guessed : null;
        }

        $selector = $spectrum['sourceSelector'] ?? $spectrum['selector'] ?? [];
        $files = is_array($selector['files'] ?? null) ? $selector['files'] : [];
        $sourceFiles = array_values(array_filter($files, 'is_string'));

        return new self(
            dimension: $dimension,
            nuclei: $nuclei,
            experiment: $experiment,
            datasetId: $datasetId,
            studyId: $studyId,
            sourceFiles: $sourceFiles,
        );
    }
}
