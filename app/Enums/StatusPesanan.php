<?php

namespace App\Enums;

enum StatusPesanan: string
{
    case Dipesan = 'dipesan';
    case Disewa = 'disewa';
    case Selesai = 'selesai';
    case Batal = 'batal';

    public function label(): string
    {
        return match ($this) {
            self::Dipesan => 'Dipesan',
            self::Disewa => 'Sedang Disewa',
            self::Selesai => 'Selesai',
            self::Batal => 'Dibatalkan',
        };
    }

    public function bisaKe(self $tujuan): bool
    {
        return match ($this) {
            self::Dipesan => in_array($tujuan, [self::Disewa, self::Batal], true),
            self::Disewa => $tujuan === self::Selesai,
            default => false,
        };
    }
}
