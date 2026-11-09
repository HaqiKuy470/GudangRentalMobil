<?php

namespace App\Services;

use App\Enums\JenisSewa;
use App\Models\Mobil;
use App\Models\Pengaturan;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;

class BiayaService
{
    public function lamaSewa(CarbonInterface $ambil, CarbonInterface $kembali): int
    {
        if ($kembali->lt($ambil)) {
            throw ValidationException::withMessages(['tgl_kembali' => 'Tanggal kembali harus sama atau setelah tanggal ambil.']);
        }

        return (int) $ambil->copy()->startOfDay()->diffInDays($kembali->copy()->startOfDay()) + 1;
    }

    public function hitung(Mobil $mobil, JenisSewa $jenis, CarbonInterface $ambil, CarbonInterface $kembali, int $denda = 0): RincianBiaya
    {
        if ($mobil->harga_per_hari === null) {
            throw ValidationException::withMessages(['harga_kesepakatan' => 'Isi harga per hari sesuai kesepakatan dengan pelanggan.']);
        }

        $lama = $this->lamaSewa($ambil, $kembali);
        $subtotal = $mobil->harga_per_hari * $lama;
        $sopir = $jenis === JenisSewa::DenganSopir ? (int) Pengaturan::nilai()->biaya_sopir_per_hari * $lama : 0;

        return new RincianBiaya($lama, $mobil->harga_per_hari, $subtotal, $sopir, $denda, $subtotal + $sopir + $denda);
    }
}
