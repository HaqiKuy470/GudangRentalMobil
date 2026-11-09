<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\PengaturanRequest;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function edit(): View
    {
        return view('owner.pengaturan');
    }

    public function update(PengaturanRequest $request): RedirectResponse
    {
        Pengaturan::nilai()->update($request->validated());

        return back()->with('success', 'Pengaturan usaha diperbarui.');
    }

    public function password(PasswordRequest $request): RedirectResponse
    {
        $request->user()->update(['password' => $request->validated('password')]);
        $request->session()->regenerate();

        return back()->with('success', 'Password berhasil diubah.');
    }
}
