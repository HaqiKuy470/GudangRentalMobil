<?php

namespace App\Models;

use App\Enums\JenisPembayaran;
use App\Enums\MetodePembayaran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = ['pesanan_id', 'jenis', 'jumlah', 'metode', 'tgl_bayar'];

    protected function casts(): array
    {
        return ['jenis' => JenisPembayaran::class, 'metode' => MetodePembayaran::class, 'tgl_bayar' => 'date', 'jumlah' => 'integer'];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }
}
