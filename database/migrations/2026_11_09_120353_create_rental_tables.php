<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_usaha');
            $table->text('alamat')->nullable();
            $table->string('jam_operasional')->nullable();
            $table->string('no_whatsapp')->nullable();
            $table->unsignedBigInteger('biaya_sopir_per_hari')->default(150000);
            $table->text('template_pesan');
            $table->text('syarat_sewa')->nullable();
            $table->timestamps();
        });
        Schema::create('mobil', function (Blueprint $table) {
            $table->id();
            $table->string('merk');
            $table->string('model');
            $table->unsignedSmallInteger('tahun');
            $table->string('plat_nomor')->unique();
            $table->string('jenis');
            $table->unsignedTinyInteger('kapasitas');
            $table->string('transmisi');
            $table->unsignedBigInteger('harga_per_hari');
            $table->string('foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('status')->default('tersedia');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('no_hp')->index();
            $table->text('alamat')->nullable();
            $table->string('no_identitas')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->restrictOnDelete();
            $table->foreignId('mobil_id')->constrained('mobil')->restrictOnDelete();
            $table->string('jenis_sewa');
            $table->date('tgl_ambil');
            $table->date('tgl_kembali');
            $table->unsignedSmallInteger('lama_sewa');
            $table->unsignedBigInteger('harga_per_hari');
            $table->unsignedBigInteger('biaya_sopir')->default(0);
            $table->unsignedBigInteger('denda')->default(0);
            $table->unsignedBigInteger('total_biaya');
            $table->string('status')->default('dipesan');
            $table->date('tgl_dikembalikan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->index(['mobil_id', 'status', 'tgl_ambil', 'tgl_kembali']);
        });
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->string('jenis');
            $table->unsignedBigInteger('jumlah');
            $table->string('metode');
            $table->date('tgl_bayar')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['pembayaran', 'pesanan', 'pelanggan', 'mobil', 'pengaturan'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
