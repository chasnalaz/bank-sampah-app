<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - Bank Sampah</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    
    {{-- Kita pakai Bootstrap lewat CDN biar tampilannya rapi saat diprint --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Times New Roman', serif; color: #000; }
        .header-laporan { border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { width: 80px; height: auto; }
        .tanda-tangan { margin-top: 50px; text-align: center; }
        
        /* CSS Khusus Cetak */
        @media print {
            .no-print { display: none !important; } /* Sembunyikan tombol saat diprint */
            body { -webkit-print-color-adjust: exact; } /* Paksa warna background muncul */
        }
    </style>
</head>
<body class="bg-white p-5">

    {{-- TOMBOL PRINT (Akan hilang pas dicetak) --}}
    <div class="no-print position-fixed top-0 end-0 p-3">
        <button onclick="window.print()" class="btn btn-primary shadow fw-bold">
            🖨️ Cetak Dokumen
        </button>
    </div>

    {{-- KOP SURAT --}}
    <div class="header-laporan text-center d-flex align-items-center justify-content-center gap-4">
        {{-- Ganti src dengan logo kamu jika ada --}}
        {{-- <img src="{{ asset('img/logo.png') }}" class="logo" alt="Logo"> --}}
        
        <div>
            <h3 class="fw-bold text-uppercase m-0">Bank Sampah Berseri Sejahtera</h3>
            <p class="m-0 small">Jl. Jeruk Manis No. 01, Cilacap, Jawa Tengah</p>
            <p class="m-0 small">Email: admin@berseri.com | Telp: 0812-3456-7890</p>
        </div>
    </div>

    {{-- JUDUL LAPORAN --}}
    <div class="text-center mb-4">
        <h4 class="fw-bold text-uppercase text-decoration-underline">Laporan Keuangan & Arus Kas</h4>
        <p class="fst-italic mb-0">{{ $labelFilter }}</p>
        <small class="text-muted">Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</small>
    </div>

    {{-- RINGKASAN SALDO --}}
    <div class="card border-dark mb-4 rounded-0">
        <div class="card-body p-0">
            <table class="table table-bordered mb-0 text-center">
                <thead class="table-light border-dark">
                    <tr>
                        <th>Total Pemasukan (Tengkulak)</th>
                        <th>Total Pengeluaran (Nasabah)</th>
                        <th>Keuntungan Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="fw-bold fs-5">
                        <td class="text-success">+ Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                        <td class="text-danger">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                        <td class="{{ $keuntungan < 0 ? 'text-danger' : 'text-primary' }}">
                            Rp {{ number_format($keuntungan, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- TABEL 1: PEMASUKAN --}}
    <h6 class="fw-bold mt-4">A. Rincian Pemasukan (Penjualan Sampah)</h6>
    <table class="table table-bordered table-sm border-dark align-middle">
        <thead class="table-secondary border-dark text-center">
            <tr>
                <th width="5%">No</th>
                <th>Tanggal</th>
                <th>Tengkulak</th>
                <th>Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penjualan as $index => $jual)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($jual->tanggal_jual)->translatedFormat('d F Y') }}</td>
                <td>{{ $jual->tengkulak->nama_tengkulak ?? '-' }}</td>
                <td class="text-end pe-3">Rp {{ number_format($jual->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center fst-italic py-3">Tidak ada data pemasukan pada periode ini.</td>
            </tr>
            @endforelse
            @if($penjualan->count() > 0)
            <tr class="table-light fw-bold">
                <td colspan="3" class="text-end pe-3">Subtotal Pemasukan</td>
                <td class="text-end pe-3">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- TABEL 2: PENGELUARAN --}}
    <h6 class="fw-bold mt-4">B. Rincian Pengeluaran (Setor Sampah Nasabah)</h6>
    <table class="table table-bordered table-sm border-dark align-middle">
        <thead class="table-secondary border-dark text-center">
            <tr>
                <th width="5%">No</th>
                <th>Tanggal</th>
                <th>Nama Nasabah</th>
                <th>Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $index => $t)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($t->created_at)->translatedFormat('d F Y') }}</td>
                <td>{{ $t->nasabah->nama ?? 'Nasabah Terhapus' }}</td>
                <td class="text-end pe-3">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center fst-italic py-3">Tidak ada data pengeluaran pada periode ini.</td>
            </tr>
            @endforelse
            @if($transaksi->count() > 0)
            <tr class="table-light fw-bold">
                <td colspan="3" class="text-end pe-3">Subtotal Pengeluaran</td>
                <td class="text-end pe-3">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <div class="row tanda-tangan">
        <div class="col-8"></div> {{-- Spacer --}}
        <div class="col-4">
            <p class="mb-5">Cilacap, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Mengetahui,</p>
            <br>
            <p class="fw-bold text-decoration-underline mb-0">{{ Auth::user()->name }}</p>
            <p class="small">Administrator</p>
        </div>
    </div>

    {{-- SCRIPT OTOMATIS PRINT SAAT DIBUKA --}}
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>