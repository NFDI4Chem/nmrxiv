<?php

declare(strict_types=1);

namespace App\Support\Quality;

/**
 * Gathered evidence for one molecule under a given scope (global or team).
 *
 * @phpstan-type Extra array<string, mixed>
 */
final readonly class MoleculeEvidence
{
    /**
     * @param  list<SpectrumDescriptor>  $spectra
     * @param  Extra  $extra
     */
    public function __construct(
        public int $moleculeId,
        public array $spectra = [],
        public bool $hasAssignments = false,
        public array $extra = [],
    ) {}

    public function hasSpectra(): bool
    {
        return $this->spectra !== [];
    }

    /**
     * @return Extra
     */
    public function withExtra(string $key, mixed $value): array
    {
        return array_merge($this->extra, [$key => $value]);
    }
}
