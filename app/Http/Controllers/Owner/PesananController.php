<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\PesananRequest;
use App\Http\Requests\StatusRequest;
use App\Models\Mobil;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Services\PesananService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function __construct(private PesananService $service) {}

    public function index(Request $request): View
    {
        $q = mb_substr((string) $request->query('q', ''), 0, 120);
        $status = (string) $request->query('status', '');
        $pesanan = Pesanan::with('mobil', 'pelanggan', 'pembayaran')->when(in_array($status, ['dipesan', 'disewa', 'selesai', 'batal']), fn ($q) => $q->where('status', $status))
            ->when($q, fn ($query) => $query->where(fn ($s) => $s->where('kode', 'like', '%'.$q.'%')->orWhereHas('pelanggan', fn ($p) => $p->where('nama', 'like', '%'.$q.'%'))->orWhereHas('mobil', fn ($m) => $m->where('plat_nomor', 'like', '%'.$q.'%'))))
            ->latest()->paginate(15)->withQueryString();

        return view('owner.pesanan.index', compact('pesanan'));
    }

    private function form(Pesanan $pesanan): View
    {
        return view('owner.pesanan.form', ['pesanan' => $pesanan, 'mobil' => Mobil::orderBy('merk')->get(), 'pelanggan' => Pelanggan::orderBy('nama')->get()]);
    }

    public function create(): View
    {
        return $this->form(new Pesanan);
    }

    public function store(PesananRequest $request): RedirectResponse
    {
        $p = $this->service->simpan($request->validated());

        return redirect()->route('owner.pesanan.show', $p)->with('success', 'Pesanan '.$p->kode.' tersimpan.');
    }

    public function show(Pesanan $pesanan): View
    {
        return view('owner.pesanan.show', ['pesanan' => $pesanan->load('mobil', 'pelanggan', 'pembayaran')]);
    }

    public function edit(Pesanan $pesanan): View
    {
        abort_unless($pesanan->status->value === 'dipesan', 403);

        return $this->form($pesanan);
    }

    public function update(PesananRequest $request, Pesanan $pesanan): RedirectResponse
    {
        $this->service->simpan($request->validated(), $pesanan);

        return redirect()->route('owner.pesanan.show', $pesanan)->with('success', 'Pesanan diperbarui.');
    }

    public function status(StatusRequest $request, Pesanan $pesanan): RedirectResponse
    {
        $this->service->status($pesanan,$request->validated());

        return back()->with('success','Status pesanan diperbarui.');
    }
}
