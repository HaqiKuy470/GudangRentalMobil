<?php

namespace Database\Factories;

use App\Models\Mobil;
use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PesananFactory extends Factory
{
    public function definition(): array
    {
        return ['kode' => 'GRM-'.now()->format('ym').'-'.fake()->unique()->numerify('####'), 'mobil_id' => Mobil::factory(), 'pelanggan_id' => Pelanggan::factory(),
            'jenis_sewa' => 'lepas_kunci', 'tgl_ambil' => today()->addDay(), 'tgl_kembali' => today()->addDays(3), 'lama_sewa' => 3,
            'harga_per_hari' => 350000, 'biaya_sopir' => 0, 'denda' => 0, 'total_biaya' => 1050000, 'status' => 'dipesan'];
    }
}
