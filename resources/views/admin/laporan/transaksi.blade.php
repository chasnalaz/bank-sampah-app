@extends('layouts.main')

@section('title', 'Laporan Keuangan & Arus Kas')

@section('content')
<div class="container-fluid">
    
    {{-- FILTER --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form action="{{ route('laporan.transaksi') }}" method="GET" class="row align-items-end">
                <div class="col-md-3">
                    <label class="fw-bold small">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="fw-bold small">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Filter</button>
                    <a href="{{ route('laporan.transaksi') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                    {{-- Tombol Cetak nanti menyusul --}}
                    <a href="{{ route('laporan.transaksi.cetak', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                    target="_blank" 
                    class="btn btn-danger">
                        <i class="bi bi-printer-fill"></i> Cetak
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- RINGKASAN PROFIT --}}
    <div class="row mb-4">
    
    {{-- 1. KARTU PEMASUKAN --}}
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">
                {{-- Ikon Hijau (Uang Masuk) --}}
                <div class="mb-3">
                    <i class="bi bi-arrow-down-circle-fill fs-1 text-success"></i>
                </div>
                {{-- Nominal --}}
                <h3 class="fw-bold text-dark mb-1">
                    Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                </h3>
                {{-- Label --}}
                <p class="text-muted mb-0 small text-uppercase spacing-1">Total Pemasukan</p>
            </div>
        </div>
    </div>

    {{-- 2. KARTU PENGELUARAN --}}
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">
                {{-- Ikon Merah (Uang Keluar) --}}
                <div class="mb-3">
                    <i class="bi bi-arrow-up-circle-fill fs-1 text-danger"></i>
                </div>
                {{-- Nominal --}}
                <h3 class="fw-bold text-dark mb-1">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </h3>
                {{-- Label --}}
                <p class="text-muted mb-0 small text-uppercase spacing-1">Total Pengeluaran</p>
            </div>
        </div>
    </div>

    {{-- 3. KARTU KEUNTUNGAN --}}
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">
                {{-- Ikon Biru/Wallet (Saldo/Profit) --}}
                <div class="mb-3">
                    <i class="bi bi-wallet-fill fs-1 text-primary"></i>
                </div>
                {{-- Nominal (Warna berubah merah jika rugi) --}}
                <h3 class="fw-bold {{ $keuntungan < 0 ? 'text-danger' : 'text-dark' }} mb-1">
                    Rp {{ number_format($keuntungan, 0, ',', '.') }}
                </h3>
                {{-- Label --}}
                <p class="text-muted mb-0 small text-uppercase spacing-1">Keuntungan Bersih</p>
            </div>
        </div>
    </div>
    </div>

    {{-- TAB NAVIGASI --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <ul class="nav nav-tabs card-header-tabs" id="laporanTab" role="tablist">
            {{-- Tab 1: Pengeluaran (Default Active) --}}
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-danger" id="pengeluaran-tab" data-bs-toggle="tab" data-bs-target="#pengeluaran" type="button" role="tab" aria-controls="pengeluaran" aria-selected="true">
                    <i class="bi bi-arrow-up-circle me-2"></i> Pengeluaran (Nasabah)
                </button>
            </li>
            {{-- Tab 2: Pemasukan --}}
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-success" id="pemasukan-tab" data-bs-toggle="tab" data-bs-target="#pemasukan" type="button" role="tab" aria-controls="pemasukan" aria-selected="false">
                    <i class="bi bi-arrow-down-circle me-2"></i> Pemasukan (Tengkulak)
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="laporanTabContent">
            
            {{-- ISI TAB 1: PENGELUARAN --}}
            <div class="tab-pane fade show active" id="pengeluaran" role="tabpanel" aria-labelledby="pengeluaran-tab">
                <h6 class="mb-3 text-muted small text-uppercase">Data Riwayat Transaksi Dengan Nasabah</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Nama Nasabah</th>
                                <th class="text-center">Nominal (Rp)</th>
                                <th class="text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transaksi as $t)
                            <tr>
                                <td>{{ $t->created_at->format('d M Y') }}</td>
                                <td>{{ $t->nasabah->nama ?? 'Nasabah Terhapus' }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('transaksi.struk', $t->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak Struk">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Tidak ada data pengeluaran.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ISI TAB 2: PEMASUKAN --}}
            <div class="tab-pane fade" id="pemasukan" role="tabpanel" aria-labelledby="pemasukan-tab">
                <h6 class="mb-3 text-muted small text-uppercase">Data Riwayat Penjualan Ke Tengkulak</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Tengkulak</th>
                                <th class="text-center">Pendapatan (Rp)</th>
                                <th class="text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($penjualan as $jual)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($jual->tanggal_jual)->format('d M Y') }}</td>
                                <td>{{ $jual->tengkulak->nama_tengkulak ?? '-' }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($jual->total_pendapatan, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('penjualan.struk', $jual->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak Struk">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada data pemasukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

</div>
@endsection