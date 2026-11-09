<!doctype html>
<html lang="id" data-type="service">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="theme-color" content="#0B2A52">
    <meta name="page-type" content="service">
    <meta name="site-kind" content="other">
    <meta property="og:title" content="Gudang Rental Mobil — Rental mobil di Lamongan">
    <meta property="og:description" content="Pilih mobil, cek tanggal, lalu hubungi kami lewat WhatsApp.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('images/fleet/.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <title>@yield('title', 'Gudang Rental Mobil') — Gudang Rental Mobil</title>
    <meta name="description"
        content="Rental mobil di Lamongan. Pilih mobil, cek ketersediaan tanggal, dan pesan langsung melalui WhatsApp Gudang Rental Mobil.">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])<noscript>
        <style>
            .filter-panel .filters {
                display: grid
            }

            .filter-panel .filter-bottom {
                display: flex
            }
        </style>
    </noscript>
</head>

<body class="site-public">
    <a class="skip-link" href="#konten">Lewati ke konten</a>
    <header class="topnav">
        <div class="container nav-inner"><a class="brand" href="{{ route('beranda') }}"
                aria-label="Gudang Rental Mobil — Beranda"><img src="{{ asset('images/logo.svg') }}"
                    alt="Gudang Rental Mobil" width="157" height="51"></a>
            <nav class="nav-links" id="public-nav" aria-label="Navigasi utama"><a href="{{ route('beranda') }}"
                    class="{{ request()->routeIs('beranda') ? 'active' : '' }}">Beranda</a><a
                    href="{{ route('katalog.index') }}"
                    class="{{ request()->routeIs('katalog.*') ? 'active' : '' }}">Katalog</a><a
                    href="{{ route('tentang') }}"
                    class="{{ request()->routeIs('tentang') ? 'active' : '' }}">Tentang</a></nav>
            @if ($kontakWa)
                <x-button variant="whatsapp" icon="whatsapp" :href="$kontakWa" target="_blank"
                rel="noopener noreferrer">Tanya via WhatsApp</x-button>@else<x-button variant="accent"
                    :href="route('tentang')">Hubungi kami</x-button>
            @endif
            <button type="button" class="btn icon-only nav-toggle" data-toggle="public-nav" aria-controls="public-nav"
                aria-expanded="false" aria-label="Buka menu"><x-icon name="menu" /></button>
        </div>
    </header>
    <main id="konten" class="public-main">@yield('content')</main>
    <footer class="footer">
        <div class="container">
            <div class="footer-main">
                <div><a class="brand" href="{{ route('beranda') }}"><img src="{{ asset('images/logo.svg') }}"
                            alt="Gudang Rental Mobil" width="157" height="51"></a>
                    <p>Sewa mobil lepas kunci atau dengan sopir.<br>Berangkat dari Lamongan.</p>
                </div>
                <div>
                    <h3>Temukan kami</h3>
                    <p>{{ $pengaturan->alamat }}</p>
                    <p style="margin-top:10px">{{ $pengaturan->jam_operasional }}</p>
                </div>
                <div>
                    <h3>Perlu bantuan?</h3>
                    <div class="footer-links"><a href="{{ route('tentang') }}">Informasi & syarat sewa</a>
                        @if ($kontakWa)
                            <a href="{{ $kontakWa }}" target="_blank" rel="noopener noreferrer">WhatsApp
                                {{ $pengaturan->no_whatsapp }}</a>
                        @endif
                        <a href="{{ route('katalog.index') }}">Lihat semua mobil</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom"><span>© {{ date('Y') }} Gudang Rental Mobil.</sp></dianv>
        </div>
    </footer>
    @stack('scripts')
</body>

</html>
