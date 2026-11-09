<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengaturanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['nama_usaha' => ['required', 'string', 'max:120'], 'alamat' => ['required', 'string', 'max:2000'],
            'jam_operasional' => ['required', 'string', 'max:200'], 'no_whatsapp' => ['required', 'regex:/^62[0-9]{8,13}$/'],
            'biaya_sopir_per_hari' => ['required', 'integer', 'min:0', 'max:100000000'], 'template_pesan' => ['required', 'string', 'max:5000'],
            'syarat_sewa' => ['required', 'string', 'max:10000']];
    }
}
