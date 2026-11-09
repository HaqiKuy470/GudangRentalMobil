<?php

namespace App\Models;

use App\Enums\StatusMobil;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mobil extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mobil';

    protected $fillable = ['merk', 'model', 'tahun', 'plat_nomor', 'jenis', 'kapasitas', 'transmisi', 'harga_per_hari', 'foto', 'deskripsi', 'status'];

    protected function casts(): array
    {
        return ['status' => StatusMobil::class, 'harga_per_hari' => 'integer'];
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    public function nama(): string
    {
        return $this->merk.' '.$this->model;
    }

    public function statusHariIni(): string
    {
        if ($this->status === StatusMobil::Perawatan) {
            return 'perawatan';
        }

        return $this->pesanan()->whereIn('status', ['dipesan', 'disewa'])->whereDate('tgl_ambil', '<=', today())->whereDate('tgl_kembali', '>=', today())->exists() ? 'disewa' : 'tersedia';
    }

    public function fotoUrl(): string
    {
        if ($this->foto) {
            return asset('storage/'.$this->foto);
        }

        $contoh = match (strtolower($this->merk.' '.$this->model)) {
            'toyota avanza' => 'avanza',
            'daihatsu xenia' => 'xenia',
            'honda brio' => 'brio',
            'toyota innova' => 'innova',
            'suzuki ertiga' => 'ertiga',
            'mitsubishi xpander' => 'xpander',
            default => null,
        };

        return $contoh ? asset('images/fleet/'.$contoh.'.jpg') : asset('images/car-0.svg');
    }
}
