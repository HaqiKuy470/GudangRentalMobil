<?php

namespace App\Http\Controllers;

use App\Enums\JenisSewa;
use App\Http\Requests\TanggalRequest;
use App\Models\Mobil;
use App\Services\BiayaService;
use App\Services\KetersediaanService;
use App\Services\WhatsAppLinkBuilder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PublikController extends Controller
{
    public function __construct(private KetersediaanService $jadwal, private BiayaService $biaya, private WhatsAppLinkBuilder $wa) {}

    public function beranda(): View
    {
        return view('publik.beranda', ['mobil' => Mobil::where('status', 'tersedia')->limit(3)->get()]);
    }

    public function katalog(TanggalRequest $request): View
    {
        $data = $request->validated();
        $mobil = Mobil::query()->when($data['q'] ?? null, fn ($q, $s) => $q->where(fn ($q) => $q->where('merk', 'like', '%'.$s.'%')->orWhere('model', 'like', '%'.$s.'%')))
            ->when($data['jenis'] ?? null, fn ($q, $v) => $q->where('jenis', $v))
            ->when($data['kapasitas'] ?? null, fn ($q, $v) => $q->where('kapasitas', '>=', $v))
            ->when($data['transmisi'] ?? null, fn ($q, $v) => $q->where('transmisi', $v))
            ->when(isset($data['harga_max']), fn ($q) => $q->where('harga_per_hari', '<=', $data['harga_max']))->orderBy('harga_per_hari')->get();
        $mobil->each(function ($m) use ($data) {
            $m->tersedia_pilihan = ! empty($data['tgl_ambil']) ? $this->jadwal->tersedia($m, Carbon::parse($data['tgl_ambil']), Carbon::parse($data['tgl_kembali'])) : $m->statusHariIni() === 'tersedia';
        });

        return view('publik.katalog', ['mobil' => $mobil->sortByDesc('tersedia_pilihan')->values()]);
    }

    private function rincian(Mobil $mobil, array $data): array
    {
        $jenis = JenisSewa::from($data['jenis_sewa'] ?? 'lepas_kunci');
        $adaTanggal = ! empty($data['tgl_ambil']) && ! empty($data['tgl_kembali']);
        $ambil = $adaTanggal ? Carbon::parse($data['tgl_ambil']) : null;
        $kembali = $adaTanggal ? Carbon::parse($data['tgl_kembali']) : null;
        $tersedia = $adaTanggal ? $this->jadwal->tersedia($mobil, $ambil, $kembali) : null;
        $biaya = $adaTanggal && $mobil->harga_per_hari !== null ? $this->biaya->hitung($mobil, $jenis, $ambil, $kembali) : null;
        $lama = $adaTanggal ? $this->biaya->lamaSewa($ambil, $kembali) : null;
        $tanya = $mobil->harga_per_hari === null && $mobil->status->value === 'tersedia' ? $this->wa->tanya($mobil) : null;

        return ['jenis' => $jenis, 'tersedia' => $tersedia, 'biaya' => $biaya, 'lama' => $lama, 'tanya' => $tanya,
            'wa' => $tersedia ? $this->wa->buat($mobil, $jenis, $ambil, $kembali, $biaya) : ($adaTanggal ? null : $tanya)];
    }

    public function detail(TanggalRequest $request, Mobil $mobil): View
    {
        return view('publik.detail', ['mobil' => $mobil, 'tanggalTerpakai' => $this->jadwal->tanggalTerpakai($mobil)] + $this->rincian($mobil, $request->validated()));
    }

    public function estimasi(TanggalRequest $request, Mobil $mobil): JsonResponse
    {
        $r = $this->rincian($mobil, $request->validated());

        $harga = $mobil->harga_per_hari === null ? 'Hubungi untuk harga' : '—';

        return response()->json(['tersedia' => $r['tersedia'], 'lama' => $r['lama'], 'sewa' => $r['biaya'] ? rupiah($r['biaya']->subtotalSewa) : $harga,
            'sopir' => $r['biaya'] ? rupiah($r['biaya']->biayaSopir) : '—', 'total' => $r['biaya'] ? rupiah($r['biaya']->total) : $harga, 'wa' => $r['wa']]);
    }

    public function tentang(): View
    {
        return view('publik.tentang');
    }
}
