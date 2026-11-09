<?php

namespace App\Services;

use App\Enums\JenisSewa;
use App\Models\Mobil;
use App\Models\Pengaturan;
use Carbon\CarbonInterface;

class WhatsAppLinkBuilder
{
    public function kontak(): ?string
    {
        $nomor = Pengaturan::nilai()->no_whatsapp;

        return preg_match('/^62[0-9]{8,13}$/', $nomor ?? '') ? 'https://wa.me/'.$nomor.'?text='.rawurlencode('Halo Gudang Rental Mobil, saya ingin bertanya tentang sewa mobil.') : null;
    }

    public function tanya(Mobil $mobil): ?string
    {
        if (! $this->kontak()) {
            return null;
        }

        $teks = 'Halo '.Pengaturan::nilai()->nama_usaha.', saya ingin bertanya harga sewa '.$mobil->nama().' ('.$mobil->plat_nomor.').';

        return 'https://wa.me/'.Pengaturan::nilai()->no_whatsapp.'?text='.rawurlencode($teks);
    }

    public function buat(Mobil $mobil, JenisSewa $jenis, CarbonInterface $ambil, CarbonInterface $kembali, ?RincianBiaya $biaya): ?string
    {
        $pengaturan = Pengaturan::nilai();
        if (! $this->kontak()) {
            return null;
        }
        $teks = strtr($pengaturan->template_pesan, [
            '{nama_usaha}' => $pengaturan->nama_usaha, '{mobil}' => $mobil->nama(), '{plat}' => $mobil->plat_nomor,
            '{jenis_sewa}' => $jenis->label(), '{tgl_ambil}' => tanggal_id($ambil), '{tgl_kembali}' => tanggal_id($kembali),
            '{lama}' => (string) ($biaya?->lamaSewa ?? app(BiayaService::class)->lamaSewa($ambil, $kembali)),
            '{estimasi}' => $biaya ? rupiah($biaya->total) : 'Mohon informasi harga sewa',
        ]);

        return 'https://wa.me/'.$pengaturan->no_whatsapp.'?text='.rawurlencode($teks);
    }
}
