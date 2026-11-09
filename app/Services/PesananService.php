<?php

namespace App\Services;

use App\Enums\JenisSewa;
use App\Enums\StatusMobil;
use App\Enums\StatusPesanan;
use App\Models\Mobil;
use App\Models\Pelanggan;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PesananService
{
    public function __construct(private KetersediaanService $jadwal, private BiayaService $biaya) {}

    public function simpan(array $data, ?Pesanan $pesanan = null): Pesanan
    {
        Pengaturan::nilai();

        return DB::transaction(function () use ($data, $pesanan) {
            Pengaturan::whereKey(1)->lockForUpdate()->firstOrFail();
            if ($pesanan) {
                $pesanan = Pesanan::whereKey($pesanan->id)->lockForUpdate()->firstOrFail();
                if ($pesanan->status !== StatusPesanan::Dipesan) {
                    throw ValidationException::withMessages(['pesanan' => 'Hanya pesanan berstatus Dipesan yang dapat diubah.']);
                }
            }
            $mobil = Mobil::whereKey($data['mobil_id'])->lockForUpdate()->firstOrFail();
            $ambil = Carbon::parse($data['tgl_ambil']);
            $kembali = Carbon::parse($data['tgl_kembali']);
            $bentrok = $this->jadwal->bentrok($mobil->id, $ambil, $kembali, $pesanan?->id);
            if ($mobil->status === StatusMobil::Perawatan || $bentrok->isNotEmpty()) {
                throw ValidationException::withMessages(['tgl_ambil' => $mobil->status === StatusMobil::Perawatan ? 'Mobil sedang perawatan. Pilih mobil lain.' : 'Jadwal bentrok dengan pesanan '.$bentrok->pluck('kode')->join(', ').'. Pilih tanggal atau mobil lain.']);
            }
            if (empty($data['pelanggan_id'])) {
                $pelanggan = Pelanggan::create(['nama' => $data['nama_pelanggan'], 'no_hp' => $data['no_hp_pelanggan']]);
                $data['pelanggan_id'] = $pelanggan->id;
            }
            $jenis = JenisSewa::from($data['jenis_sewa']);
            $mobil->harga_per_hari = $pesanan?->harga_per_hari ?? $mobil->harga_per_hari ?? ($data['harga_kesepakatan'] ?? null);
            $rincian = $this->biaya->hitung($mobil, $jenis, $ambil, $kembali);
            $harga = $pesanan?->harga_per_hari ?? $rincian->hargaPerHari;
            $tarifSopir = $pesanan && $pesanan->jenis_sewa === JenisSewa::DenganSopir ? intdiv($pesanan->biaya_sopir, $pesanan->lama_sewa) : (int) Pengaturan::nilai()->biaya_sopir_per_hari;
            $sopir = $jenis === JenisSewa::DenganSopir ? $tarifSopir * $rincian->lamaSewa : 0;
            $nilai = [
                'pelanggan_id' => $data['pelanggan_id'], 'mobil_id' => $mobil->id, 'jenis_sewa' => $jenis,
                'tgl_ambil' => $ambil, 'tgl_kembali' => $kembali, 'lama_sewa' => $rincian->lamaSewa,
                'harga_per_hari' => $harga, 'biaya_sopir' => $sopir, 'total_biaya' => $harga * $rincian->lamaSewa + $sopir,
                'catatan' => $data['catatan'] ?? null,
            ];
            if ($pesanan) {
                $pesanan->update($nilai);

                return $pesanan;
            }
            $prefix = 'GRM-'.now()->format('ym').'-';
            $last = Pesanan::where('kode', 'like', $prefix.'%')->orderByDesc('id')->first();
            $urut = $last ? ((int) substr($last->kode, strrpos($last->kode, '-') + 1)) + 1 : 1;

            return Pesanan::create($nilai + ['kode' => $prefix.str_pad((string) $urut, 4, '0', STR_PAD_LEFT), 'status' => StatusPesanan::Dipesan]);
        });
    }

    public function status(Pesanan $pesanan, array $data): void
    {
        DB::transaction(function () use ($pesanan, $data) {
            $pesanan = Pesanan::whereKey($pesanan->id)->lockForUpdate()->firstOrFail();
            $tujuan = StatusPesanan::from($data['status']);
            if (! $pesanan->status->bisaKe($tujuan)) {
                throw ValidationException::withMessages(['status' => 'Perubahan status ini tidak diperbolehkan.']);
            }
            $nilai = ['status' => $tujuan];
            if ($tujuan === StatusPesanan::Selesai) {
                $denda = (int) ($data['denda'] ?? 0);
                $nilai += ['tgl_dikembalikan' => $data['tgl_dikembalikan'], 'denda' => $denda, 'total_biaya' => $pesanan->harga_per_hari * $pesanan->lama_sewa + $pesanan->biaya_sopir + $denda];
            }
            $pesanan->update($nilai);
        });
    }
}
