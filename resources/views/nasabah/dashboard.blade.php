@extends('layouts.nasabah-mobile')

@section('title', 'Dashboard Nasabah')

@section('content')
    {{-- 1. Header dengan Logo --}}
    <div class="text-center mb-4">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" height="60">
    </div>

    {{-- Salam Pembuka & Saldo dalam satu kartu --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    {{-- 2. Menambah Sentuhan Warna Oranye --}}
                    <h5 class="mb-0 fw-bold text-primary">Halo, {{ $nasabah->nama }}!</h5>
                    <p class="text-muted small mb-0">Selamat datang kembali</p>
                </div>
                <div class="text-end">
                    <div class="small text-muted">Saldo Anda</div>
                    <div class="fw-bold fs-5 text-primary">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Jadwal Bank Sampah (Placeholder dalam kartu terpisah) --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0">
            <h6 class="mb-0">Jadwal Bank Sampah Berikutnya</h6>
        </div>
        <div class="card-body">
            <p class="fs-5 fw-bold mb-0">Sabtu, 2 November 2025</p>
            <small class="text-muted">Pukul 08:00 - 11:00 WIB</small>
        </div>
    </div>
    
    {{-- Riwayat Transaksi Terakhir (dalam kartu terpisah) --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0">
            <h6 class="mb-0">Riwayat Transaksi Terakhir</h6>
        </div>
        <div class="list-group list-group-flush">
            @forelse ($riwayatTransaksi as $transaksi)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        @if ($transaksi->jenis_transaksi == 'setor')
                            <i class="bi bi-arrow-down-circle-fill text-success me-2"></i> Setor Sampah
                        @else
                            <i class="bi bi-arrow-up-circle-fill text-danger me-2"></i> Tarik Saldo
                        @endif
                        <br>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d F Y') }}</small>
                    </div>
                    <div class="fw-bold text-end">
                        @if ($transaksi->jenis_transaksi == 'setor')
                            <span class="text-success">+ Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                        @else
                            <span class="text-danger">- Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="list-group-item text-center text-muted">
                    Belum ada riwayat transaksi.
                </div>
            @endforelse
        </div>
    </div>
@endsection