@extends('layouts.owner')
@section('title', 'Laporan transaksi')
@section('content')<div class="page-head">
        <div>
            <h1>Laporan transaksi</h1>
            <p>Rekap pesanan dan pembayaran untuk periode pilihan.</p>
        </div><x-button variant="accent" icon="download" :href="route('owner.laporan.pdf', compact('awal', 'akhir'))">Unduh PDF</x-button>
    </div>
    <form class="panel" method="GET" style="margin-bottom:24px">
        <div class="form-grid"><x-input name="awal" label="Tanggal awal" type="date" :value="$awal" required /><x-input
                name="akhir" label="Tanggal akhir" type="date" :value="$akhir" required /></div><x-button
            style="margin-top:18px" icon="filter">Tampilkan laporan</x-button>
    </form>
    <div class="grid four" style="margin-bottom:24px">
        <div class="stat-card">
            <p class="small muted">Total penerimaan</p>
            <div class="stat-value" style="font-size:24px">{{ rupiah($total) }}</div>
        </div>
        @foreach (['DP', 'Pelunasan', 'Denda'] as $jenis)
            <div class="stat-card">
                <p class="small muted">{{ $jenis }}</p>
                <div class="stat-value" style="font-size:24px">{{ rupiah($perJenis->get($jenis, 0)) }}</div>
            </div>
        @endforeach
    </div>
    <div class="stack">
        <section class="panel">
            <h3>Pesanan dalam periode</h3>
            <p class="help" style="margin-bottom:16px">Pesanan difilter berdasarkan tanggal ambil.</p>
            @if ($pesanan->isEmpty())
            <p class="muted">Belum ada pesanan pada periode ini.</p>@else<div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Pelanggan</th>
                                <th>Mobil</th>
                                <th>Tanggal ambil</th>
                                <th>Status</th>
                                <th>Total biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pesanan as $p)
                                <tr>
                                    <td><a
                                            href="{{ route('owner.pesanan.show', $p) }}"><strong>{{ $p->kode }}</strong></a>
                                    </td>
                                    <td>{{ $p->pelanggan->nama }}</td>
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
        <section class="panel">
            <h3>Pembayaran diterima</h3>
            <p class="help" style="margin-bottom:16px">Penerimaan dihitung berdasarkan tanggal pembayaran, termasuk
                pembayaran pesanan di luar periode ambil.</p>
            @if ($pembayaran->isEmpty())
            <p class="muted">Belum ada pembayaran pada periode ini. Total {{ rupiah(0) }}.</p>@else<div
                    class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal bayar</th>
                                <th>Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Jenis</th>
                                <th>Metode</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembayaran as $p)
                                <tr>
                                    <td>{{ tanggal_id($p->tgl_bayar) }}</td>
                                    <td>{{ $p->pesanan->kode }}</td>
                                    <td>{{ $p->pesanan->pelanggan->nama }}</td>
                                    <td>{{ $p->jenis->label() }}</td>
                                    <td>{{ $p->metode->label() }}</td>
                                    <td>{{ rupiah($p->jumlah) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
</div>@endsection
