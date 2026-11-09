<?php

return [
    'required' => ':attribute wajib diisi.', 'required_with' => ':attribute wajib diisi jika :values diisi.',
    'required_without' => ':attribute wajib diisi jika :values belum dipilih.', 'required_if' => ':attribute wajib diisi untuk status :value.',
    'email' => 'Format :attribute tidak valid.', 'string' => ':attribute harus berupa teks.',
    'integer' => ':attribute harus berupa bilangan bulat.', 'numeric' => ':attribute harus berupa angka.',
    'date' => ':attribute harus berupa tanggal yang valid.', 'date_format' => 'Format :attribute harus tahun-bulan-tanggal.',
    'after_or_equal' => ':attribute harus sama atau setelah :date.', 'before_or_equal' => ':attribute harus sama atau sebelum :date.',
    'min' => ['numeric' => ':attribute minimal :min.', 'string' => ':attribute minimal :min karakter.', 'file' => ':attribute minimal :min KB.'],
    'max' => ['numeric' => ':attribute maksimal :max.', 'string' => ':attribute maksimal :max karakter.', 'file' => ':attribute maksimal :max KB.'],
    'between' => ['numeric' => ':attribute harus antara :min dan :max.'],
    'unique' => ':attribute sudah digunakan. Gunakan nilai lain.', 'exists' => ':attribute tidak ditemukan. Silakan pilih kembali.',
    'in' => 'Pilihan :attribute tidak valid.', 'enum' => 'Pilihan :attribute tidak valid.', 'regex' => 'Format :attribute tidak valid. Nomor HP harus diawali 62 tanpa spasi.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.', 'current_password' => 'Password lama tidak cocok.',
    'image' => ':attribute harus berupa gambar.', 'mimes' => ':attribute harus berformat :values.', 'uploaded' => ':attribute gagal diunggah. Pilih ulang berkas.',
    'attributes' => ['tgl_ambil' => 'Tanggal ambil', 'tgl_kembali' => 'Tanggal kembali', 'tgl_bayar' => 'Tanggal bayar', 'tgl_dikembalikan' => 'Tanggal dikembalikan',
        'pelanggan_id' => 'Pelanggan', 'mobil_id' => 'Mobil', 'nama_pelanggan' => 'Nama pelanggan baru', 'no_hp_pelanggan' => 'Nomor HP pelanggan baru', 'no_hp' => 'Nomor HP',
        'no_whatsapp' => 'Nomor WhatsApp', 'harga_per_hari' => 'Harga per hari', 'biaya_sopir_per_hari' => 'Biaya sopir per hari', 'plat_nomor' => 'Plat nomor',
        'jenis_sewa' => 'Jenis sewa', 'current_password' => 'Password lama', 'password' => 'Password', 'password_confirmation' => 'Konfirmasi password', 'awal' => 'Tanggal awal', 'akhir' => 'Tanggal akhir'],
];
