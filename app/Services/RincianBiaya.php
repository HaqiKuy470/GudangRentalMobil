<?php

namespace App\Services;

readonly class RincianBiaya
{
    public function __construct(public int $lamaSewa, public int $hargaPerHari, public int $subtotalSewa, public int $biayaSopir, public int $denda, public int $total) {}
}
