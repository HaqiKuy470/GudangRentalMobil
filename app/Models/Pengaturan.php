<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = ['nama_usaha', 'alamat', 'jam_operasional', 'no_whatsapp', 'biaya_sopir_per_hari', 'template_pesan', 'syarat_sewa'];

    public const TEMPLATE = "Halo {nama_usaha}, saya ingin menyewa {mobil} ({plat})\nTanggal: {tgl_ambil} s.d. {tgl_kembali} ({lama} hari)\nJenis sewa: {jenis_sewa}\nEstimasi: {estimasi}\nNama saya: ";

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('pengaturan'));
    }

    public static function nilai(): self
    {
        $attributes = Cache::rememberForever('pengaturan', fn () => static::firstOrCreate(['id' => 1], [
            'nama_usaha' => 'Gudang Rental Mobil', 'alamat' => 'Jl. Laras Liris No.116, Dapur Barat, Sidokumpul, Lamongan, Jawa Timur 62213',
            'jam_operasional' => 'Hubungi Owner untuk jam layanan', 'template_pesan' => self::TEMPLATE,
            'biaya_sopir_per_hari' => 150000, 'syarat_sewa' => "Siapkan KTP dan SIM A yang masih berlaku.\nPersyaratan, harga final, dan pengambilan disepakati melalui WhatsApp.",
        ])->getAttributes());

        return (new static)->newFromBuilder($attributes);
    }
}
