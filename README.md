# Gudang Rental Mobil — GERAK

Website katalog dan pencatatan rental mobil berdasarkan `../SKPL_01_GERAK_revisi.docx`. Frontend mengikuti mockup Figma GRM yang diberikan pemilik proyek. Pelanggan tanpa akun; transaksi disepakati melalui tautan WhatsApp. Owner mencatat pesanan dan pembayaran secara manual.

## Menjalankan aplikasi

Root aplikasi: `D:/GudangRentalMobil/GudangRentalMobil`. Gunakan PHP 8.4 atau lebih baru, Composer, Node.js, dan MySQL. Pada komputer ini server diuji dengan PHP 8.5.12 yang sudah tersedia di Laragon; PHP bawaan PATH masih 8.3.33.

```powershell
# Jika terminal masih memakai PHP 8.3, pilih PHP yang tersedia untuk sesi ini:
$env:Path = 'D:/laragon/bin/php/php-8.5.12-nts-Win32-vs17-x64;' + $env:Path
composer install
npm ci
# Untuk instalasi baru, salin .env.example ke .env lalu isi konfigurasi database.
# Jangan menimpa .env yang sudah berisi konfigurasi lokal.
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

Buka http://127.0.0.1:8000. Katalog di `/mobil`, login Owner di `/login`, dashboard di `/owner`.

## Akun Owner dan konfigurasi usaha

Seeder membaca `OWNER_EMAIL` dan `OWNER_PASSWORD` dari `.env`, tanpa kredensial di source. Akun lokal telah dibuat; password acak tersimpan di `OWNER_PASSWORD`. Seeder tidak mereset password akun yang sudah ada. Ubah password melalui Pengaturan dengan password lama, atau jalankan `php artisan owner:password` untuk input tersembunyi di terminal. Jangan commit `.env`.

Nomor WhatsApp lokal telah diatur menjadi `6285850720208`. Untuk instalasi baru, isi nomor melalui Pengaturan. Jam operasional belum dikonfirmasi dan sementara menampilkan “Hubungi Owner untuk jam layanan”. Tautan WhatsApp tidak aktif jika nomor belum diisi; aplikasi tidak mengirim pesan otomatis.

## Data contoh

```powershell
php artisan db:seed --class=DemoSeeder
```

Seeder contoh menyediakan enam mobil mengikuti mockup, tanpa pelanggan, pesanan, atau pembayaran rekaan. Seeder ini menolak lingkungan produksi. Data mobil dan plat adalah contoh untuk pengembangan, bukan inventaris usaha yang sudah diverifikasi. Ganti melalui menu Mobil. Ilustrasi mobil adalah placeholder; unggah foto unit asli lewat Owner (JPG/PNG/WebP, maksimal 2 MB).

## Fitur

- Beranda, katalog dengan pencarian/filter/tanggal, detail, estimasi inklusif, pilihan sopir, dan kontak usaha.
- Pengecekan jadwal di server dan tautan WhatsApp dengan teks terisi serta URL encoding.
- Autentikasi Owner, penguncian login setelah lima kegagalan, dan perubahan password.
- Mobil dan foto, pelanggan dan riwayat, pesanan dan transisi status, pembayaran DP/pelunasan/denda, pengembalian, dan denda.
- Harga tersalin ke pesanan. Pemeriksaan bentrok dan penyimpanan menggunakan transaksi serta lock baris untuk MySQL.
- Dashboard, laporan per periode, dan PDF. Daftar pesanan berdasarkan tanggal ambil; penerimaan berdasarkan tanggal bayar.
- Layout responsif mulai 360 px, navigasi mobile, pesan validasi Bahasa Indonesia, serta halaman kesalahan.

## Verifikasi

```powershell
php artisan test --compact
php vendor/bin/pint --format agent
npm run build
```

Pengujian memakai SQLite in-memory dengan database terpisah dari MySQL lokal. Ada 25 test otomatis / 158 assertion pada pemeriksaan awal, mencakup biaya, bentrok, snapshot, transisi, pembayaran, akses, login, upload, cache, dan PDF. UI katalog/detail/dashboard serta navigasi mobile diperiksa pada browser, termasuk lebar 360 px. Nomor WhatsApp diuji sebagai tautan; tidak ada pesan yang dikirim.

Dependensi tambahan: `barryvdh/laravel-dompdf` untuk laporan PDF sesuai SKPL dan `laravel/boost` untuk tooling pengembangan sesuai instruksi repositori. Font Montserrat dan Inter dibundel lokal oleh Vite. Tidak ada framework JavaScript tambahan.

## Produksi

Aplikasi saat ini dijalankan lokal. Untuk produksi, gunakan HTTPS, `APP_ENV=production`, `APP_DEBUG=false`, cookie Secure, konfigurasi database dan akun Owner yang benar, serta webroot `public/`. Jalankan migration dan build, lalu cache konfigurasi/route/view. Jangan menerbitkan data contoh.

Persiapkan backup database minimal mingguan dan uji pemulihan maksimal 24 jam (NF04). Target 50 pengunjung bersamaan, waktu tanggap pada 4G, layanan 24/7, dan seluruh browser dalam SKPL perlu diukur pada hosting tujuan; belum diklaim terverifikasi oleh pengujian lokal.
