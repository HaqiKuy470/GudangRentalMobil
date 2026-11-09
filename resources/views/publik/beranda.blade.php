@extends('layouts.publik')
@section('title', 'Sewa mobil di Lamongan')
@section('content')
    <section class="rental-hero" data-enter>
        <div class="container">
            <div class="rental-hero-top"><span>Gudang Rental Mobil</span><span><x-icon name="pin" /> Lamongan, Jawa
                    Timur</span></div>
            <div class="rental-hero-grid">
                <div class="rental-hero-copy">
                    <h1>Sewa mobil.<br>Berangkat dari<br>Lamongan.</h1>
                    <p>Untuk agenda kerja, pulang kampung, atau jalan bersama keluarga. Pilih mobilnya, tentukan tanggalnya.
                    </p>
                    <x-button variant="accent" :href="route('katalog.index')">Lihat katalog mobil<x-icon name="arrow" /></x-button>
                    <a class="rental-text-link" href="#cara-sewa">Bagaimana cara sewanya? <span>↓</span></a>
                </div>
                <div class="rental-hero-photo">
                    <img src="{{ $mobil->first()?->fotoUrl() ?? asset('images/fleet/image.png') }}"
                        alt="{{ $mobil->first()?->nama() ?? 'Toyota Avanza' }}" width="1280" height="960"
                        fetchpriority="high" data-source="{{ $mobil->first()?->foto ? 'client' : 'stand-in' }}">
                </div>
            </div>
            <div class="rental-hero-foot"><span>Lepas kunci / Dengan sopir</span><span>Cek tanggal di website, konfirmasi
                    lewat WhatsApp.</span><a href="#armada" aria-label="Lihat pilihan armada">↓</a></div>
        </div>
    </section>
    <section class="rental-fleet" id="armada">
        <div class="container">
            <div class="section-heading">
                <div>
                    <p class="section-kicker">Pilihan armada</p>
                    <h2>Mobil untuk rencana Anda.</h2>
                </div>
                <a class="rental-text-link" href="{{ route('katalog.index') }}">Lihat semua mobil <x-icon
                        name="arrow" /></a>
            </div>
            <div class="catalog-grid">
                @forelse ($mobil as $item)
                    <x-card-mobil :mobil="$item" />
                @empty
                    <div class="span-2"><x-empty-state title="Armada sedang disiapkan"
                            description="Hubungi kami untuk informasi pilihan mobil."><x-button :href="route('tentang')">Informasi
                                usaha</x-button></x-empty-state></div>
                @endforelse
            </div>
        </div>
    </section>
    <section class="rental-how" id="cara-sewa">
        <div class="container rental-how-grid">
            <div>
                <p class="section-kicker">Cara sewa</p>
                <h2>Dari pilih mobil<br>sampai ambil kunci.</h2>
                <p class="rental-how-note">Pemesanan dikonfirmasi langsung dengan Owner. Anda tidak perlu membuat akun.</p>
                <a class="rental-text-link" href="{{ route('tentang') }}">Baca syarat sewa <x-icon name="arrow" /></a>
            </div>
            <ol class="rental-steps">
                <li><span>01</span>
                    <div>
                        <h3>Pilih mobil</h3>
                        <p>Bandingkan harga, jumlah kursi, dan transmisi di katalog.</p>
                    </div>
                </li>
                <li><span>02</span>
                    <div>
                        <h3>Tentukan tanggal</h3>
                        <p>Isi tanggal ambil dan kembali. Pilih lepas kunci atau dengan sopir untuk melihat estimasi biaya.
                        </p>
                    </div>
                </li>
                <li><span>03</span>
                    <div>
                        <h3>Konfirmasi lewat WhatsApp</h3>
                        <p>Kirim rincian pilihan Anda, lalu sepakati harga akhir, persyaratan, dan waktu pengambilan.</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>
    <section class="rental-contact">
        <div class="container">
            <div>
                <p class="section-kicker">Ada yang ingin ditanyakan?</p>
                <h2>Bicarakan rencana<br>perjalanan Anda.</h2>
            </div>
            @if ($kontakWa)
                <x-button variant="whatsapp" icon="whatsapp" :href="$kontakWa" target="_blank"
                    rel="noopener noreferrer">Tanya via WhatsApp</x-button>
            @else
                <x-button :href="route('tentang')">Lihat kontak usaha</x-button>
            @endif
        </div>
    </section>
@endsection
