<?php

namespace App\Models;

use App\Enums\JenisSewa;
use App\Enums\StatusPesanan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $fillable = ['kode', 'pelanggan_id', 'mobil_id', 'jenis_sewa', 'tgl_ambil', 'tgl_kembali', 'lama_sewa', 'harga_per_hari', 'biaya_sopir', 'denda', 'total_biaya', 'status', 'tgl_dikembalikan', 'catatan'];

    protected function casts(): array
    {
        return ['status' => StatusPesanan::class, 'jenis_sewa' => JenisSewa::class, 'tgl_ambil' => 'date', 'tgl_kembali' => 'date', 'tgl_dikembalikan' => 'date'];
    }

    public function mobil(): BelongsTo
    {
        return $this->belongsTo(Mobil::class)->withTrashed();
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function terbayar(): int
    {
        return (int) ($this->relationLoaded('pembayaran') ? $this->pembayaran->sum('jumlah') : $this->pembayaran()->sum('jumlah'));
    }

    public function sisaTagihan(): int
    {
        return $this->total_biaya - $this->terbayar();
    }

    public function statusBayar(): string
    {
        return $this->terbayar() === 0 ? 'Belum bayar' : ($this->sisaTagihan() <= 0 ? 'Lunas' : 'DP');
    }
}
