@extends('layouts.owner')
@section('title', $pesanan->kode)
@section('content')<div class="page-head">
        <div>
            <div class="actions">
                <h1>{{ $pesanan->kode }}</h1><x-badge-status :status="$pesanan->status" />
            </div>
            <p>Dicatat {{ tanggal_id($pesanan->created_at) }} · {{ $pesanan->pelanggan->nama }}</p>
        </div>
        @if ($pesanan->status->value === 'dipesan')
            <x-button variant="outline" icon="edit" :href="route('owner.pesanan.edit', $pesanan)">Ubah pesanan</x-button>
        @endif
    </div>
    <div class="record-grid">
        <div class="stack">
            <section class="panel">
                <h3>Ringkasan pesanan</h3>
                <div class="summary-list">
                    <div class="summary-row"><span>Mobil</span><strong>{{ $pesanan->mobil->nama() }}<small class="muted"> ·
                                {{ $pesanan->mobil->plat_nomor }}</small></strong></div>
                    <div class="summary-row"><span>Pelanggan</span><a
                            href="{{ route('owner.pelanggan.show', $pesanan->pelanggan) }}"><strong>{{ $pesanan->pelanggan->nama }}</strong></a>
                    </div>
                    <div class="summary-row"><span>Nomor HP</span><strong>{{ $pesanan->pelanggan->no_hp }}</strong></div>
                    <div class="summary-row"><span>Tanggal sewa</span><strong>{{ tanggal_id($pesanan->tgl_ambil) }} –
                            {{ tanggal_id($pesanan->tgl_kembali) }}<br>{{ $pesanan->lama_sewa }} hari</strong></div>
                    <div class="summary-row"><span>Jenis sewa</span><strong>{{ $pesanan->jenis_sewa->label() }}</strong>
                    </div>
                    @if ($pesanan->tgl_dikembalikan)
                        <div class="summary-row">
                            <span>Dikembalikan</span><strong>{{ tanggal_id($pesanan->tgl_dikembalikan) }}</strong></div>
                    @endif
                    <div>
                        <span class="small muted">Catatan</span>
                        <p>{{ $pesanan->catatan ?: 'Tidak ada catatan.' }}</p>
                    </div>
                </div>
                @if ($pesanan->status->value === 'dipesan')
                    <div class="status-form">
                        <h3>Perbarui status</h3>
                        <div class="actions">
                            <form method="POST" action="{{ route('owner.pesanan.status', $pesanan) }}"
                                data-confirm="Tandai mobil sudah diambil dan sedang disewa?">@csrf @method('PATCH')<input
                                    type="hidden" name="status" value="disewa"><x-button icon="car">Tandai sedang
                                    disewa</x-button></form>
                            <form method="POST" action="{{ route('owner.pesanan.status', $pesanan) }}"
                                data-confirm="Batalkan pesanan ini? Tanggal sewa akan kembali tersedia.">@csrf
                                @method('PATCH')<input type="hidden" name="status" value="batal"><x-button
                                    variant="outline">Batalkan pesanan</x-button></form>
                        </div>
                    </div>
                @elseif($pesanan->status->value === 'disewa')
                    <form class="status-form" method="POST" action="{{ route('owner.pesanan.status', $pesanan) }}">@csrf
                        @method('PATCH')<input type="hidden" name="status" value="selesai">
                        <h3>Catat pengembalian</h3>
                        <div class="form-grid"><x-input name="tgl_dikembalikan" label="Tanggal dikembalikan" type="date"
                                :min="$pesanan->tgl_ambil->toDateString()" :max="today()->toDateString()" :value="today()->toDateString()" required /><x-input name="denda"
                                label="Denda keterlambatan (Rp)" type="number" min="0" value="0" /></div>
                        @if (today()->gt($pesanan->tgl_kembali))
                            <p class="help text-orange">Jadwal kembali telah terlewati. Periksa keterlambatan dan isi denda
                                sesuai kesepakatan.</p>
                        @endif
                        <x-button style="margin-top:16px" icon="check">
                            Tandai selesai</x-button>
                    </form>
                @endif
            </section>
            <section class="panel">
                <h3>Riwayat pembayaran</h3>
                @if ($pesanan->pembayaran->isEmpty())
                <p class="muted">Belum ada pembayaran yang dicatat.</p>@else<div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Metode</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pesanan->pembayaran as $bayar)
                                    <tr>
                                        <td>{{ tanggal_id($bayar->tgl_bayar) }}</td>
                                        <td>{{ $bayar->jenis->label() }}</td>
                                        <td>{{ $bayar->metode->label() }}</td>
                                        <td><strong>{{ rupiah($bayar->jumlah) }}</strong></td>
                                        <td>
                                            <form method="POST" action="{{ route('owner.pembayaran.destroy', $bayar) }}"
                                                data-confirm="Hapus catatan pembayaran ini? Sisa tagihan akan dihitung ulang.">
                                                @csrf @method('DELETE')<button class="btn btn-outline compact"
                                                    aria-label="Hapus catatan pembayaran"><x-icon name="bin" /></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
        <aside class="stack">
            <section class="panel">
                <h3>Rincian biaya</h3>
                <div class="costs">
                    <div class="cost-row"><span>{{ rupiah($pesanan->harga_per_hari) }} × {{ $pesanan->lama_sewa }}
                            hari</span><strong>{{ rupiah($pesanan->harga_per_hari * $pesanan->lama_sewa) }}</strong></div>
                    <div class="cost-row"><span>Biaya sopir</span><strong>{{ rupiah($pesanan->biaya_sopir) }}</strong>
                    </div>
                    <div class="cost-row"><span>Denda</span><strong>{{ rupiah($pesanan->denda) }}</strong></div>
                    <div class="cost-total"><strong>Total biaya</strong><span
                            class="price">{{ rupiah($pesanan->total_biaya) }}</span></div>
                    <div class="cost-row"><span>Sudah dibayar</span><strong>{{ rupiah($pesanan->terbayar()) }}</strong>
                    </div>
                </div>
                <div class="debt-total"><span
                        class="small muted">{{ $pesanan->sisaTagihan() < 0 ? 'Kelebihan pembayaran' : 'Sisa tagihan' }}</span>
                    <div class="price">{{ rupiah(abs($pesanan->sisaTagihan())) }}</div>
                </div><x-badge-status :status="$pesanan->statusBayar()" />
            </section>
            <section class="panel">
                <h3>Catat pembayaran</h3>
                <form method="POST" action="{{ route('owner.pembayaran.store', $pesanan) }}">@csrf<div class="stack"
                        style="gap:15px"><x-select name="jenis" label="Jenis pembayaran" required>
                            @foreach (\App\Enums\JenisPembayaran::cases() as $v)
                                <option value="{{ $v->value }}" @selected(old('jenis') === $v->value)>{{ $v->label() }}
                                </option>
                            @endforeach
                        </x-select>
                        <x-input name="jumlah" label="Jumlah (Rp)" type="number" min="1" required /><x-select
                            name="metode" label="Metode" required>
                            @foreach (\App\Enums\MetodePembayaran::cases() as $v)
                                <option value="{{ $v->value }}" @selected(old('metode') === $v->value)>{{ $v->label() }}
                                </option>
                            @endforeach
                        </x-select><x-input name="tgl_bayar" label="Tanggal bayar" type="date" :max="today()->toDateString()"
                            :value="today()->toDateString()" required /><x-button class="full">Simpan pembayaran</x-button>
                    </div>
                </form>
            </section>
        </aside>
</div>@endsection
