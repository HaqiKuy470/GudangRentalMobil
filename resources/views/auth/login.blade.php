@extends('layouts.auth')
@section('title', 'Login Owner')
@section('content')<div class="login-page">
        <section class="login-story"><a href="{{ route('beranda') }}"><img src="{{ asset('images/logo.svg') }}"
                    alt="Gudang Rental Mobil"></a>
            <h1>Semua perjalanan,<br>tercatat dengan baik.</h1>
            <p>Kelola armada, pantau jadwal, dan catat transaksi rental Anda dari satu tempat.</p>
            <div class="hero-proof"><span><x-icon name="shield" />Akses khusus Owner</span></div>
        </section>
        <main class="login-form-wrap">
            <div class="login-form">
                <div class="icon-box" style="margin-bottom:24px"><x-icon name="shield" /></div>
                <h1>Selamat datang kembali</h1>
                <p>Masuk untuk mengelola Gudang Rental Mobil.</p><x-flash />
                <form action="{{ route('login.store') }}" method="POST">@csrf<x-input name="email" label="Email Owner"
                        type="email" autocomplete="username" required autofocus /><x-input name="password"
                        label="Password" type="password" autocomplete="current-password" required /><x-button
                        class="full">Masuk ke dashboard<x-icon name="arrow" /></x-button></form><a
                    href="{{ route('beranda') }}" class="login-back">Kembali ke website publik</a>
            </div>
        </main>
</div>@endsection
