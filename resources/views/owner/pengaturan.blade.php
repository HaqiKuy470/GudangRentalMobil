@extends('layouts.owner')
@section('title', 'Pengaturan')
@section('content')<div class="page-head">
        <div>
            <h1>Pengaturan</h1>
            <p>Informasi usaha, layanan, dan keamanan akun Owner.</p>
        </div>
    </div>
    <div class="record-grid">
        <form class="panel" method="POST" action="{{ route('owner.pengaturan.update') }}">@csrf @method('PUT')<h3>
                Informasi usaha</h3>
            <div class="stack"><x-input name="nama_usaha" label="Nama usaha" :value="$pengaturan->nama_usaha" required /><x-textarea
                    name="alamat" label="Alamat" :value="$pengaturan->alamat" required />
                <div class="form-grid"><x-input name="jam_operasional" label="Jam operasional" :value="$pengaturan->jam_operasional" required />
                    <div><x-input name="no_whatsapp" label="Nomor WhatsApp" :value="$pengaturan->no_whatsapp" placeholder="62812…"
                            required />
                        <p class="help">Kode negara 62, tanpa tanda + dan tanpa spasi.</p>
                    </div>
                </div><x-input name="biaya_sopir_per_hari" label="Biaya sopir per hari (Rp)" type="number" min="0"
                    :value="$pengaturan->biaya_sopir_per_hari" required /><x-textarea name="template_pesan" label="Template pesan WhatsApp"
                    :value="$pengaturan->template_pesan" required rows="7" />
                <p class="help">Placeholder: {nama_usaha}, {mobil}, {plat}, {jenis_sewa}, {tgl_ambil}, {tgl_kembali},
                    {lama}, {estimasi}.</p><x-textarea name="syarat_sewa" label="Syarat sewa" :value="$pengaturan->syarat_sewa" required
                    rows="6" />
            </div>
            <div class="form-actions"><x-button>Simpan pengaturan</x-button></div>
        </form>
        <aside class="panel">
            <h3>Ubah password</h3>
            <p class="small muted" style="margin-bottom:18px">Gunakan password minimal 12 karakter yang hanya Anda ketahui.
            </p>
            <form method="POST" action="{{ route('owner.password.update') }}">@csrf @method('PUT')<div class="stack"
                    style="gap:16px"><x-input name="current_password" label="Password lama" type="password"
                        autocomplete="current-password" required /><x-input name="password" label="Password baru"
                        type="password" autocomplete="new-password" minlength="12" required /><x-input
                        name="password_confirmation" label="Ulangi password baru" type="password"
                        autocomplete="new-password" required /><x-button variant="outline" class="full">Simpan
                        password</x-button></div>
            </form>
        </aside>
</div>@endsection
