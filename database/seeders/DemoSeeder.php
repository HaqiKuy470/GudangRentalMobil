<?php

namespace Database\Seeders;

use App\Models\Mobil;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->warn('Data contoh hanya untuk lingkungan pengembangan.');

            return;
        }
        foreach ([
            ['Toyota', 'Avanza', 2022, 'B 1234 XY', 'MPV', 7, 'manual', 350000, 'tersedia'],
            ['Daihatsu', 'Xenia', 2023, 'B 5511 RT', 'MPV', 7, 'manual', 330000, 'tersedia'],
            ['Honda', 'Brio', 2023, 'B 7788 GT', 'City Car', 5, 'otomatis', 300000, 'tersedia'],
            ['Toyota', 'Innova', 2024, 'B 3104 ZK', 'MPV', 7, 'otomatis', 550000, 'tersedia'],
            ['Suzuki', 'Ertiga', 2022, 'B 9012 MN', 'MPV', 7, 'manual', 380000, 'perawatan'],
            ['Mitsubishi', 'Xpander', 2023, 'B 4567 OP', 'MPV', 7, 'otomatis', 420000, 'tersedia'],
        ] as [$merk,$model,$tahun,$plat,$jenis,$kapasitas,$transmisi,$harga,$status]) {
            Mobil::firstOrCreate(['plat_nomor' => $plat], ['merk' => $merk, 'model' => $model, 'tahun' => $tahun, 'jenis' => $jenis, 'kapasitas' => $kapasitas, 'transmisi' => $transmisi, 'harga_per_hari' => $harga, 'status' => $status,
                'deskripsi' => 'Pilihan nyaman untuk perjalanan keluarga dan aktivitas Anda. Hubungi Owner untuk konfirmasi fasilitas serta syarat sewa.']);
        }
    }
}
