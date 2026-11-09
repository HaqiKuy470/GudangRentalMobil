<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PelangganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['nama' => ['required', 'string', 'max:120'], 'no_hp' => ['required', 'regex:/^62[0-9]{8,13}$/'],
            'alamat' => ['nullable', 'string', 'max:2000'], 'no_identitas' => ['nullable', 'string', 'max:40'], 'catatan' => ['nullable', 'string', 'max:2000']];
    }
}
