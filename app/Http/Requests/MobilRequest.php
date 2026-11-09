<?php

namespace App\Http\Requests;

use App\Enums\StatusMobil;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MobilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'merk' => ['required', 'string', 'max:80'], 'model' => ['required', 'string', 'max:80'],
            'tahun' => ['required', 'integer', 'between:1990,'.(now()->year + 1)],
            'plat_nomor' => ['required', 'string', 'max:20', Rule::unique('mobil', 'plat_nomor')->ignore($this->route('mobil'))],
            'jenis' => ['required', Rule::in(['MPV', 'SUV', 'City Car', 'Sedan', 'Minibus'])],
            'kapasitas' => ['required', 'integer', 'between:1,30'], 'transmisi' => ['required', Rule::in(['manual', 'otomatis'])],
            'harga_per_hari' => ['nullable', 'integer', 'min:1', 'max:100000000'], 'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'deskripsi' => ['nullable', 'string', 'max:5000'], 'status' => ['required', Rule::enum(StatusMobil::class)],
        ];
    }
}
