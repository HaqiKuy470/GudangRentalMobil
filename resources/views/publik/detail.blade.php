@extends('layouts.publik')
@section('title', $mobil->nama())
@section('content')<div class="container">
        <div class="breadcrumb"><a href="{{ route('katalog.index') }}">Katalog</a><x-icon
                name="chevron" /><span>{{ $mobil->nama() }}</span></div><x-flash />
        <div class="detail-grid">
            <div class="stack">
                <div class="detail-photo"><img src="{{ $mobil->fotoUrl() }}" alt="{{ $mobil->nama() . ' ' . $mobil->tahun }}"
                        data-source="{{ $mobil->foto ? 'client' : 'stand-in' }}" width="600" height="420"></div>
                @if (!$mobil->foto)
                    <p class="small muted" style="margin-top:-12px">Foto referensi model. Warna, tipe, dan kondisi unit
                        dikonfirmasi melalui WhatsApp.</p>
                @endif
                <section class="panel">
                    <h3>Spesifikasi</h3>
                    <div class="spec-grid">
                        @foreach ([['car', 'Jenis', $mobil->jenis], ['users', 'Kapasitas', $mobil->kapasitas . ' kursi'], ['gear', 'Transmisi', ucfirst($mobil->transmisi)]] as [$icon, $label, $value])
                            <div class="spec"><x-icon
                                    :name="$icon" /><small>{{ $label }}</small><strong>{{ $value }}</strong>
                            </div>
                        @endforeach
                    </div>
                    <p class="muted" style="margin-top:20px">{{ $mobil->deskripsi }}</p>
                </section>
                @if (count($tanggalTerpakai))
                    <section class="panel">
                        <h3>Jadwal yang sudah terisi</h3>
                        <div class="stack" style="gap:8px">
                            @foreach ($tanggalTerpakai as $tanggal)
                                <div class="small muted actions"><x-icon
                                        name="calendar" />{{ tanggal_id($tanggal['ambil']) }} –
                                    {{ tanggal_id($tanggal['kembali']) }}</div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
            <div class="detail-copy">
                <div class="actions" style="justify-content:space-between">
                    <h1>{{ $mobil->nama() }}</h1><x-badge-status :status="$mobil->statusHariIni()" />
                </div>
                <p class="muted">{{ $mobil->tahun }} · {{ $mobil->plat_nomor }}</p>
                <p class="price">{{ $mobil->harga_per_hari !== null ? rupiah($mobil->harga_per_hari) : 'Hubungi untuk harga' }}@if ($mobil->harga_per_hari !== null)<small> / hari</small>@endif</p>
                <p class="small muted">{{ $mobil->harga_per_hari !== null ? 'Harga sewa mobil. Biaya sopir dihitung terpisah jika dipilih.' : 'Tanyakan harga sewa langsung melalui WhatsApp.' }}</p>
                <section class="panel booking {{ $lama ? 'has-dates' : '' }}" id="booking">
                    <h3>Rencanakan sewa Anda</h3>
                    <form method="GET" action="{{ route('katalog.show', $mobil) }}" data-estimate-form
                        data-estimate-url="{{ route('katalog.estimasi', $mobil) }}" data-inquiry-url="{{ $tanya }}">
                        <div class="form-grid"><x-input name="tgl_ambil" label="Tanggal ambil" type="date"
                                :value="request('tgl_ambil')" :min="today()->toDateString()" required /><x-input name="tgl_kembali"
                                label="Tanggal kembali" type="date" :value="request('tgl_kembali')" :min="request('tgl_ambil', today()->toDateString())" required /></div>
                        <fieldset style="margin:18px 0 0;border:0;padding:0">
                            <legend class="small muted" style="margin-bottom:9px">Jenis sewa</legend>
                            <div class="radio-options">
                                @foreach (\App\Enums\JenisSewa::cases() as $tipe)
                                    <label class="radio-option"><input type="radio" name="jenis_sewa"
                                            value="{{ $tipe->value }}"
                                            @checked($jenis === $tipe)><span><strong>{{ $tipe->label() }}</strong><small>{{ $tipe->value === 'lepas_kunci' ? 'Kemudikan sendiri' : '+' . rupiah($pengaturan->biaya_sopir_per_hari) . '/hari' }}</small></span></label>
                                @endforeach
                            </div>
                        </fieldset><button class="btn btn-outline full" style="margin-top:16px" type="submit">Cek
                            ketersediaan
                            {{ $mobil->harga_per_hari !== null ? '& estimasi' : '' }}</button>
                    </form>
                    <div id="availability" role="status"
                        class="availability {{ $tersedia === false ? 'error' : ($tersedia === null ? 'neutral' : '') }}">
                        {{ $tersedia === true ? 'Tersedia pada tanggal pilihan Anda.' : ($tersedia === false ? ($mobil->status->value === 'perawatan' ? 'Mobil sedang perawatan. Pilih mobil lain.' : 'Mobil sudah dipesan pada tanggal tersebut. Coba tanggal lain.') : 'Pilih tanggal sewa terlebih dahulu.') }}
                    </div>
                    <div class="costs" aria-live="polite">
                        <div class="cost-row"><span>Sewa mobil <span
                                    id="estimate-days">{{ $lama ? '· ' . $lama . ' hari' : '' }}</span></span><strong
                                id="estimate-rent">{{ $biaya ? rupiah($biaya->subtotalSewa) : ($mobil->harga_per_hari === null ? 'Hubungi untuk harga' : '—') }}</strong></div>
                        <div class="cost-row"><span>Biaya sopir</span><strong
                                id="estimate-driver">{{ $biaya ? rupiah($biaya->biayaSopir) : '—' }}</strong></div>
                        <div class="cost-total"><strong>Estimasi total</strong><span class="price"
                                id="estimate-total">{{ $biaya ? rupiah($biaya->total) : ($mobil->harga_per_hari === null ? 'Hubungi untuk harga' : '—') }}</span></div>
                    </div>
                    <div class="booking-cta"><a id="booking-wa" class="btn btn-whatsapp full"
                            @if ($wa) href="{{ $wa }}" target="_blank" rel="noopener noreferrer" @else aria-disabled="true" tabindex="-1" @endif><x-icon
                                name="whatsapp" />{{ $mobil->harga_per_hari === null ? 'Tanya harga via WhatsApp' : 'Pesan via WhatsApp' }}</a></div>
                    <p class="booking-note"><x-icon name="info" /><span>Harga final dan ketersediaan dikonfirmasi Owner
                            lewat WhatsApp. Pembayaran dilakukan di luar sistem.</span></p>
                    @if (!$kontakWa)
                        <p class="help">Kontak WhatsApp belum tersedia. Silakan lihat alamat usaha di halaman Tentang.</p>
                    @endif
                </section>
            </div>
        </div>
</div>@endsection
