<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MobilFactory extends Factory
{
    public function definition(): array
    {
        return ['merk' => 'Toyota', 'model' => 'Avanza', 'tahun' => 2023, 'plat_nomor' => fake()->unique()->bothify('B #### ??'),
            'jenis' => 'MPV', 'kapasitas' => 7, 'transmisi' => 'manual', 'harga_per_hari' => 350000, 'status' => 'tersedia'];
    }
}
