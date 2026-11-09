@extends('layouts.owner')
@section('title', 'Pelanggan')
@section('content')<div class="page-head">
        <div>
            <h1>Pelanggan</h1>
            <p>Data kontak dan riwayat sewa pelanggan Anda.</p>
        </div><x-button variant="accent" icon="plus" :href="route('owner.pelanggan.create')">Tambah pelanggan</x-button>
    </div>
    <div class="toolbar">
        <form method="GET" class="search-form"><input class="search-input" name="q" aria-label="Cari pelanggan"
                placeholder="Cari nama atau nomor HP…" value="{{ request('q') }}"><x-button icon="search">Cari</x-button>
        </form>
    </div>
    <section class="panel table-panel">
        @if ($pelanggan->isEmpty())
            <x-empty-state title="Pelanggan tidak ditemukan"
            description="Tambahkan pelanggan baru atau gunakan kata pencarian lain." icon="users" />@else<div
                class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Nomor HP</th>
                            <th>Jumlah pesanan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pelanggan as $p)
                            <tr>
                                <td><strong>{{ $p->nama }}</strong></td>
                                <td>{{ $p->no_hp }}</td>
                                <td>{{ $p->pesanan_count }} pesanan</td>
                                <td><x-button variant="outline" class="compact" :href="route('owner.pelanggan.show', $p)">Lihat detail</x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
<div class="pagination">{{ $pelanggan->links() }}</div>@endsection
