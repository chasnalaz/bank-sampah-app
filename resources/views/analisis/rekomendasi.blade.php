@extends('layouts.main')

@section('title', 'Rekomendasi Penjualan')

@section('content')
<div class="container-fluid pt-3"> {{-- Tambah pt-3 biar ada jarak dari navbar --}}
    
    <div class="row">
        @php
            $colors = ['primary', 'success', 'warning', 'danger', 'info', 'secondary'];
        @endphp

        @forelse($rekomendasi as $index => $sampah)
        @php 
            $themeColor = $colors[$index % count($colors)];
        @endphp

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden card-hover-effect">
                
                {{-- HEADER KARTU --}}
                <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape icon-shape-{{ $themeColor }} rounded-circle me-3">
                            <i class="bi bi-recycle"></i>
                        </div>
                        <h6 class="m-0 font-weight-bold text-dark text-uppercase letter-spacing-1">
                            {{ $sampah->nama_sampah }}
                        </h6>
                    </div>
                    {{-- Badge Jumlah Pengepul --}}
                    <span class="badge bg-light text-muted border rounded-pill">
                        {{ $sampah->tengkulaks->count() }}
                    </span>
                </div>

                <div class="card-body p-0">
                    @if($sampah->tengkulaks->count() > 0)
                        
                        @php 
                            $juara = $sampah->tengkulaks->first(); 
                            $hargaTertinggi = $juara->harga_beli;
                        @endphp

                        <div class="list-group list-group-flush">
                            
                            {{-- JUARA 1 (LAYOUT BARU: VERTIKAL / CENTERED) --}}
                            {{-- Solusi biar gak mepet: Kita buat rata tengah (centered) --}}
                            <div class="list-group-item py-4 px-3 border-bottom-0 text-center" style="background: linear-gradient(to bottom, rgba(25, 135, 84, 0.03), transparent);">
                                
                                {{-- 1. Badge --}}
                                <div class="mb-2">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-1">
                                        <i class="bi bi-trophy-fill me-1"></i> Harga Tertinggi
                                    </span>
                                </div>
                                
                                {{-- 2. Harga (Fokus Utama - Besar) --}}
                                <h2 class="fw-bold text-success mb-1" style="font-size: 2.5rem;">
                                    <span class="fs-5 text-muted fw-normal align-top me-1">Rp</span>{{ number_format($juara->harga_beli, 0, ',', '.') }}
                                </h2>

                                {{-- 3. Nama Tengkulak (Di Bawah Harga) --}}
                                <p class="text-muted text-uppercase fw-bold small mb-0 letter-spacing-1">
                                    Pengepul: <span class="text-dark">{{ $juara->nama_tengkulak }}</span>
                                </p>
                            </div>

                            {{-- ALTERNATIF --}}
                            @if($sampah->tengkulaks->count() > 1)
                                <div class="bg-light py-2 px-4 border-top border-bottom">
                                    <small class="fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Opsi Lainnya</small>
                                </div>

                                @foreach($sampah->tengkulaks->skip(1) as $alt)
                                <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center hover-bg-gray">
                                    <div class="d-flex align-items-center">
                                        <span class="text-muted fw-bold me-3 small" style="width: 20px;">{{ $loop->iteration + 1 }}</span>
                                        <h6 class="mb-0 text-dark small fw-bold">{{ $alt->nama_tengkulak }}</h6>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-secondary small">
                                            Rp {{ number_format($alt->harga_beli, 0, ',', '.') }}
                                        </div>
                                        <small class="text-danger fw-bold" style="font-size: 0.7rem;">
                                            <i class="bi bi-arrow-down-short"></i> 
                                            {{ number_format($hargaTertinggi - $alt->harga_beli, 0, ',', '.') }}
                                        </small>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>

                    @else
                        {{-- EMPTY STATE --}}
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted fs-3 opacity-25"></i>
                            <p class="small text-muted mb-0 mt-2">Belum ada data.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <h5 class="text-gray-400">Belum ada jenis sampah.</h5>
        </div>
        @endforelse
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .letter-spacing-1 { letter-spacing: 1px; }
    
    .icon-shape {
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 0.9rem;
    }
    .icon-shape-primary { background: #4e73df; }
    .icon-shape-success { background: #1cc88a; }
    .icon-shape-warning { background: #f6c23e; }
    .icon-shape-danger { background: #e74a3b; }
    .icon-shape-info { background: #36b9cc; }
    .icon-shape-secondary { background: #858796; }

    .hover-bg-gray:hover { background-color: #f8f9fa; }
    .card-hover-effect { transition: transform 0.2s; }
    .card-hover-effect:hover { transform: translateY(-3px); }
</style>
@endsection