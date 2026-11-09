@extends('layouts.publik')
@section('title', 'Katalog mobil')
@section('content')<div class="container">
        <div class="page-head">
            <div>
                <h1>Katalog mobil</h1>
                <p>Pilih mobil, cek ketersediaan tanggal, lalu pesan lewat WhatsApp.</p>
            </div><span class="small muted">{{ $mobil->count() }} mobil ditemukan</span>
        </div><x-flash />
        <form action="{{ route('katalog.index') }}"
            class="panel filter-panel {{ count(request()->query()) ? 'expanded' : '' }}" id="filters">
            <div class="filter-heading"><strong>Sesuaikan pilihan mobil</strong><button class="btn btn-outline compact"
                    type="button" data-filter-toggle aria-expanded="{{ count(request()->query()) ? 'true' : 'false' }}"
                    aria-controls="filter-fields"><x-icon name="filter" />Filter</button></div>
            <div class="filters" id="filter-fields">
                <div class="filter-search"><x-input name="q" label="Cari mobil" :value="request('q')"
                        placeholder="Cari nama atau merek mobil…" /></div>
                <x-select name="jenis" label="Jenis mobil">
                    <option value="">Semua jenis</option>
                    @foreach (['MPV', 'SUV', 'City Car', 'Sedan', 'Minibus'] as $v)
                        <option value="{{ $v }}" @selected(request('jenis') === $v)>{{ $v }}</option>
                    @endforeach
                </x-select>
                <x-select name="kapasitas" label="Kapasitas">
                    <option value="">Semua kapasitas</option>
                    @foreach ([5, 7, 12] as $v)
                        <option value="{{ $v }}" @selected(request('kapasitas') == $v)>Minimal {{ $v }} kursi
                        </option>
                    @endforeach
                </x-select>
                <x-select name="transmisi" label="Transmisi">
                    <option value="">Semua transmisi</option>
                    @foreach (['manual', 'otomatis'] as $v)
                        <option value="{{ $v }}" @selected(request('transmisi') === $v)>{{ ucfirst($v) }}</option>
                    @endforeach
                </x-select>
                <x-input type="number" name="harga_max" label="Harga maksimal / hari (Rp)" min="0" :value="request('harga_max')"
                    placeholder="Mis. 500000" />
                <x-input type="date" name="tgl_ambil" label="Tanggal ambil" :min="today()->toDateString()" :value="request('tgl_ambil')" />
                <x-input type="date" name="tgl_kembali" label="Tanggal kembali" :min="request('tgl_ambil', today()->toDateString())" :value="request('tgl_kembali')" />
            </div>
            <div class="filter-bottom"><x-button icon="filter">Terapkan filter</x-button><a
                    href="{{ route('katalog.index') }}" class="small muted">Reset filter</a>
                @if (request('tgl_ambil'))
                    <span class="small muted">{{ tanggal_id(request('tgl_ambil')) }} –
                        {{ tanggal_id(request('tgl_kembali')) }}</span>
                @endif
            </div>
        </form>
        @if ($mobil->isEmpty())
            <div class="panel"><x-empty-state title="Belum ada mobil yang cocok"
                    description="Coba ubah jenis, harga, atau tanggal sewa Anda."><x-button variant="outline"
                    :href="route('katalog.index')">Reset filter</x-button></x-empty-state></div>@else<div class="catalog-grid">
                @foreach ($mobil as $item)
                    <x-card-mobil :mobil="$item" />
                @endforeach
            </div>
        @endif
</div>@endsection
