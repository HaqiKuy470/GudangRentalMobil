<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Pesanan;

class LaporanService
{
    public function periode(string $awal, string $akhir): array
    {
        $pembayaran = Pembayaran::with('pesanan.pelanggan', 'pesanan.mobil')->whereDate('tgl_bayar', '>=', $awal)->whereDate('tgl_bayar', '<=', $akhir)->orderBy('tgl_bayar')->get();

        return ['pembayaran' => $pembayaran, 'pesanan' => Pesanan::with('mobil', 'pelanggan', 'pembayaran')->whereDate('tgl_ambil', '>=', $awal)->whereDate('tgl_ambil', '<=', $akhir)->orderBy('tgl_ambil')->get(),
            'total' => (int) $pembayaran->sum('jumlah'), 'perJenis' => $pembayaran->groupBy(fn ($p) => $p->jenis->label())->map(fn ($items) => $items->sum('jumlah'))];
    }
}
