@extends('layouts.owner')
@section('title', 'Kelola mobil')
@section('content')<div class="page-head">
        <div>
            <h1>Mobil</h1>
            <p>Kelola armada, harga sewa, dan status perawatan.</p>
        </div><x-button variant="accent" icon="plus" :href="route('owner.mobil.create')">Tambah mobil</x-button>
    </div>
    <section class="panel table-panel">
        @if ($mobil->isEmpty())
            <x-empty-state title="Belum ada mobil"
                description="Tambahkan mobil pertama agar pelanggan dapat melihatnya di katalog."><x-button
                :href="route('owner.mobil.create')">Tambah mobil pertama</x-button></x-empty-state>@else<div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Mobil</th>
                            <th>Plat nomor</th>
                            <th>Spesifikasi</th>
                            <th>Harga / hari</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mobil as $m)
                            <tr>
                                <td><strong>{{ $m->nama() }}</strong><small>{{ $m->tahun }}</small></td>
                                <td>{{ $m->plat_nomor }}</td>
                                <td>{{ $m->kapasitas }} kursi · {{ ucfirst($m->transmisi) }}</td>
                                <td>{{ $m->harga_per_hari !== null ? rupiah($m->harga_per_hari) : 'Hubungi untuk harga' }}</td>
                                <td><x-badge-status :status="$m->status" /></td>
                                <td>
                                    <div class="actions"><x-button variant="outline" class="compact"
                                            :href="route('owner.mobil.edit', $m)">Ubah</x-button>
                                        <form method="POST" action="{{ route('owner.mobil.destroy', $m) }}"
                                            data-confirm="Hapus mobil ini dari katalog? Riwayat pesanan tetap tersimpan.">
                                            @csrf @method('DELETE')<button class="btn btn-outline compact"
                                                aria-label="Hapus {{ $m->nama() }}"><x-icon name="bin" /></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
<div class="pagination">{{ $mobil->links() }}</div>@endsection
