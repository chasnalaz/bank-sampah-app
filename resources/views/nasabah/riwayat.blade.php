@extends('layouts.main')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            {{-- BAGIAN KIRI: JUDUL & LABEL FILTER --}}
            <div>
                <h6 class="m-0 fw-bold text-dark">
                    <i class="bi bi-clock-history me-2 text-muted"></i>Log Aktivitas Tabungan
                </h6>
                {{-- Ini Label Keterangan Filternya --}}
                <small class="text-muted fst-italic ms-1" style="font-size: 0.85rem;">
                    • {{ $labelFilter }}
                </small>
            </div>
            
            {{-- BAGIAN KANAN: TOMBOL FILTER (WARNA NETRAL/ABU-ABU) --}}
            {{-- Perubahan: btn-outline-primary JADI btn-outline-secondary --}}
            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalFilter">
                <i class="bi bi-sliders me-1"></i> Filter
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tanggal</th>
                            <th>Jenis Transaksi</th>
                            <th>Detail Sampah</th>
                            <th>Petugas</th>
                            <th class="text-end pe-4">Nominal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($semuaTransaksi as $trx)
                        <tr>
                            <td class="ps-4">
                                {{-- REVISI: Hapus bagian Jam/WIB, ambil Tanggal saja --}}
                                {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d F Y') }}
                            </td>
                            <td>
                                @if($trx->jenis_transaksi == 'setor')
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Setor Sampah</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Tarik Tunai</span>
                                @endif
                            </td>
                            <td>
                                @if($trx->jenis_transaksi == 'setor')
                                    {{-- PERBAIKAN: Gunakan relasi 'jenisSampah' untuk ambil namanya --}}
                                    <strong>{{ $trx->jenisSampah->nama_sampah ?? 'Sampah' }}</strong> <br>
                                    
                                    <small class="text-muted">Berat: {{ $trx->berat }} Kg</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $trx->petugas->name ?? 'Sistem' }}</span>
                            </td>
                            <td class="text-end pe-4 fw-bold {{ $trx->jenis_transaksi == 'setor' ? 'text-success' : 'text-danger' }}">
                                {{ $trx->jenis_transaksi == 'setor' ? '+' : '-' }} Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt-cutoff fs-1 d-block mb-2"></i>
                                Belum ada riwayat transaksi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination --}}
        <div class="card-footer bg-white py-3">
            {{ $semuaTransaksi->links('pagination::bootstrap-5') }}
        </div>
    </div>

    {{-- MODAL FILTER WAKTU (Gaya Minimalis) --}}
    <div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm"> {{-- modal-sm biar kecil kayak HP --}}
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold fs-6">Rentang Waktu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <form action="{{ route('nasabah.riwayat') }}" method="GET" id="formFilter">
                        
                        {{-- OPSI 1: HARI INI --}}
                        {{-- TAMBAHKAN position-relative DI SINI --}}
                        <div class="form-check mb-3 position-relative"> 
                            <input class="form-check-input" type="radio" name="filter_jenis" value="hari_ini" id="f_hari_ini" 
                                {{ request('filter_jenis') == 'hari_ini' ? 'checked' : '' }}>
                            <label class="form-check-label w-100 stretched-link" for="f_hari_ini">Hari Ini</label>
                        </div>

                        {{-- OPSI 2: 7 HARI TERAKHIR --}}
                        {{-- TAMBAHKAN position-relative DI SINI --}}
                        <div class="form-check mb-3 position-relative">
                            <input class="form-check-input" type="radio" name="filter_jenis" value="7_hari" id="f_7_hari"
                                {{ request('filter_jenis') == '7_hari' ? 'checked' : '' }}>
                            <label class="form-check-label w-100 stretched-link" for="f_7_hari">7 Hari Terakhir</label>
                        </div>

                        {{-- OPSI 3: PILIH BULAN --}}
                        {{-- TAMBAHKAN position-relative DI SINI --}}
                        <div class="form-check mb-2 position-relative">
                            <input class="form-check-input" type="radio" name="filter_jenis" value="bulan" id="f_bulan"
                                {{ request('filter_jenis') == 'bulan' ? 'checked' : '' }}>
                            {{-- HAPUS stretched-link DI SINI AGAR TIDAK MENGGANGGU DROPDOWN --}}
                            <label class="form-check-label w-100" for="f_bulan">Pilih Bulan</label>
                        </div>
                        {{-- Dropdown Bulan --}}
                        <div class="ms-4 mb-3 collapse {{ request('filter_jenis') == 'bulan' ? 'show' : '' }}" id="areaBulan">
                            <div class="d-flex gap-2">
                                <select name="bulan" class="form-select form-select-sm">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endforeach
                                </select>
                                <select name="tahun" class="form-select form-select-sm">
                                    @foreach(range(date('Y'), 2023) as $y)
                                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- OPSI 4: PILIH TANGGAL (CUSTOM) --}}
                        {{-- TAMBAHKAN position-relative DI SINI --}}
                        <div class="form-check mb-2 position-relative">
                            <input class="form-check-input" type="radio" name="filter_jenis" value="custom" id="f_custom"
                                {{ request('filter_jenis') == 'custom' ? 'checked' : '' }}>
                            {{-- HAPUS stretched-link DI SINI AGAR TIDAK MENGGANGGU INPUT TANGGAL --}}
                            <label class="form-check-label w-100" for="f_custom">Pilih Tanggal</label>
                        </div>
                        {{-- Input Tanggal --}}
                        <div class="ms-4 mb-3 collapse {{ request('filter_jenis') == 'custom' ? 'show' : '' }}" id="areaCustom">
                            <input type="date" name="tgl_awal" class="form-control form-control-sm mb-2" value="{{ request('tgl_awal') }}">
                            <input type="date" name="tgl_akhir" class="form-control form-control-sm" value="{{ request('tgl_akhir') }}">
                        </div>

                        {{-- TOMBOL TERAPKAN --}}
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Terapkan Filter</button>
                            
                            @if(request()->has('filter_jenis'))
                                <a href="{{ route('nasabah.riwayat') }}" class="btn btn-link w-100 btn-sm text-muted text-decoration-none mt-2">Hapus Filter</a>
                            @endif
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT SEDERHANA UNTUK BUKA/TUTUP MENU --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const radios = document.querySelectorAll('input[name="filter_jenis"]');
            const areaBulan = document.getElementById('areaBulan');
            const areaCustom = document.getElementById('areaCustom');

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Tutup semua dulu
                    areaBulan.classList.remove('show');
                    areaCustom.classList.remove('show');

                    // Buka yang dipilih
                    if (this.value === 'bulan') {
                        areaBulan.classList.add('show');
                    } else if (this.value === 'custom') {
                        areaCustom.classList.add('show');
                    }
                });
            });
        });
    </script>
@endsection