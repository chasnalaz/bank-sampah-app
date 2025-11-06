@extends('layouts.main')

@section('title', 'Dashboard Petugas')

@section('content')
    {{-- ... (Salam pembuka, notifikasi, kartu statistik tidak berubah) ... --}}
    <div class="mb-4">
        <h3>Halo, {{ Auth::user()->name }}!</h3>
    </div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-6 col-md-6 mb-4">
            <a href="{{ route('penjemputan.tugas') }}#baru-tab" class="card-link">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Permintaan Baru Tersedia (Belum Diambil)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $permintaanBaruCount }} Tugas
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-inbox-fill fs-2 text-gray-300"></i>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">Klik untuk melihat daftar tugas</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-6 col-md-6 mb-4">
            <a href="{{ route('penjemputan.tugas') }}#aktif-tab" class="card-link">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Tugas Aktif Saya
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $tugasAktifCount }} Tugas
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-truck fs-2 text-gray-300"></i>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">Klik untuk melihat tugas Anda</small>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <hr class="my-4">
    {{-- AKHIR KARTU STATISTIK BARU --}}

    {{-- DAFTAR HARGA SAMPAH (dari kode lama Anda) --}}
    <div>
        <h5 class="mb-3">Daftar Harga Sampah Hari Ini</h5>
        <div class="row g-3">
            @forelse ($daftarHargaSampah as $sampah)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card shadow-sm border-0 text-center">
                        <div class="card-body">
                            {{-- <-- PERBAIKAN DI SINI --}}
                            <h6 class="card-title text-muted small">{{ $sampah->nama_sampah }}</h6>
                            <p class="card-text fw-bold text-primary mb-0">
                                Rp {{ number_format($sampah->harga_per_kg, 0, ',', '.') }}/kg
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Belum ada data jenis sampah.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@push('styles')
{{-- ... (CSS tidak berubah) ... --}}
<style>
    .card-link {
        text-decoration: none;
        color: inherit;
    }
    .card-link .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        transition: all 0.2s ease-in-out;
    }
    .border-left-warning { border-left: .25rem solid #f6c23e !important; }
    .border-left-info   { border-left: .25rem solid #36b9cc !important; }
</style>
@endpush

@endsection