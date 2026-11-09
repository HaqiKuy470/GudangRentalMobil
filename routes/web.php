<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\LaporanController;
use App\Http\Controllers\Owner\MobilController;
use App\Http\Controllers\Owner\PelangganController;
use App\Http\Controllers\Owner\PembayaranController;
use App\Http\Controllers\Owner\PengaturanController;
use App\Http\Controllers\Owner\PesananController;
use App\Http\Controllers\PublikController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublikController::class, 'beranda'])->name('beranda');
Route::get('/mobil', [PublikController::class, 'katalog'])->name('katalog.index');
Route::get('/mobil/{mobil}', [PublikController::class, 'detail'])->name('katalog.show');
Route::get('/mobil/{mobil}/estimasi', [PublikController::class, 'estimasi'])->name('katalog.estimasi');
Route::get('/tentang', [PublikController::class, 'tentang'])->name('tentang');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'form'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::prefix('owner')->name('owner.')->middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('mobil', MobilController::class)->except('show')->parameters(['mobil' => 'mobil']);
    Route::resource('pelanggan', PelangganController::class)->parameters(['pelanggan' => 'pelanggan']);
    Route::resource('pesanan', PesananController::class)->except('destroy')->parameters(['pesanan' => 'pesanan']);
    Route::patch('/pesanan/{pesanan}/status', [PesananController::class, 'status'])->name('pesanan.status');
    Route::post('/pesanan/{pesanan}/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::delete('/pembayaran/{pembayaran}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf', [LaporanController::class, 'pdf'])->name('laporan.pdf');
    Route::get('/pengaturan', [PengaturanController::class, 'edit'])->name('pengaturan.edit');
    Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    Route::put('/password', [PengaturanController::class, 'password'])->name('password.update');
});
