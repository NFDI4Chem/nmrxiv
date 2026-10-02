<?php

namespace App\Enums;

enum AssignmentSource: string
{
    case Nmrium = 'nmrium';
    case MnovaSdf = 'mnova_sdf';
    case Nmredata = 'nmredata';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Nmrium => 'NMRium assignments',
            self::MnovaSdf => 'Mnova SDF export',
            self::Nmredata => 'NMReDATA file',
            self::Manual => 'Manual entry',
        };
    }
}
