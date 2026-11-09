<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\PelangganRequest;
use App\Models\Pelanggan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PelangganController extends Controller
{
    public function index(Request $request): View
    {
        $q = mb_substr((string) $request->query('q', ''), 0, 120);

        return view('owner.pelanggan.index', ['pelanggan' => Pelanggan::withCount('pesanan')->when($q, fn ($query) => $query->where(fn ($s) => $s->where('nama', 'like', '%'.$q.'%')->orWhere('no_hp', 'like', '%'.$q.'%')))->orderBy('nama')->paginate(15)->withQueryString()]);
    }

    public function create(): View
    {
        return view('owner.pelanggan.form', ['pelanggan' => new Pelanggan]);
    }

    public function store(PelangganRequest $request): RedirectResponse
    {
        $p = Pelanggan::create($request->validated());

        return redirect()->route('owner.pelanggan.show', $p)->with('success', 'Kontak pelanggan ditambahkan.');
    }

    public function show(Pelanggan $pelanggan): View
    {
        return view('owner.pelanggan.show', ['pelanggan' => $pelanggan->load('pesanan.mobil', 'pesanan.pembayaran')]);
    }

    public function edit(Pelanggan $pelanggan): View
    {
        return view('owner.pelanggan.form', compact('pelanggan'));
    }

    public function update(PelangganRequest $request, Pelanggan $pelanggan): RedirectResponse
    {
        $pelanggan->update($request->validated());

        return redirect()->route('owner.pelanggan.show', $pelanggan)->with('success', 'Kontak pelanggan diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan): RedirectResponse
    {
        if ($pelanggan->pesanan()->exists()) {
            throw ValidationException::withMessages(['pelanggan' => 'Pelanggan dengan riwayat pesanan tidak dapat dihapus.']);
        }
        $pelanggan->delete();

        return redirect()->route('owner.pelanggan.index')->with('success','Kontak pelanggan dihapus.');
    }
}
