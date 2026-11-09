<?php

namespace App\Enums;

enum JenisPembayaran: string
{
    case Dp = 'dp';
    case Pelunasan = 'pelunasan';
    case Denda = 'denda';

    public function label(): string
    {
        return match ($this) {
            self::Dp => 'DP',
            self::Pelunasan => 'Pelunasan',
            self::Denda => 'Denda',
        };
    }
}
