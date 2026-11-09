<?php

namespace App\Http\Requests;

use App\Enums\JenisPembayaran;
use App\Enums\MetodePembayaran;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['jenis' => ['required', Rule::enum(JenisPembayaran::class)], 'jumlah' => ['required', 'integer', 'min:1', 'max:100000000000'],
            'metode' => ['required', Rule::enum(MetodePembayaran::class)], 'tgl_bayar' => ['required', 'date_format:Y-m-d', 'before_or_equal:today']];
    }
}
