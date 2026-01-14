@extends('layouts.main')
@section('title', 'Analisis & Statistik Cerdas')

@section('content')
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <ul class="nav nav-tabs card-header-tabs" id="analisisTab" role="tablist">
                {{-- TAB 1: VOLUME --}}
                <li class="nav-item">
                    <button class="nav-link active fw-bold" id="volume-tab" data-bs-toggle="tab" data-bs-target="#volume" type="button">
                        <i class="bi bi-bar-chart-fill me-2 text-primary"></i>Volume Sampah
                    </button>
                </li>
                {{-- TAB 2: NASABAH --}}
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="nasabah-tab" data-bs-toggle="tab" data-bs-target="#nasabah" type="button">
                        <i class="bi bi-people-fill me-2 text-success"></i>Partisipasi Nasabah
                    </button>
                </li>
                {{-- TAB 3: PETUGAS --}}
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="petugas-tab" data-bs-toggle="tab" data-bs-target="#petugas" type="button">
                        <i class="bi bi-person-badge-fill me-2 text-warning"></i>Kinerja Petugas
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content p-2">

                {{-- KONTEN 1: VOLUME SAMPAH --}}
                <div class="tab-pane fade show active" id="volume">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-6 text-center border-end">
                            <h6 class="text-muted text-uppercase">Bulan Lalu</h6>
                            <h3>{{ $beratLalu }} <small class="fs-6 text-muted">kg</small></h3>
                        </div>
                        <div class="col-md-6 text-center">
                            <h6 class="text-muted text-uppercase">Bulan Ini</h6>
                            <h3 class="{{ $analisisVolume['status'] == 'success' ? 'text-success' : 'text-danger' }}">
                                {{ $beratIni }} <small class="fs-6 text-muted">kg</small>
                            </h3>
                        </div>
                    </div>

                    {{-- KOTAK SARAN CERDAS --}}
                    <div class="alert alert-{{ $analisisVolume['status'] }} border-0 shadow-sm">
                        <h5 class="alert-heading fw-bold"><i class="bi bi-lightbulb-fill me-2"></i>Analisis Sistem:</h5>
                        <p class="mb-1"><strong>Deskripsi:</strong> {{ $analisisVolume['deskripsi'] }}</p>
                        <p class="mb-1"><strong>Kesimpulan:</strong> {{ $analisisVolume['kesimpulan'] }}</p>
                        <hr>
                        <p class="mb-0 fst-italic fw-bold">
                            <i class="bi bi-arrow-right-circle me-1"></i>
                            Rekomendasi: "{{ $analisisVolume['saran'] }}"
                        </p>
                    </div>
                </div>

                {{-- KONTEN 2: NASABAH --}}
                <div class="tab-pane fade" id="nasabah">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold">Rasio Keaktifan Nasabah</span>
                            <span>{{ number_format($persenAktif, 1) }}% ({{ $nasabahAktif }} dari {{ $totalNasabah }} orang)</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-{{ $analisisNasabah['status'] }}" role="progressbar" 
                                 style="width: {{ $persenAktif }}%">
                                 {{ number_format($persenAktif, 0) }}% Aktif
                            </div>
                        </div>
                    </div>

                    {{-- KOTAK SARAN CERDAS --}}
                    <div class="alert alert-{{ $analisisNasabah['status'] }} border-0 shadow-sm">
                        <h5 class="alert-heading fw-bold"><i class="bi bi-search me-2"></i>Analisis Sistem:</h5>
                        <p class="mb-2"><strong>Kesimpulan:</strong> {{ $analisisNasabah['kesimpulan'] }}</p>
                        <hr>
                        <p class="mb-0 fst-italic fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            Rekomendasi: "{{ $analisisNasabah['saran'] }}"
                        </p>
                    </div>
                </div>

                {{-- KONTEN 3: PETUGAS --}}
                <div class="tab-pane fade" id="petugas">
                    <div class="text-center mb-4">
                        @if($topPetugas)
                            <div class="d-inline-block p-4 rounded-circle bg-light border border-warning shadow-sm mb-3" style="width: 120px; height: 120px;">
                                <i class="bi bi-trophy-fill text-warning fs-1"></i>
                            </div>
                            <h4 class="fw-bold">{{ $topPetugas['nama'] }}</h4>
                            <span class="badge bg-warning text-dark">Petugas Terbaik Bulan Ini</span>
                            <p class="text-muted mt-2">Menangani {{ $topPetugas['total'] }} Transaksi</p>
                        @else
                            <p class="text-muted fst-italic py-4">Belum ada data kinerja petugas bulan ini.</p>
                        @endif
                    </div>

                    {{-- KOTAK SARAN CERDAS --}}
                    <div class="alert alert-info border-0 shadow-sm">
                        <h5 class="alert-heading fw-bold"><i class="bi bi-info-circle-fill me-2"></i>Analisis SDM:</h5>
                        <p class="mb-0 fst-italic">
                            "{{ $analisisPetugas['saran'] }}"
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection