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

<body>@yield('content')</body>

</html>
