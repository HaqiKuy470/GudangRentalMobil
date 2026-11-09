<?php

namespace App\Http\Requests;

use App\Enums\JenisSewa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TanggalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tgl_ambil' => ['nullable', 'required_with:tgl_kembali', 'date_format:Y-m-d', 'after_or_equal:today'],
            'tgl_kembali' => ['nullable', 'required_with:tgl_ambil', 'date_format:Y-m-d', 'after_or_equal:tgl_ambil'],
            'jenis_sewa' => ['nullable', Rule::enum(JenisSewa::class)],
            'q' => ['nullable', 'string', 'max:120'], 'jenis' => ['nullable', Rule::in(['MPV', 'SUV', 'City Car', 'Sedan', 'Minibus'])],
            'kapasitas' => ['nullable', 'integer', 'between:1,30'], 'transmisi' => ['nullable', Rule::in(['manual', 'otomatis'])],
            'harga_max' => ['nullable', 'integer', 'min:0', 'max:100000000'],
        ];
    }
}
