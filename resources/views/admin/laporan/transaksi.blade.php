@extends('layouts.main')

@section('title', 'Laporan Keuangan & Arus Kas')

@section('content')
<div class="container-fluid">
    
    {{-- 1. HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-wallet2 me-2 text-primary"></i>Laporan Keuangan
            </h5>
            <small class="text-muted fst-italic">
                • {{ $labelFilter ?? 'Bulan Ini' }}
            </small>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('laporan.transaksi.cetak', request()->all()) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm">
                <i class="bi bi-printer-fill me-1"></i> Cetak PDF
            </a>
            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalFilterAdmin">
                <i class="bi bi-sliders me-1"></i> Filter Waktu
            </button>
        </div>
    </div>

    {{-- 2. KARTU RINGKASAN --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-arrow-down-circle-fill fs-1 text-success mb-2 d-block"></i>
                    <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
                    <p class="text-muted mb-0 small text-uppercase">Total Pemasukan</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-arrow-up-circle-fill fs-1 text-danger mb-2 d-block"></i>
                    <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
                    <p class="text-muted mb-0 small text-uppercase">Total Pengeluaran</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-wallet-fill fs-1 text-primary mb-2 d-block"></i>
                    <h3 class="fw-bold {{ $keuntungan < 0 ? 'text-danger' : 'text-dark' }} mb-1">
                        Rp {{ number_format($keuntungan, 0, ',', '.') }}
                    </h3>
                    <p class="text-muted mb-0 small text-uppercase">Keuntungan Bersih</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. TABEL DATA --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs" id="laporanTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold text-danger" id="pengeluaran-tab" data-bs-toggle="tab" data-bs-target="#pengeluaran" type="button">
                        <i class="bi bi-arrow-up-circle me-2"></i> Pengeluaran
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold text-success" id="pemasukan-tab" data-bs-toggle="tab" data-bs-target="#pemasukan" type="button">
                        <i class="bi bi-arrow-down-circle me-2"></i> Pemasukan
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                
                {{-- TAB 1: PENGELUARAN --}}
                <div class="tab-pane fade show active" id="pengeluaran">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover w-100">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Tanggal</th>
                                    <th>Nama Nasabah</th>
                                    <th class="text-end">Nominal (Rp)</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transaksi as $t)
                                <tr>
                                    <td class="text-center">{{ $t->created_at->format('d M Y') }}</td>
                                    <td>{{ $t->nasabah->nama ?? 'Terhapus' }}</td>
                                    <td class="text-end fw-bold text-danger">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('transaksi.struk', $t->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer-fill"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination Link --}}
                    <div class="d-flex justify-content-end mt-3">
                        {{ $transaksi->links() }}
                    </div>
                </div>

                {{-- TAB 2: PEMASUKAN --}}
                <div class="tab-pane fade" id="pemasukan">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover w-100">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Tanggal</th>
                                    <th>Tengkulak</th>
                                    <th class="text-end">Pendapatan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($penjualan as $jual)
                                <tr>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($jual->tanggal_jual)->format('d M Y') }}</td>
                                    <td>{{ $jual->tengkulak->nama_tengkulak ?? '-' }}</td>
                                    <td class="text-end fw-bold text-success">Rp {{ number_format($jual->total_pendapatan, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination Link --}}
                    <div class="d-flex justify-content-end mt-3">
                        {{ $penjualan->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- MODAL FILTER --}}
<div class="modal fade" id="modalFilterAdmin" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold fs-6">Filter Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <form action="{{ route('laporan.transaksi') }}" method="GET">
                    
                    {{-- Opsi Hari Ini --}}
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="filter_jenis" value="hari_ini" id="admin_hari_ini" 
                            {{ request('filter_jenis') == 'hari_ini' ? 'checked' : '' }}>
                        <label class="form-check-label w-100" for="admin_hari_ini">Hari Ini</label>
                    </div>

                    {{-- Opsi 7 Hari --}}
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="filter_jenis" value="7_hari" id="admin_7_hari"
                            {{ request('filter_jenis') == '7_hari' ? 'checked' : '' }}>
                        <label class="form-check-label w-100" for="admin_7_hari">7 Hari Terakhir</label>
                    </div>

                    {{-- Opsi Bulan (DEFAULT CHECKED jika belum ada filter) --}}
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="filter_jenis" value="bulan" id="admin_bulan"
                            {{ request('filter_jenis') == 'bulan' || !request()->has('filter_jenis') ? 'checked' : '' }}>
                        <label class="form-check-label w-100" for="admin_bulan">Pilih Bulan</label>
                    </div>
                    
                    <div class="ms-4 mb-3 collapse {{ (request('filter_jenis') == 'bulan' || !request()->has('filter_jenis')) ? 'show' : '' }}" id="areaBulanAdmin">
                        <div class="d-flex gap-2">
                            <select name="bulan" class="form-select form-select-sm">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ (request('bulan') ?? date('m')) == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="tahun" class="form-select form-select-sm">
                                @foreach(range(date('Y'), 2023) as $y)
                                    <option value="{{ $y }}" {{ (request('tahun') ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Opsi Custom --}}
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="filter_jenis" value="custom" id="admin_custom"
                            {{ request('filter_jenis') == 'custom' ? 'checked' : '' }}>
                        <label class="form-check-label w-100" for="admin_custom">Pilih Tanggal</label>
                    </div>
                    <div class="ms-4 mb-3 collapse {{ request('filter_jenis') == 'custom' ? 'show' : '' }}" id="areaCustomAdmin">
                        <input type="date" name="tgl_awal" class="form-control form-control-sm mb-2" value="{{ request('tgl_awal') }}">
                        <input type="date" name="tgl_akhir" class="form-control form-control-sm" value="{{ request('tgl_akhir') }}">
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Tampilkan Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Script Toggle Area Filter
    document.addEventListener("DOMContentLoaded", function() {
        const radios = document.querySelectorAll('input[name="filter_jenis"]');
        const areaBulan = document.getElementById('areaBulanAdmin');
        const areaCustom = document.getElementById('areaCustomAdmin');

        function toggleArea() {
            areaBulan.classList.remove('show');
            areaCustom.classList.remove('show');
            
            const checked = document.querySelector('input[name="filter_jenis"]:checked');
            if (checked) {
                if (checked.value === 'bulan') areaBulan.classList.add('show');
                else if (checked.value === 'custom') areaCustom.classList.add('show');
            }
        }

        radios.forEach(radio => radio.addEventListener('change', toggleArea));
        toggleArea(); // Run on init
    });
</script>
@endsection