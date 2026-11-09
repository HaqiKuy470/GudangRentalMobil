@extends('layouts.owner')
@section('title', $pelanggan->nama)
@section('content')<div class="page-head">
        <div>
            <h1>{{ $pelanggan->nama }}</h1>
            <p>Detail kontak dan riwayat sewa.</p>
        </div>
        <div class="actions"><x-button variant="outline" :href="route('owner.pelanggan.edit', $pelanggan)" icon="edit">Ubah kontak</x-button>
            <form method="POST" action="{{ route('owner.pelanggan.destroy', $pelanggan) }}"
                data-confirm="Hapus kontak pelanggan ini?">@csrf @method('DELETE')<x-button variant="danger"
                    icon="bin">Hapus</x-button></form>
        </div>
    </div>
    <div class="record-grid">
        <section class="panel">
            <h3>Riwayat sewa</h3>
            @if ($pelanggan->pesanan->isEmpty())
                <x-empty-state title="Belum ada riwayat sewa" description="Pesanan pelanggan akan tampil di sini."
                icon="calendar" />@else<div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Pesanan</th>
                                <th>Mobil</th>
                                <th>Tanggal ambil</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pelanggan->pesanan as $p)
                                <tr>
                                    <td><a
                                            href="{{ route('owner.pesanan.show', $p) }}"><strong>{{ $p->kode }}</strong></a>
                                    </td>
                                    <td>{{ $p->mobil->nama() }}</td>
                                    <td>{{ tanggal_id($p->tgl_ambil) }}</td>
                                    <td><x-badge-status :status="$p->status" /></td>
                                    <td>{{ rupiah($p->total_biaya) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
        <aside class="panel">
            <h3>Informasi pelanggan</h3>
            <div class="summary-list">
                @foreach (['no_hp' => 'Nomor HP', 'alamat' => 'Alamat', 'no_identitas' => 'Nomor identitas', 'catatan' => 'Catatan'] as $key => $label)
                    <div>
                        <p class="small muted">{{ $label }}</p>
                        <p style="overflow-wrap:anywhere">{{ $pelanggan->$key ?: '—' }}</p>
                    </div>
                @endforeach
            </div>
        </aside>
</div>@endsection
