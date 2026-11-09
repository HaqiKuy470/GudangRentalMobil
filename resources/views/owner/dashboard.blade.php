@extends('layouts.owner')
@section('title', 'Dashboard Owner')
@section('content')<div class="page-head">
        <div>
            <h1>Dashboard</h1>
            <p>{{ now()->translatedFormat('l, j F Y') }}</p>
        </div><x-button variant="accent" icon="plus" :href="route('owner.pesanan.create')">Catat pesanan</x-button>
    </div>
    <div class="grid four">
        @foreach ([['Pesanan aktif', $aktif, 'calendar', 'Dipesan dan sedang disewa'], ['Mobil sedang disewa', $disewa, 'car', 'Berdasarkan status pesanan'], ['Kembali hari ini', $kembali, 'clock', 'Jadwal pengembalian hari ini'], ['Pendapatan bulan ini', rupiah($pendapatan), 'wallet', 'Pembayaran yang sudah diterima']] as [$label, $value, $icon, $desc])
            <div class="stat-card">
                <div class="stat-label"><span>{{ $label }}</span><span class="icon-box"><x-icon
                            :name="$icon" /></span></div>
                <div class="stat-value" style="{{ $icon === 'wallet' ? 'font-size:22px' : '' }}">{{ $value }}</div>
                <p class="small muted">{{ $desc }}</p>
            </div>
        @endforeach
    </div>
    <div class="dashboard-lower">
        <section class="panel">
            <div class="section-heading" style="margin-bottom:18px">
                <h3 style="margin:0">Jadwal ambil / kembali 7 hari ke depan</h3>
            </div>
            @if ($jadwal->isEmpty())
                <x-empty-state title="Belum ada jadwal"
                description="Jadwal pengambilan dan pengembalian mobil akan tampil di sini." icon="calendar" />@else<div
                    class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Mobil</th>
                                <th>Pelanggan</th>
                                <th>Kegiatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jadwal as $p)
                                @foreach ([['Ambil', $p->tgl_ambil], ['Kembali', $p->tgl_kembali]] as [$kegiatan, $tanggal])
                                    @if ($tanggal->betweenIncluded(today(), today()->addDays(7)))
                                        <tr>
                                            <td>{{ $tanggal->translatedFormat('j M') }}</td>
                                            <td><a
                                                    href="{{ route('owner.pesanan.show', $p) }}"><strong>{{ $p->mobil->nama() }}</strong></a>
                                            </td>
                                            <td>{{ $p->pelanggan->nama }}</td>
                                            <td><span
                                                    class="badge {{ $kegiatan === 'Ambil' ? 'dipesan' : 'disewa' }}">{{ $kegiatan }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            <div style="margin-top:18px">
                <a href="{{ route('owner.pesanan.index') }}" class="small muted">Lihat semua pesanan</a>
            </div>
        </section>
        <section class="panel">
            <h3>Pesanan belum lunas</h3>
            <div class="debt-total">
                <div class="small muted">Total sisa tagihan</div>
                <div class="price">{{ rupiah($belumLunas->sum(fn($p) => $p->sisaTagihan())) }}</div>
            </div>
            @forelse($belumLunas->take(5) as $p)
                <a class="debt-item" href="{{ route('owner.pesanan.show', $p) }}"><span><small
                        class="muted">{{ $p->kode }}</small><br>{{ $p->pelanggan->nama }}</span><strong>{{ rupiah($p->sisaTagihan()) }}</strong></a>@empty
                <p class="small muted">Semua tagihan sudah lunas.</p>
            @endforelse
        </section>
</div>@endsection
