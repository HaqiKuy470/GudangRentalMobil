<?php

return [

    /** Nama aplikasi. */
    'name' => env('APP_NAME', 'Laravel'),

    /** Lingkungan aplikasi. */
    'env' => env('APP_ENV', 'production'),

    /** Mode debug. */
    'debug' => (bool) env('APP_DEBUG', false),

    /** URL aplikasi. */
    'url' => env('APP_URL', 'http://localhost'),

    /** Zona waktu. */
    'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),

    /** Bahasa aplikasi. */
    'locale' => env('APP_LOCALE', 'id'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /** Kunci enkripsi. */
    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /** Mode pemeliharaan. */
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
