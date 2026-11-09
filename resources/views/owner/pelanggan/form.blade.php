@extends('layouts.owner')
@section('title', $pelanggan->exists ? 'Ubah pelanggan' : 'Tambah pelanggan')
@section('content')<div class="page-head">
        <div>
            <h1>{{ $pelanggan->exists ? 'Ubah pelanggan' : 'Tambah pelanggan' }}</h1>
            <p>Data ini hanya dapat dilihat oleh Owner.</p>
        </div>
    </div>
    <form class="panel" method="POST"
        action="{{ $pelanggan->exists ? route('owner.pelanggan.update', $pelanggan) : route('owner.pelanggan.store') }}">@csrf
        @if ($pelanggan->exists)
            @method('PUT')
        @endif
        <div class="form-grid"><x-input name="nama" label="Nama lengkap" :value="$pelanggan->nama" required />
            <div><x-input name="no_hp" label="Nomor HP" :value="$pelanggan->no_hp" placeholder="62812…" required />
                <p class="help">Gunakan kode negara 62 tanpa tanda +.</p>
            </div>
            <div class="span-2"><x-textarea name="alamat" label="Alamat" :value="$pelanggan->alamat" /></div><x-input
                name="no_identitas" label="Nomor KTP / SIM (opsional)" :value="$pelanggan->no_identitas" /><x-textarea name="catatan"
                label="Catatan (opsional)" :value="$pelanggan->catatan" />
        </div>
        <div class="form-actions"><x-button>Simpan pelanggan</x-button><x-button variant="outline"
                :href="route('owner.pelanggan.index')">Batal</x-button></div>
</form>@endsection
