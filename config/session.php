<?php

use Illuminate\Support\Str;

return [

    /** Penyimpanan sesi utama. */
    'driver' => env('SESSION_DRIVER', 'database'),

    /** Masa berlaku sesi. */
    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    /** Enkripsi sesi. */
    'encrypt' => env('SESSION_ENCRYPT', false),

    /** Lokasi berkas sesi. */
    'files' => storage_path('framework/sessions'),

    /** Koneksi database sesi. */
    'connection' => env('SESSION_CONNECTION'),

    /** Tabel sesi. */
    'table' => env('SESSION_TABLE', 'sessions'),

    /** Penyimpanan cache sesi. */
    'store' => env('SESSION_STORE'),

    /** Pembersihan sesi kedaluwarsa. */
    'lottery' => [2, 100],

    /** Nama cookie sesi. */
    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug((string) env('APP_NAME', 'laravel')).'-session'
    ),

    /** Cakupan path cookie. */
    'path' => env('SESSION_PATH', '/'),

    /** Domain cookie. */
    'domain' => env('SESSION_DOMAIN'),

    /** Cookie hanya melalui HTTPS. */
    'secure' => env('SESSION_SECURE_COOKIE'),

    /** Cegah akses cookie melalui JavaScript. */
    'http_only' => env('SESSION_HTTP_ONLY', true),

    /** Aturan cookie lintas situs. */
    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    /** Cookie terpartisi. */
    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

    /** Format penyimpanan sesi. */
    'serialization' => 'json',

];
