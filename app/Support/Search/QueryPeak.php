<?php

namespace App\Support\Search;

use App\Enums\PeakRule;

/**
 * One row of a spectrum search query: a single shift (from === to) or a
 * region, with optional proton count and shape used only for ranking.
 */
final readonly class QueryPeak
{
    public function __construct(
        public float $from,
        public float $to,
        public PeakRule $rule = PeakRule::Must,
        public ?float $protons = null,
        public ?string $shape = null,
    ) {}

    public function isRegion(): bool
    {
        return $this->to > $this->from;
    }

    public function center(): float
    {
        return ($this->from + $this->to) / 2;
    }

    /**
     * @return array{from: float, to: float, rule: string, protons: ?float, shape: ?string}
     */
    public function toArray(): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'rule' => $this->rule->value,
            'protons' => $this->protons,
            'shape' => $this->shape,
        ];
    }

    /**
     * Canonical multiplicity ("br s" and "brs" become "s"), or null when unrecognised.
     */
    public static function normalizeShape(?string $shape): ?string
    {
        $normalized = preg_replace('/^br/', '', strtolower(preg_replace('/[\s.]+/', '', (string) $shape)));
        $normalized = match ($normalized) {
            'quint', 'quin' => 'p',
            'hept' => 'sept',
            default => $normalized,
        };

        return preg_match('/^(?:[sdtqm]{1,4}|p|sext|sept)$/', $normalized) === 1 ? $normalized : null;
    }
}
