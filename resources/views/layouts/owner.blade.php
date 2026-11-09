<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0B2A52">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <title>@yield('title', 'Gudang Rental Mobil') — Gudang Rental Mobil</title>
    <meta name="description"
        content="Rental mobil di Lamongan. Pilih mobil, cek ketersediaan tanggal, dan pesan langsung melalui WhatsApp Gudang Rental Mobil.">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="robots" content="noindex,nofollow">
</head>

<body>
    <a class="sr-only" href="#konten">Lewati ke konten</a>
    <div class="owner-shell">
        <div class="sidebar-overlay" id="sidebar-overlay"></div>
        <aside class="sidebar" id="owner-sidebar"><button type="button" class="mobile-close" data-sidebar-close
                aria-label="Tutup menu"><x-icon name="close" /></button><a class="brand"
                href="{{ route('owner.dashboard') }}"><span class="brand-mark"><x-icon name="car" /></span><span>GRM
                    Owner<small>Gudang Rental Mobil</small></span></a>
            <nav class="side-nav" aria-label="Menu Owner">
                @foreach ([['owner.dashboard', 'dashboard', 'Dashboard'], ['owner.pesanan.index', 'calendar', 'Pesanan'], ['owner.mobil.index', 'car', 'Mobil'], ['owner.pelanggan.index', 'users', 'Pelanggan'], ['owner.laporan.index', 'file', 'Laporan'], ['owner.pengaturan.edit', 'gear', 'Pengaturan']] as [$route, $icon, $label])
                    <a href="{{ route($route) }}"
                        class="{{ request()->routeIs($route === 'owner.dashboard' ? $route : substr($route, 0, strrpos($route, '.')) . '.*') ? 'active' : '' }}"><x-icon
                            :name="$icon" />{{ $label }}</a>
                @endforeach
            </nav>
            <div class="sidebar-bottom"><a href="{{ route('beranda') }}" class="actions small"><x-icon
                        name="arrow" />Lihat website publik</a><small style="margin-top:16px">GERAK · Panel
                    pengelola</small></div>
        </aside>
        <div class="owner-area">
            <header class="owner-topbar">
                <div class="actions"><button class="btn btn-outline icon-only owner-menu" type="button"
                        data-sidebar-open aria-controls="owner-sidebar" aria-expanded="false"
                        aria-label="Buka menu Owner"><x-icon name="menu" /></button><span
                        class="topbar-name muted">Panel pengelola</span></div>
                <div class="user"><span
                        class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span><span>{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"
                            class="btn btn-outline compact" aria-label="Logout"><x-icon
                                name="logout" /><span>Keluar</span></button></form>
                </div>
            </header>
            <main class="owner-main" id="konten"><x-flash />@yield('content')</main>
        </div>
    </div>@stack('scripts')
</body>

</html>
