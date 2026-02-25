@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')

{{-- 1. BARIS KARTU STATUS (SERAGAM & SEIMBANG) --}}
<div class="row g-3 mb-4">
    
    {{-- Card 1: Request Jemput --}}
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center p-3 d-flex flex-column justify-content-center">
                <div class="mb-2">
                    <i class="bi bi-truck fs-2 text-danger"></i>
                </div>
                <h4 class="fw-bold mb-0 text-dark">{{ $jemputanPending }}</h4>
                <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.70rem;">Request Jemput</span>
                
                {{-- INFO TAMBAHAN (FILLER) --}}
                <div class="mt-2 pt-2 border-top">
                    <small class="text-muted">
                        <i class="bi bi-exclamation-circle me-1"></i> Perlu Tindakan
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Petugas Siap --}}
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center p-3 d-flex flex-column justify-content-center">
                <div class="mb-2">
                    <i class="bi bi-person-badge fs-2 text-primary"></i>
                </div>
                <h4 class="fw-bold mb-0 text-dark">{{ $petugasHadir }} Orang</h4>
                <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.70rem;">Petugas Siap</span>

                {{-- INFO TAMBAHAN (TOTAL PETUGAS) --}}
                <div class="mt-2 pt-2 border-top">
                    <small class="text-muted">
                        <i class="bi bi-people me-1"></i> Dari {{ $totalPetugas }} Terdaftar
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Total Saldo --}}
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center p-3 d-flex flex-column justify-content-center">
                <div class="mb-2">
                    <i class="bi bi-wallet2 fs-2 text-success"></i>
                </div>
                <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</h4>
                <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.70rem;">Total Saldo</span>

                {{-- INFO TAMBAHAN (LABEL STATIS) --}}
                <div class="mt-2 pt-2 border-top">
                    <small class="text-muted">
                        <i class="bi bi-safe me-1"></i> Aset Nasabah
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 4: Total Sampah (YANG BIKIN MASALAH TADI) --}}
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center p-3 d-flex flex-column justify-content-center">
                <div class="mb-2">
                    <i class="bi bi-recycle fs-2 text-info"></i>
                </div>
                
                {{-- ANGKA UTAMA (BULAN INI) --}}
                <h4 class="fw-bold mb-0 text-dark">
                    {{ number_format($totalBeratBulanIni, 1) }} Kg
                </h4>
                <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.70rem;">
                    Sampah Bulan Ini
                </span>

                {{-- INFO TAMBAHAN (ALL TIME) --}}
                <div class="mt-2 pt-2 border-top">
                    <small class="text-muted fw-bold">
                        <i class="bi bi-layers-fill me-1"></i> Total: {{ number_format($totalBeratAllTime, 0, ',', '.') }} Kg
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. BARIS KONTEN UTAMA --}}
<div class="row g-4">
    
    {{-- KOLOM KIRI: JADWAL OPERASIONAL --}}
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="bi bi-calendar-event me-2 text-primary"></i>Jadwal Operasional
                </h6>
                
                {{-- Logika Badge Live --}}
                @php
                    $sekarang  = \Carbon\Carbon::now('Asia/Jakarta');
                    $tglBukaCarbon = $tglBuka ? \Carbon\Carbon::parse($tglBuka) : null;
                    $waktuBukaObj = \Carbon\Carbon::createFromTimeString($jamBuka, 'Asia/Jakarta');
                    $waktuTutupObj = \Carbon\Carbon::createFromTimeString($jamTutup, 'Asia/Jakarta');

                    $isHariH = $tglBukaCarbon && $tglBukaCarbon->isToday();
                    $isJamKerja = $sekarang->between($waktuBukaObj, $waktuTutupObj);
                @endphp

                @if($isHariH && $isJamKerja)
                    <span class="badge bg-success animate__animated animate__pulse animate__infinite">LIVE (SEDANG BUKA)</span>
                @elseif($isHariH && !$isJamKerja)
                    <span class="badge bg-warning text-dark">HARI INI (TUTUP)</span>
                @else
                    <span class="badge bg-secondary">TUTUP</span>
                @endif
            </div>

            <div class="card-body d-flex flex-column justify-content-center">
                {{-- TAMPILAN TANGGAL BESAR (CENTERED) --}}
                <div class="text-center mb-4">
                    <p class="text-muted mb-1 small text-uppercase fw-bold">Jadwal Buka Berikutnya:</p>
                    <h2 class="fw-bold text-dark">
                        @if($tglBuka)
                            {{ \Carbon\Carbon::parse($tglBuka)->translatedFormat('l, d F Y') }}
                        @else
                            Belum Diatur
                        @endif
                    </h2>
                    <div class="h5 text-primary">
                        <i class="bi bi-clock me-1"></i> {{ $jamBuka }} - {{ $jamTutup }} WIB
                    </div>
                </div>

                {{-- HANYA ADMIN YANG BISA LIHAT FORM INI --}}
                @if(Auth::user()->role == 'admin')
                    <div class="bg-light p-3 rounded">
                        <form action="{{ route('pengaturan.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row g-2 align-items-end">
                                <div class="col-md-5">
                                    <label class="small text-muted fw-bold mb-1">Tanggal</label>
                                    <input type="date" name="tanggal_buka" class="form-control form-control-sm" value="{{ $tglBuka }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="small text-muted fw-bold mb-1">Buka</label>
                                    <input type="time" name="jam_buka" class="form-control form-control-sm" value="{{ $jamBuka }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="small text-muted fw-bold mb-1">Tutup</label>
                                    <input type="time" name="jam_tutup" class="form-control form-control-sm" value="{{ $jamTutup }}" required>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary btn-sm w-100" title="Simpan">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    {{-- UNTUK KETUA: TAMPILKAN PESAN SAJA --}}
                    <div class="alert alert-light text-center small text-muted border-0 mb-0">
                        <i class="bi bi-info-circle me-1"></i> Hubungi Admin untuk mengubah jadwal operasional.
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: GRAFIK DONAT --}}
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="bi bi-pie-chart me-2 text-primary"></i>Komposisi Sampah
                </h6>
            </div>
            <div class="card-body">
                <div style="height: 250px; position: relative;">
                    <canvas id="sampahChart"></canvas>
                </div>
                <div class="mt-3 text-center small text-muted">
                    *5 jenis sampah terbanyak (All Time)
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const labels = {!! json_encode($chartLabels) !!};
        const dataValues = {!! json_encode($chartValues) !!};

        const ctx = document.getElementById('sampahChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, usePointStyle: true, padding: 20 }
                    }
                },
                cutout: '70%',
            }
        });
    });
</script>

@endsection