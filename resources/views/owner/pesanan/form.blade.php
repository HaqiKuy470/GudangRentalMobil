@extends('layouts.owner')
@section('title', $pesanan->exists ? 'Ubah pesanan' : 'Catat pesanan baru')
@section('content')<div class="page-head">
        <div>
            <h1>{{ $pesanan->exists ? 'Ubah pesanan' : 'Catat pesanan baru' }}</h1>
            <p>Pastikan kesepakatan dengan pelanggan sudah dikonfirmasi lewat WhatsApp.</p>
        </div>
    </div>
    <form method="POST"
        action="{{ $pesanan->exists ? route('owner.pesanan.update', $pesanan) : route('owner.pesanan.store') }}"
        data-order-form data-driver-rate="{{ \App\Models\Pengaturan::nilai()->biaya_sopir_per_hari }}"
        data-snapshot-price="{{ $pesanan->harga_per_hari }}"
        data-snapshot-driver="{{ $pesanan->exists && $pesanan->jenis_sewa->value === 'dengan_sopir' ? intdiv($pesanan->biaya_sopir, $pesanan->lama_sewa) : '' }}">
        @csrf @if ($pesanan->exists)
            @method('PUT')
        @endif
        <div class="record-grid">
            <section class="panel">
                <h3>Data pesanan</h3>
                <div class="stack"><x-select name="pelanggan_id" label="Pelanggan">
                        <option value="">Tambah pelanggan baru</option>
                        @foreach ($pelanggan as $p)
                            <option value="{{ $p->id }}" @selected(old('pelanggan_id', $pesanan->pelanggan_id) == $p->id)>{{ $p->nama }} ·
                                {{ $p->no_hp }}</option>
                        @endforeach
                    </x-select>
                    <div class="form-grid" id="new-customer"><x-input name="nama_pelanggan"
                            label="Nama pelanggan baru" /><x-input name="no_hp_pelanggan" label="Nomor HP pelanggan baru"
                            placeholder="62812…" /></div><x-select name="mobil_id" label="Mobil" required>
                        <option value="">Pilih mobil</option>
                        @foreach ($mobil as $m)
                            <option value="{{ $m->id }}" data-price="{{ $m->harga_per_hari }}"
                                data-photo="{{ $m->fotoUrl() }}" data-title="{{ $m->nama() }}"
                                @selected(old('mobil_id', $pesanan->mobil_id) == $m->id) @disabled($m->status->value === 'perawatan')>{{ $m->nama() }} ·
                                {{ $m->plat_nomor }}{{ $m->status->value === 'perawatan' ? ' (Perawatan)' : '' }}</option>
                        @endforeach
                    </x-select>
                    <div class="form-grid"><x-input name="tgl_ambil" label="Tanggal ambil" type="date" :value="$pesanan->tgl_ambil?->toDateString()"
                            required /><x-input name="tgl_kembali" label="Tanggal kembali" type="date" :value="$pesanan->tgl_kembali?->toDateString()"
                            required /></div><x-select name="jenis_sewa" label="Jenis sewa" required>
                        @foreach (\App\Enums\JenisSewa::cases() as $v)
                            <option value="{{ $v->value }}" @selected(old('jenis_sewa', $pesanan->jenis_sewa?->value) === $v->value)>{{ $v->label() }}</option>
                        @endforeach
                    </x-select>
                    @if (!$pesanan->exists)
                        <div><x-input name="harga_kesepakatan" label="Harga kesepakatan per hari (Rp)" type="number" min="1" max="100000000" />
                            <p class="help">Wajib untuk mobil tanpa harga katalog. Isi sesuai kesepakatan dengan pelanggan.</p>
                        </div>
                    @endif
                    <x-textarea name="catatan" label="Catatan (opsional)" :value="$pesanan->catatan" />
                </div>
                <div class="form-actions"><x-button>Simpan pesanan</x-button><x-button variant="outline"
                        :href="route('owner.pesanan.index')">Batal</x-button></div>
            </section>
            <aside class="panel">
                <h3>Ringkasan biaya</h3><img id="order-photo" class="summary-photo" src="{{ asset('images/car-0.svg') }}"
                    alt="Ilustrasi pilihan mobil">
                <h3 id="order-car">Pilih mobil terlebih dahulu</h3>
                <p class="small muted">Total dihitung dari tanggal sewa dan jenis layanan.</p>
                <div class="costs">
                    <div class="cost-row"><span>Lama sewa</span><strong id="order-days">—</strong></div>
                    <div class="cost-row"><span>Sewa mobil</span><strong id="order-rent">—</strong></div>
                    <div class="cost-row"><span>Biaya sopir</span><strong id="order-driver">—</strong></div>
                    <div class="cost-total"><strong>Total biaya</strong><span class="price" id="order-total">—</span></div>
                </div>
                <p class="help">Tanggal dihitung inklusif. Jadwal akan diperiksa kembali saat pesanan disimpan.</p>
                @if ($pesanan->exists)
                    <p class="help">Harga pesanan menggunakan tarif yang tersimpan saat pencatatan awal.</p>
                @endif
            </aside>
        </div>
</form>@endsection
