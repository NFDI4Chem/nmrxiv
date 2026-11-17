<?php

declare(strict_types=1);

namespace App\Support\Quality;

/**
 * Result of evaluating a molecule against the quality rubric.
 *
 * @phpstan-type Breakdown array{
 *     version: int,
 *     tier: int,
 *     tier_label: string,
 *     criteria: array<string, bool>,
 *     bonuses: array<string, bool>,
 *     next_tier_missing: list<string>
 * }
 */
final readonly class QualityResult
{
    /**
     * @param  array<string, bool>  $criteria
     * @param  array<string, bool>  $bonuses
     * @param  list<string>  $nextTierMissing
     */
    public function __construct(
        public int $version,
        public int $tier,
        public string $tierLabel,
        public array $criteria,
        public array $bonuses,
        public array $nextTierMissing = [],
    ) {}

    /**
     * @return Breakdown
     */
    public function toArray(): array
    {
        return [
            'version' => $this->version,
            'tier' => $this->tier,
            'tier_label' => $this->tierLabel,
            'criteria' => $this->criteria,
            'bonuses' => $this->bonuses,
            'next_tier_missing' => $this->nextTierMissing,
        ];
    }
}
