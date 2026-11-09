<?php

namespace App\Http\Requests;

use App\Enums\JenisSewa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PesananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pelanggan_id' => ['nullable', 'exists:pelanggan,id'], 'nama_pelanggan' => ['required_without:pelanggan_id', 'nullable', 'string', 'max:120'],
            'no_hp_pelanggan' => ['required_without:pelanggan_id', 'nullable', 'regex:/^62[0-9]{8,13}$/'],
            'mobil_id' => ['required', 'exists:mobil,id'], 'jenis_sewa' => ['required', Rule::enum(JenisSewa::class)],
            'tgl_ambil' => ['required', 'date_format:Y-m-d'], 'tgl_kembali' => ['required', 'date_format:Y-m-d', 'after_or_equal:tgl_ambil'],
            'catatan' => ['nullable', 'string', 'max:5000'],
            'harga_kesepakatan' => ['nullable', 'integer', 'min:1', 'max:100000000'],
        ];
    }
}
