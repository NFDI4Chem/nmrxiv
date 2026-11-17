<?php

declare(strict_types=1);

namespace App\Support\Quality\Criteria;

use App\Support\Quality\MoleculeEvidence;
use App\Support\Quality\SpectrumDescriptor;
use InvalidArgumentException;

final class ExperimentFamilyCriterion implements QualityCriterion
{
    /**
     * @param  list<string>  $nuclei
     * @param  list<string>  $experiments
     */
    public function __construct(
        private readonly string $key,
        private readonly string $label,
        private readonly ?int $dimension,
        private readonly array $nuclei,
        private readonly array $experiments,
    ) {
        if ($this->experiments === []) {
            throw new InvalidArgumentException("Family '{$key}' must declare at least one experiment token.");
        }
    }

    /**
     * @param  array{label?: string, dimension?: int|null, nuclei?: list<string>, experiments?: list<string>}  $family
     */
    public static function fromFamilyConfig(string $key, array $family): self
    {
        return new self(
            key: $key,
            label: (string) ($family['label'] ?? $key),
            dimension: isset($family['dimension']) && is_numeric($family['dimension'])
                ? (int) $family['dimension']
                : null,
            nuclei: array_values(array_map('strval', $family['nuclei'] ?? [])),
            experiments: array_values(array_map(
                static fn (mixed $e): string => strtolower((string) $e),
                $family['experiments'] ?? [],
            )),
        );
    }

    public function key(): string
    {
        return $this->key;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function isMet(MoleculeEvidence $evidence): bool
    {
        foreach ($evidence->spectra as $spectrum) {
            if ($this->matches($spectrum)) {
                return true;
            }
        }

        return false;
    }

    public function matches(SpectrumDescriptor $spectrum): bool
    {
        $experiment = $spectrum->experiment !== null
            ? strtolower($spectrum->experiment)
            : null;

        if ($experiment === null || ! in_array($experiment, $this->experiments, true)) {
            return false;
        }

        if ($this->dimension !== null && $spectrum->dimension !== null && $spectrum->dimension !== $this->dimension) {
            return false;
        }

        if ($this->nuclei === []) {
            return true;
        }

        foreach ($spectrum->nuclei as $nucleus) {
            if (in_array($nucleus, $this->nuclei, true)) {
                return true;
            }
        }

        return false;
    }
}
