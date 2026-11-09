<?php

namespace App\Enums;

enum MetodePembayaran: string
{
    case Transfer = 'transfer';
    case Tunai = 'tunai';

    public function label(): string
    {
        return match ($this) {
            self::Transfer => 'Transfer',
            self::Tunai => 'Tunai',
        };
    }
}
