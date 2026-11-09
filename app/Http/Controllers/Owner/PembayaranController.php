<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\PembayaranRequest;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;

class PembayaranController extends Controller
{
    public function store(PembayaranRequest $request, Pesanan $pesanan): RedirectResponse
    {
        $pesanan->pembayaran()->create($request->validated());

        return back()->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function destroy(Pembayaran $pembayaran): RedirectResponse
    {
        $pembayaran->delete();

        return back()->with('success', 'Catatan pembayaran dihapus.');
    }
}
