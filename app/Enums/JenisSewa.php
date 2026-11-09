<?php

namespace App\Enums;

enum JenisSewa: string
{
    case LepasKunci = 'lepas_kunci';
    case DenganSopir = 'dengan_sopir';

    public function label(): string
    {
        return match ($this) {
            self::LepasKunci => 'Lepas Kunci',
            self::DenganSopir => 'Dengan Sopir',
        };
    }
}
