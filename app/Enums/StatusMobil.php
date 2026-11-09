<?php

namespace App\Enums;

enum StatusMobil: string
{
    case Tersedia = 'tersedia';
    case Perawatan = 'perawatan';

    public function label(): string
    {
        return match ($this) {
            self::Tersedia => 'Tersedia',
            self::Perawatan => 'Perawatan',
        };
    }
}
