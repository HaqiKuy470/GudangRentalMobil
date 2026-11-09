<?php

namespace Database\Factories;

use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PembayaranFactory extends Factory
{
    public function definition(): array
    {
        return ['pesanan_id' => Pesanan::factory(), 'jenis' => 'dp', 'jumlah' => 300000, 'metode' => 'transfer', 'tgl_bayar' => today()];
    }
}
