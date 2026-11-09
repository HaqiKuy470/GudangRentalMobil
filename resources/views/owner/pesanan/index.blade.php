@extends('layouts.owner')
@section('title', 'Pesanan')
@section('content')<div class="page-head">
        <div>
            <h1>Pesanan</h1>
            <p>Catat dan pantau setiap sewa yang sudah disepakati.</p>
        </div><x-button variant="accent" icon="plus" :href="route('owner.pesanan.create')">Catat pesanan</x-button>
    </div>
    <div class="toolbar">
        <nav class="tabs" aria-label="Filter status"><a href="{{ route('owner.pesanan.index') }}"
                class="{{ !request('status') ? 'active' : '' }}">Semua</a>
            @foreach (\App\Enums\StatusPesanan::cases() as $s)
                <a class="{{ request('status') === $s->value ? 'active' : '' }}"
                    href="{{ route('owner.pesanan.index', ['status' => $s->value]) }}">{{ $s->label() }}</a>
            @endforeach
        </nav>
        <form class="search-form"><input type="hidden" name="status" value="{{ request('status') }}"><input name="q"
                class="search-input" value="{{ request('q') }}" placeholder="Kode, nama, atau plat…"
                aria-label="Cari pesanan"><x-button icon="search">Cari</x-button></form>
    </div>
    <section class="panel table-panel">
        @if ($pesanan->isEmpty())
            <x-empty-state title="Belum ada pesanan yang cocok" description="Catat pesanan baru atau ubah filter pencarian."
            icon="calendar" />@else<div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Pesanan</th>
                            <th>Mobil</th>
                            <th>Tanggal sewa</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th>Total biaya</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pesanan as $p)
                            <tr>
                                <td><strong>{{ $p->kode }}</strong><small>{{ $p->pelanggan->nama }}</small></td>
                                <td>{{ $p->mobil->nama() }}<small>{{ $p->mobil->plat_nomor }}</small></td>
                                <td>{{ tanggal_id($p->tgl_ambil) }}<small>s.d. {{ tanggal_id($p->tgl_kembali) }}</small>
                                </td>
                                <td><x-badge-status :status="$p->status" /></td>
                                <td><x-badge-status :status="$p->statusBayar()" /></td>
                                <td>{{ rupiah($p->total_biaya) }}</td>
                                <td><x-button variant="outline" class="compact" :href="route('owner.pesanan.show', $p)">Detail</x-button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
<div class="pagination">{{ $pesanan->links() }}</div>@endsection
