<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        if (config('grm.owner_email') && config('grm.owner_password')) {
            User::firstOrCreate(['email' => config('grm.owner_email')], ['name' => 'Owner Gudang Rental Mobil', 'password' => config('grm.owner_password')]);
        }
    }
}
