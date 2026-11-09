<?php

namespace App\Http\Requests;

use App\Enums\StatusPesanan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::enum(StatusPesanan::class)],
            'tgl_dikembalikan' => ['required_if:status,selesai', 'nullable', 'date_format:Y-m-d', 'after_or_equal:'.$this->route('pesanan')->tgl_ambil->toDateString(), 'before_or_equal:today'],
            'denda' => ['nullable', 'integer', 'min:0', 'max:100000000000']];
    }
}
