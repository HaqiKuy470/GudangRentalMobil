<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $aktif = Pesanan::whereIn('status', ['dipesan', 'disewa']);
        $jadwal = Pesanan::with('mobil', 'pelanggan')->whereIn('status', ['dipesan', 'disewa'])->where(function ($q) {
            $q->whereBetween('tgl_ambil', [today(), today()->addDays(7)])->orWhereBetween('tgl_kembali', [today(), today()->addDays(7)]);
        })->orderBy('tgl_ambil')->get();
        $belumLunas = Pesanan::with('mobil', 'pelanggan', 'pembayaran')->where('status', '!=', 'batal')->get()->filter(fn ($p) => $p->sisaTagihan() > 0);

        return view('owner.dashboard', [
            'aktif' => $aktif->count(), 'disewa' => Pesanan::where('status', 'disewa')->distinct('mobil_id')->count('mobil_id'),
            'kembali' => Pesanan::whereIn('status', ['dipesan', 'disewa'])->whereDate('tgl_kembali', today())->count(),
            'pendapatan' => (int) Pembayaran::whereBetween('tgl_bayar', [today()->startOfMonth(), today()->endOfMonth()])->sum('jumlah'),
            'jadwal' => $jadwal, 'belumLunas' => $belumLunas,
        ]);
    }
}
