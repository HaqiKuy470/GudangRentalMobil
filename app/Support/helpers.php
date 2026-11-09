<?php

use Carbon\Carbon;

function rupiah(int|float $nilai): string
{
    return 'Rp'.number_format($nilai, 0, ',', '.');
}
function tanggal_id(mixed $tanggal): string
{
    return $tanggal ? Carbon::parse($tanggal)->translatedFormat('j M Y') : '—';
}
