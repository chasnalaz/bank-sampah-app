<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Transaksi #{{ $transaksi->id }}</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; max-width: 300px; margin: 0 auto; padding: 10px; }
        .header { text-align: center; margin-bottom: 10px; }
        .separator { border-top: 1px dashed #000; margin: 5px 0; }
        .item { display: flex; justify-content: space-between; }
        .total { font-weight: bold; font-size: 14px; margin-top: 5px; }
        .footer { text-align: center; margin-top: 15px; font-size: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h3 style="margin: 0;">BS BERSERI SEJAHTERA</h3>
        <small>Jl. Jeruk Manis Rt 01 Rw 06</small><br>
        <small>Cilacap, Jawa Tengah</small>
    </div>

    <div class="separator"></div>

    <div class="item">
        <span>Tgl:</span>
        <span>{{ \Carbon\Carbon::parse($transaksi->created_at)->format('d/m/Y H:i') }}</span>
    </div>
    <div class="item">
        <span>Nasabah:</span>
        {{-- Menggunakan relasi nasabah yang sudah diperbaiki --}}
        <span>{{ strtoupper($transaksi->nasabah->nama ?? 'UMUM') }}</span>
    </div>
    <div class="item">
        <span>ID Trx:</span>
        <span>#TRX-{{ $transaksi->id }}</span>
    </div>

    <div class="separator"></div>

    {{-- Detail Transaksi --}}
    <div style="margin-bottom: 5px;">
        <div>{{ $transaksi->jenis_transaksi == 'setor' ? 'SETOR SAMPAH' : 'PENARIKAN SALDO' }}</div>
    </div>

    <div class="item total">
        <span>TOTAL:</span>
        <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
    </div>

    <div class="separator"></div>

    <div class="footer">
        <p>Terima Kasih telah menabung sampah.<br>
        Simpan struk ini sebagai bukti sah.</p>
        <br>
        <small>( Admin Petugas )</small>
    </div>

</body>
</html>