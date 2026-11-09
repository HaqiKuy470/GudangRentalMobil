@props(['mobil'])
@php($status = isset($mobil->tersedia_pilihan) && !$mobil->tersedia_pilihan && request('tgl_ambil') ? 'tidak-tersedia' : $mobil->statusHariIni())
<article class="car-card {{ $status === 'tidak-tersedia' ? 'unavailable' : '' }}">
    <div class="car-image"><img src="{{ $mobil->fotoUrl() }}" alt="{{ $mobil->nama() . ' ' . $mobil->tahun }}"
            data-source="{{ $mobil->foto ? 'client' : 'stand-in' }}" loading="lazy" width="420"
            height="300"><x-badge-status :status="$status" /></div>
    <div class="car-content">
        <div class="car-title">
            <h3><a class="car-link"
                    href="{{ route('katalog.show', [$mobil] + request()->only('tgl_ambil', 'tgl_kembali')) }}">{{ $mobil->nama() }}</a>
            </h3><span class="car-year">{{ $mobil->tahun }}</span>
        </div>
        <div class="car-meta"><span class="chip">{{ $mobil->jenis }}</span><span class="chip"><x-icon
                    name="users" />{{ $mobil->kapasitas }} kursi</span><span
                class="chip">{{ ucfirst($mobil->transmisi) }}</span></div>
        <div class="car-bottom">
            <p class="price">{{ $mobil->harga_per_hari !== null ? rupiah($mobil->harga_per_hari) : 'Hubungi untuk harga' }}@if ($mobil->harga_per_hari !== null)<small> / hari</small>@endif</p><x-button variant="outline"
                class="compact" :href="route('katalog.show', [$mobil] + request()->only('tgl_ambil', 'tgl_kembali'))">Lihat detail<x-icon name="arrow" /></x-button>
        </div>
    </div>
</article>
