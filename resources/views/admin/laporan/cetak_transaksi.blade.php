<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Times New Roman', serif; color: #000; font-size: 12pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .table-custom th, .table-custom td { border: 1px solid #000; padding: 6px; }
        .box-summary { border: 1px solid #000; padding: 10px; margin-bottom: 20px; }
        @media print {
            .no-print { display: none !important; }
            @page { margin: 2cm; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container mt-4">
        <div class="header">
            <h3 class="fw-bold text-uppercase">Bank Sampah Berseri Sejahtera</h3>
            <p class="mb-0">Jl. Jeruk Manis Rt 01 Rw 06, Cilacap</p>
            <h5 class="mt-3 fw-bold">LAPORAN ARUS KAS & KEUANGAN</h5>
            @if($startDate && $endDate)
                <small>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</small>
            @else
                <small>Periode: Semua Waktu</small>
            @endif
        </div>

        <div class="box-summary">
            <div class="row text-center">
                <div class="col-4 border-end border-dark">
                    <small>Total Pemasukan</small><br>
                    <strong class="fs-5">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</strong>
                </div>
                <div class="col-4 border-end border-dark">
                    <small>Total Pengeluaran</small><br>
                    <strong class="fs-5">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong>
                </div>
                <div class="col-4">
                    <small>Keuntungan Bersih</small><br>
                    <strong class="fs-5">Rp {{ number_format($keuntungan, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <h6 class="fw-bold mt-4">A. PEMASUKAN (PENJUALAN KE TENGKULAK)</h6>
        <table class="table table-custom w-100 mb-4">
            <thead class="bg-light">
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Tanggal</th>
                    <th>Tengkulak</th>
                    <th width="25%" class="text-end">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penjualan as $i => $jual)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($jual->tanggal_jual)->format('d/m/Y') }}</td>
                    <td>{{ $jual->tengkulak->nama_tengkulak ?? '-' }}</td>
                    <td class="text-end">Rp {{ number_format($jual->total_pendapatan, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center fst-italic">Belum ada data pemasukan.</td></tr>
                @endforelse
                <tr class="fw-bold bg-light">
                    <td colspan="3" class="text-end">TOTAL PEMASUKAN</td>
                    <td class="text-end">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <h6 class="fw-bold">B. PENGELUARAN (SETORAN NASABAH)</h6>
        <table class="table table-custom w-100">
            <thead class="bg-light">
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Tanggal</th>
                    <th>Nama Nasabah</th>
                    <th width="25%" class="text-end">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksi as $i => $t)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $t->created_at->format('d/m/Y') }}</td>
                    <td>{{ $t->nasabah->nama ?? 'Nasabah Dihapus' }}</td>
                    <td class="text-end">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center fst-italic">Belum ada data pengeluaran.</td></tr>
                @endforelse
                <tr class="fw-bold bg-light">
                    <td colspan="3" class="text-end">TOTAL PENGELUARAN</td>
                    <td class="text-end">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="row mt-5">
            <div class="col-6"></div> 
            <div class="col-6 text-center">
                <p>Cilacap, {{ date('d F Y') }}</p>
                <br><br><br>
                <p class="fw-bold text-decoration-underline">( Admin Pengelola )</p>
                <p>Bank Sampah Berseri Sejahtera</p>
            </div>
        </div>
    </div>

</body>
</html>