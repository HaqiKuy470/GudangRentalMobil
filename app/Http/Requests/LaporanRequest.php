<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['awal' => ['nullable', 'date_format:Y-m-d'], 'akhir' => ['nullable', 'required_with:awal', 'date_format:Y-m-d', 'after_or_equal:awal']];
    }
}
