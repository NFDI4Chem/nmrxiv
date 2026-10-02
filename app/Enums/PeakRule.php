<?php

namespace App\Enums;

/**
 * How a peak in a spectrum search query constrains the results.
 */
enum PeakRule: string
{
    case Must = 'must';
    case Nice = 'nice';
    case MustNot = 'not';

    /**
     * Prefix used in the compact URL encoding of a query peak.
     */
    public function prefix(): string
    {
        return match ($this) {
            self::Must => '',
            self::Nice => '?',
            self::MustNot => '!',
        };
    }

    public static function fromPrefix(string $prefix, self $default): self
    {
        return match ($prefix) {
            '?' => self::Nice,
            '!' => self::MustNot,
            '+' => self::Must,
            default => $default,
        };
    }
}
