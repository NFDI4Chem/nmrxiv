<?php

namespace App\Enums;

enum MixtureDeterminationMethod: string
{
    case Qnmr = 'qnmr';
    case Gravimetric = 'gravimetric';
    case SupplierStated = 'supplier_stated';
    case Other = 'other';

    public function displayLabel(): string
    {
        return match ($this) {
            self::Qnmr => 'qNMR',
            self::Gravimetric => 'Gravimetric',
            self::SupplierStated => 'Supplier stated',
            self::Other => 'Other',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
