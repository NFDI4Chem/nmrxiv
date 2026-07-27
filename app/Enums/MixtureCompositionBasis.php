<?php

namespace App\Enums;

enum MixtureCompositionBasis: string
{
    case MolePercent = 'mole_percent';
    case WeightPercent = 'weight_percent';
    case VolumePercent = 'volume_percent';
    case MolarRatio = 'molar_ratio';

    /**
     * Short unit label for totals and table headers (never a bare "%").
     */
    public function unitLabel(): string
    {
        return match ($this) {
            self::MolePercent => 'mol %',
            self::WeightPercent => 'wt %',
            self::VolumePercent => 'vol %',
            self::MolarRatio => 'molar ratio',
        };
    }

    /**
     * Longer label for helper text and read-only summaries.
     */
    public function displayLabel(): string
    {
        return match ($this) {
            self::MolePercent => 'mole % (mol/mol)',
            self::WeightPercent => 'weight % (wt/wt)',
            self::VolumePercent => 'volume % (vol/vol)',
            self::MolarRatio => 'molar ratio',
        };
    }

    /**
     * Whether component values are expected to sum to 100 (± tolerance).
     */
    public function expectsNormalizedTotal(): bool
    {
        return $this !== self::MolarRatio;
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
