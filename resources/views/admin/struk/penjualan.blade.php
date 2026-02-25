<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Penjualan #{{ $penjualan->id }}</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; max-width: 300px; margin: 0 auto; padding: 10px; }
        .header { text-align: center; margin-bottom: 10px; }
        .separator { border-top: 1px dashed #000; margin: 5px 0; }
        .item { display: flex; justify-content: space-between; }
        .total { font-weight: bold; font-size: 14px; margin-top: 5px; }
        .footer { text-align: center; margin-top: 15px; font-size: 10px; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h3 style="margin: 0;">BS BERSERI SEJAHTERA</h3>
        <small>BUKTI PENJUALAN SAMPAH</small>
    </div>

    <div class="separator"></div>

    <div class="item">
        <span>Tgl:</span>
        <span>{{ \Carbon\Carbon::parse($penjualan->tanggal_jual)->format('d/m/Y') }}</span>
    </div>
    <div class="item">
        <span>Kepada:</span>
        <span>{{ strtoupper($penjualan->tengkulak->nama_tengkulak ?? '-') }}</span>
    </div>

    <div class="separator"></div>

    <div class="item">
        <span>Item:</span>
        {{-- Kita asumsikan relasi jenisSampah ada di model Penjualan --}}
        <span>{{ $penjualan->jenisSampah->nama_sampah ?? 'Sampah' }}</span>
    </div>
    <div class="item">
        <span>Berat:</span>
        <span>{{ $penjualan->berat_kg }} Kg</span>
    </div>
    <div class="item">
        <span>Harga/Kg:</span>
        <span>Rp {{ number_format($penjualan->harga_per_kg, 0, ',', '.') }}</span>
    </div>

    <div class="separator"></div>

    <div class="item total">
        <span>DITERIMA:</span>
        <span>Rp {{ number_format($penjualan->total_pendapatan, 0, ',', '.') }}</span>
    </div>

    <div class="footer">
        <p>Transaksi Penjualan Berhasil.</p>
    </div>

</body>
</html>