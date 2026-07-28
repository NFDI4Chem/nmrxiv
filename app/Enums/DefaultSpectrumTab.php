<?php

namespace App\Enums;

enum DefaultSpectrumTab: string
{
    case H1 = '1H';
    case C13 = '13C';
    case F19 = '19F';
    case P31 = '31P';
    case N15 = '15N';
    case COSY = 'COSY';
    case HSQC = 'HSQC';
    case HMBC = 'HMBC';
    case NOESY = 'NOESY';
    case DEPT135 = 'DEPT135';

    public function dimension(): DefaultSpectrumDimension
    {
        return match ($this) {
            self::H1, self::C13, self::F19, self::P31, self::N15 => DefaultSpectrumDimension::OneD,
            default => DefaultSpectrumDimension::TwoD,
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return list<string>
     */
    public static function valuesForDimension(DefaultSpectrumDimension $dimension): array
    {
        return array_values(array_map(
            fn (self $case) => $case->value,
            array_filter(
                self::cases(),
                fn (self $case) => $case->dimension() === $dimension
            )
        ));
    }
}
