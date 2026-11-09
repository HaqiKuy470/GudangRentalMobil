@extends('layouts.owner')
@section('title', $mobil->exists ? 'Ubah mobil' : 'Tambah mobil')
@section('content')<div class="page-head">
        <div>
            <h1>{{ $mobil->exists ? 'Ubah mobil' : 'Tambah mobil' }}</h1>
            <p>Lengkapi data mobil yang akan ditampilkan di katalog.</p>
        </div>
    </div>
    <form class="panel" method="POST"
        action="{{ $mobil->exists ? route('owner.mobil.update', $mobil) : route('owner.mobil.store') }}"
        enctype="multipart/form-data">@csrf @if ($mobil->exists)
            @method('PUT')
        @endif
        <div class="form-grid">
            <x-input name="merk" label="Merek" :value="$mobil->merk" required /><x-input name="model" label="Model"
                :value="$mobil->model" required /><x-input name="tahun" label="Tahun" type="number" min="1990"
                :max="now()->year + 1" :value="$mobil->tahun ?: now()->year" required /><x-input name="plat_nomor" label="Plat nomor"
                :value="$mobil->plat_nomor" required />
            <x-select name="jenis" label="Jenis mobil" required>
                @foreach (['MPV', 'SUV', 'City Car', 'Sedan', 'Minibus'] as $v)
                    <option @selected(old('jenis', $mobil->jenis) === $v)>{{ $v }}</option>
                @endforeach
            </x-select>
            <x-input name="kapasitas" label="Kapasitas kursi" type="number" min="1" max="30" :value="$mobil->kapasitas ?: 7"
                required />
            <x-select name="transmisi" label="Transmisi" required>
                @foreach (['manual', 'otomatis'] as $v)
                    <option value="{{ $v }}" @selected(old('transmisi', $mobil->transmisi) === $v)>{{ ucfirst($v) }}</option>
                @endforeach
            </x-select>
            <div><x-input name="harga_per_hari" label="Harga per hari (Rp, opsional)" type="number" min="1" :value="$mobil->harga_per_hari" />
                <p class="help">Kosongkan agar pelanggan langsung menanyakan harga melalui WhatsApp.</p>
            </div>
            <x-select name="status" label="Status mobil" required>
                @foreach (\App\Enums\StatusMobil::cases() as $v)
                    <option value="{{ $v->value }}" @selected(old('status', $mobil->status?->value) === $v->value)>{{ $v->label() }}</option>
                @endforeach
            </x-select>
            <div><x-input name="foto" label="Foto mobil" type="file" accept="image/jpeg,image/png,image/webp" />
                <p class="help">JPG, PNG, atau WebP. Maksimal 2 MB. Kosongkan untuk mempertahankan foto lama.</p>
                @if (!$mobil->foto)
                    <p class="help">Unggah foto unit Anda untuk mengganti foto referensi di katalog.</p>
                @endif
            </div>
            <div class="span-2"><x-textarea name="deskripsi" label="Deskripsi" :value="$mobil->deskripsi" /></div>
        </div>
        <div class="form-actions"><x-button>Simpan mobil</x-button><x-button variant="outline"
                :href="route('owner.mobil.index')">Batal</x-button></div>
</form>@endsection
