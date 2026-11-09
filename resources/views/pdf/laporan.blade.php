<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Gudang Rental Mobil</title>
    <style>
        body {
            font: 11px DejaVu Sans, sans-serif;
            color: #0b2a52
        }

        h1 {
            font-size: 22px;
            margin-bottom: 2px
        }

        h2 {
            font-size: 15px;
            margin-top: 25px
        }

        p {
            color: #475569
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px
        }

        th {
            background: #e6ecf5;
            text-align: left
        }

        td,
        th {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0
        }

        .total {
            background: #fff4eb;
            padding: 12px;
            font-size: 14px
        }

        small {
            color: #64748b
        }
    </style>
</head>

<body>
    <h1>{{ $pengaturan->nama_usaha }}</h1>
    <p>{{ $pengaturan->alamat }}</p>
    <h2>Laporan transaksi</h2>
    <p>{{ tanggal_id($awal) }} – {{ tanggal_id($akhir) }}</p>
    <div class="total">Total penerimaan: <strong>{{ rupiah($total) }}</strong></div>
    <p>
        @foreach (['DP', 'Pelunasan', 'Denda'] as $jenis)
            {{ $jenis }}: {{ rupiah($perJenis->get($jenis, 0)) }} &nbsp;
        @endforeach
    </p>
    <h2>Daftar pesanan (berdasarkan tanggal ambil)</h2>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th>Mobil</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Total biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesanan as $p)
                <tr>
                    <td>{{ $p->kode }}</td>
                    <td>{{ $p->pelanggan->nama }}</td>
                    <td>{{ $p->mobil->nama() }}</td>
                    <td>{{ tanggal_id($p->tgl_ambil) }} – {{ tanggal_id($p->tgl_kembali) }}</td>
                    <td>{{ $p->status->label() }}</td>
                    <td>{{ rupiah($p->total_biaya) }}</td>
            </tr>@empty<tr>
                    <td colspan="6">Tidak ada pesanan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <h2>Pembayaran diterima (berdasarkan tanggal bayar)</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pesanan</th>
                <th>Pelanggan</th>
                <th>Jenis</th>
                <th>Metode</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembayaran as $p)
                <tr>
                    <td>{{ tanggal_id($p->tgl_bayar) }}</td>
                    <td>{{ $p->pesanan->kode }}</td>
                    <td>{{ $p->pesanan->pelanggan->nama }}</td>
                    <td>{{ $p->jenis->label() }}</td>
                    <td>{{ $p->metode->label() }}</td>
                    <td>{{ rupiah($p->jumlah) }}</td>
            </tr>@empty<tr>
                    <td colspan="6">Tidak ada pembayaran pada periode ini. Total Rp0.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <p><small>Dicetak {{ now()->translatedFormat('j F Y H:i') }} WIB.</small></p>
</body>

</html>
