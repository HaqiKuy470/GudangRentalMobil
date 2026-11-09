<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\MobilRequest;
use App\Models\Mobil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MobilController extends Controller
{
    public function index(Request $request): View
    {
        return view('owner.mobil.index', ['mobil' => Mobil::orderBy('merk')->paginate(15)]);
    }

    public function create(): View
    {
        return view('owner.mobil.form', ['mobil' => new Mobil]);
    }

    public function store(MobilRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('foto');
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('mobil', 'public');
        }
        Mobil::create($data);

        return redirect()->route('owner.mobil.index')->with('success', 'Mobil berhasil ditambahkan.');
    }

    public function edit(Mobil $mobil): View
    {
        return view('owner.mobil.form', compact('mobil'));
    }

    public function update(MobilRequest $request, Mobil $mobil): RedirectResponse
    {
        $data = $request->safe()->except('foto');
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('mobil', 'public');
        }
        DB::transaction(function () use ($mobil, $data) {
            Mobil::whereKey($mobil->id)->lockForUpdate()->firstOrFail()->update($data);
        });

        return redirect()->route('owner.mobil.index')->with('success', 'Data mobil diperbarui.');
    }

    public function destroy(Mobil $mobil): RedirectResponse
    {
        DB::transaction(function () use ($mobil) {
            $mobil = Mobil::whereKey($mobil->id)->lockForUpdate()->firstOrFail();
            if ($mobil->pesanan()->whereIn('status', ['dipesan', 'disewa'])->exists()) {
                throw ValidationException::withMessages(['mobil' => 'Mobil masih memiliki pesanan aktif dan tidak dapat dihapus.']);
            }
            $mobil->delete();
        });

        return back()->with('success', 'Mobil dihapus dari katalog. Riwayat pesanan tetap tersimpan.');
    }
}
