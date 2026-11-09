<?php

namespace App\Services;

use App\Enums\StatusMobil;
use App\Models\Mobil;
use App\Models\Pesanan;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

class KetersediaanService
{
    public function bentrok(int $mobilId, CarbonInterface $ambil, CarbonInterface $kembali, ?int $abaikanPesananId = null): Collection
    {
        return Pesanan::where('mobil_id', $mobilId)->whereIn('status', ['dipesan', 'disewa'])
            ->when($abaikanPesananId, fn ($q) => $q->where('id', '!=', $abaikanPesananId))
            ->whereDate('tgl_ambil', '<=', $kembali)->whereDate('tgl_kembali', '>=', $ambil)->get();
    }

    public function tersedia(Mobil $mobil, CarbonInterface $ambil, CarbonInterface $kembali): bool
    {
        return $mobil->status === StatusMobil::Tersedia && $this->bentrok($mobil->id, $ambil, $kembali)->isEmpty();
    }

    public function tanggalTerpakai(Mobil $mobil, int $hariKeDepan = 60): array
    {
        return $mobil->pesanan()->whereIn('status', ['dipesan', 'disewa'])->whereDate('tgl_kembali', '>=', today())
            ->whereDate('tgl_ambil', '<=', today()->addDays($hariKeDepan))->orderBy('tgl_ambil')->get()
            ->map(fn ($p) => ['ambil' => $p->tgl_ambil->toDateString(), 'kembali' => $p->tgl_kembali->toDateString()])->all();
    }
}
