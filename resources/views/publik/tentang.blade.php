@extends('layouts.publik')
@section('title', 'Tentang & kontak')
@section('content')<div class="container">
        <div class="page-head">
            <div>
                <h1>Kenali Gudang Rental Mobil</h1>
                <p>Rental mobil untuk perjalanan Anda di Lamongan dan sekitarnya.</p>
            </div>
        </div>
        <div class="grid two">
            <section class="panel">
                <h2>Mari rencanakan perjalanan</h2>
                <p class="muted">Kami membantu Anda memilih mobil sesuai kebutuhan, dengan opsi lepas kunci atau bersama
                    sopir. Hubungi langsung Owner untuk kesepakatan sewa dan pengambilan.</p>
                <div class="contact-line"><x-icon name="pin" />
                    <div><strong>Alamat usaha</strong>
                        <p>{{ $pengaturan->alamat }}</p>
                    </div>
                </div>
                <div class="contact-line"><x-icon name="clock" />
                    <div><strong>Jam operasional</strong>
                        <p>{{ $pengaturan->jam_operasional }}</p>
                    </div>
                </div>
                <div class="contact-line"><x-icon name="whatsapp" />
                    <div><strong>WhatsApp</strong>
                        <p>{{ $pengaturan->no_whatsapp ?: 'Nomor kontak akan segera tersedia.' }}</p>
                    </div>
                </div>
                @if ($kontakWa)
                    <x-button variant="whatsapp" icon="whatsapp" :href="$kontakWa" target="_blank"
                        rel="noopener noreferrer">Tanya via WhatsApp</x-button>
                @endif
            </section>
            <section class="panel prose">
                <h2>Syarat sewa</h2>
                @foreach (explode("\n", $pengaturan->syarat_sewa ?? '') as $baris)
                    @if (trim($baris))
                        <p>{{ $baris }}</p>
                    @endif
                @endforeach
                <p class="small">Pemilihan mobil dan estimasi di website merupakan permintaan awal. Pesanan dikonfirmasi
                    setelah kesepakatan melalui WhatsApp.</p><x-button variant="outline" :href="route('katalog.index')">Pilih mobil
                    Anda<x-icon name="arrow" /></x-button>
            </section>
        </div>
</div>@endsection
