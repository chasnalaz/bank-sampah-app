@extends('layouts.main')

@section('title', 'Dashboard Petugas')

@section('content')

@php
    // 1. Ambil Waktu Sekarang (Paksa WIB)
    $sekarang   = \Carbon\Carbon::now('Asia/Jakarta');
    
    // 2. Cek Tanggal (Format Y-m-d sesuai zona waktu Jakarta)
    $hariIniStr = $sekarang->format('Y-m-d');
    $isHariH    = ($globalTglBuka == $hariIniStr);
    
    // 3. Cek Jam (Buat objek Carbon dari string jam admin, set ke zona Jakarta)
    $buka       = \Carbon\Carbon::createFromTimeString($globalJamBuka, 'Asia/Jakarta');
    $tutup      = \Carbon\Carbon::createFromTimeString($globalJamTutup, 'Asia/Jakarta');
    
    // 4. Logika Final
    $isJamKerja = $sekarang->between($buka, $tutup);
    $sedangBuka = $isHariH && $isJamKerja;
@endphp

<div class="container-fluid">
    
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <p class="h3 text-muted mb-0">Selamat datang, <strong>{{ Auth::user()->name }}</strong></p>
        </div>
        
        {{-- Badge Status --}}
        @if($sedangBuka)
            <span class="badge bg-success px-3 py-2 shadow-sm rounded-pill">
                <i class="bi bi-shop me-1"></i> OPERASIONAL BUKA
            </span>
        @else
            <span class="badge bg-secondary px-3 py-2 shadow-sm rounded-pill">
                <i class="bi bi-lock-fill me-1"></i> TUTUP
            </span>
        @endif
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('status_updated'))
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-info-circle me-1"></i> {{ session('status_updated') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    @if($sedangBuka)
        {{-- === BAGIAN 1: STATUS KETERSEDIAAN (CLEAN LOOK) === --}}
        {{-- Hapus 'border-left-primary' agar garis biru hilang --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body py-4 px-4">
                <div class="row align-items-center">
                    <div class="col-md-7 col-12 mb-3 mb-md-0">
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-person-check-fill text-success me-2"></i>Status Ketersediaan
                        </h5>
                        <p class="mb-0 text-muted small">
                            Pastikan status Anda <strong>SIAP</strong> agar Admin tahu Anda bisa bekerja hari ini.
                        </p>
                    </div>

                    <div class="col-md-5 col-12 text-md-end">
                        <form action="{{ route('petugas.status.update') }}" method="POST">
                            @csrf
                            <div class="btn-group shadow-sm w-100 w-md-auto" role="group">
                                <button type="submit" name="status_tugas" value="siap" 
                                    class="btn px-4 py-2 {{ auth()->user()->status_tugas == 'siap' ? 'btn-success' : 'btn-outline-success' }}">
                                    <i class="bi bi-check-circle-fill me-1"></i> SIAP
                                </button>
                                <button type="submit" name="status_tugas" value="izin" 
                                    class="btn px-4 py-2 {{ auth()->user()->status_tugas == 'izin' ? 'btn-danger' : 'btn-outline-danger' }}">
                                    <i class="bi bi-x-circle-fill me-1"></i> IZIN
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- === BAGIAN 2: STATISTIK 3 KOLOM SEJAJAR (SYMMETRIC LOOK) === --}}
        <h6 class="fw-bold text-gray-800 mb-3"><i class="bi bi-activity me-1"></i> Monitoring Hari Ini</h6>
        
        <div class="row g-3 mb-4">
            {{-- KARTU 1: WAITING LIST --}}
            <div class="col-md-4">
                <a href="{{ route('penjemputan.tugas') }}#baru-tab" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-scale card-stat-red">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-uppercase small fw-bold text-danger-emphasis mb-1">Waiting List</div>
                                <div class="h2 mb-0 fw-bold text-danger">{{ $permintaanBaruCount }}</div>
                                <small class="text-muted">Permintaan Baru</small>
                            </div>
                            <div class="icon-circle bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-exclamation-lg fs-3"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- KARTU 2: TUGAS SAYA --}}
            <div class="col-md-4">
                <a href="{{ route('penjemputan.tugas') }}#aktif-tab" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-scale card-stat-blue">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-uppercase small fw-bold text-primary-emphasis mb-1">Tugas Saya</div>
                                <div class="h2 mb-0 fw-bold text-primary">{{ $tugasAktifCount }}</div>
                                <small class="text-muted">Sedang Proses</small>
                            </div>
                            <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-truck fs-3"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- KARTU 3: KINERJA (GABUNGAN) --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 card-stat-orange bg-gradient-orange text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <div class="text-uppercase small fw-bold text-white-50">Kinerja Hari Ini</div>
                                <div class="h2 mb-0 fw-bold">{{ number_format($totalBeratHariIni, 1, ',', '.') }} <small class="fs-6">kg</small></div>
                            </div>
                            <div class="icon-circle bg-white bg-opacity-25 text-white">
                                <i class="bi bi-trophy-fill fs-4"></i>
                            </div>
                        </div>
                        <div class="border-top border-white border-opacity-25 pt-2">
                            <small class="text-white-50">Tersalurkan:</small> 
                            <span class="fw-bold">Rp {{ number_format($totalUangHariIni, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- === TAMPILAN TUTUP (SAMA SEPERTI SEBELUMNYA) === --}}
        <div class="card shadow mb-4 bg-light border-0 overflow-hidden">
            <div class="card-body p-0"> 
                <div class="row g-0">
                    <div class="col-md-6 p-5 text-center border-end d-flex flex-column justify-content-center align-items-center">
                        <div class="mb-3 text-secondary">
                            <i class="bi bi-shop-window" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="fw-bold text-secondary mb-2">Bank Sampah Tutup</h3>
                        <p class="text-muted mb-0 px-4">
                            Sistem sedang <strong>offline</strong>. Absensi & tugas dinonaktifkan.
                        </p>
                    </div>
                    <div class="col-md-6 p-5 bg-white d-flex flex-column justify-content-center align-items-center">
                        <small class="text-uppercase text-primary fw-bold tracking-wide mb-2">
                            <i class="bi bi-calendar-event me-1"></i> Jadwal Buka Berikutnya
                        </small>
                        <div class="display-4 fw-bold text-dark mb-1">
                            {{ $globalTglBuka ? \Carbon\Carbon::parse($globalTglBuka)->format('d') : '--' }}
                        </div>
                        <div class="h4 text-gray-800 mb-3">
                            {{ $globalTglBuka ? \Carbon\Carbon::parse($globalTglBuka)->translatedFormat('F Y') : 'Belum Diatur' }}
                        </div>
                        <div class="badge bg-light text-dark border px-3 py-2 fs-6">
                            <i class="bi bi-clock me-1"></i> 
                            {{ $globalJamBuka }} - {{ $globalJamTutup }} WIB
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Daftar Harga --}}
    <h6 class="fw-bold text-gray-800 mt-4 mb-3">
        <i class="bi bi-tags-fill me-1"></i> Informasi Harga Sampah
    </h6>
    <div class="row g-3">
        @forelse ($daftarHargaSampah as $sampah)
            <div class="col-xl-3 col-md-4 col-6">
                <div class="card border-0 shadow-sm h-100 hover-scale">
                    <div class="card-body text-center py-3">
                        <div class="text-uppercase text-muted small fw-bold mb-1">
                            {{ $sampah->nama_sampah }}
                        </div>
                        <div class="h5 mb-0 fw-bold text-primary">
                            Rp {{ number_format($sampah->harga_per_kg, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light text-center border">Data harga belum tersedia.</div>
            </div>
        @endforelse
    </div>

</div>

@endsection

@push('styles')
<style>
    /* Animasi Hover */
    .hover-scale { transition: transform 0.2s, box-shadow 0.2s; }
    .hover-scale:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; cursor: pointer; }
    
    /* Dekorasi Icon Bulat */
    .icon-circle {
        height: 50px; width: 50px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }

    /* Warna Gradient Card Kinerja */
    .bg-gradient-orange {
        background: linear-gradient(45deg, #f6c23e, #fd7e14);
    }
</style>
@endpush