@extends('layouts.nasabah-mobile')

@section('title', 'Beranda')

@section('content')
    {{-- 1. Header Simple --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Halo, {{ Str::limit($nasabah->nama, 15) }}! 👋</h5>
            <small class="text-muted">Mari peduli lingkungan</small>
        </div>
        <img src="{{ asset('img/logo.png') }}" alt="Logo" height="40">
    </div>

    {{-- 2. Card Saldo (Gradient Design) --}}
    <div class="card border-0 shadow-sm mb-4 text-white" 
         style="background: linear-gradient(135deg, #fd7e14 0%, #e36a00 100%); border-radius: 15px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <small class="text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Saldo Tabungan</small>
                    <h2 class="fw-bold mb-0 mt-1">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-2">
                    <i class="bi bi-wallet2 fs-4"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-top border-white border-opacity-25 d-flex gap-3">
                <a href="{{ route('nasabah.penjemputan') }}" class="text-white text-decoration-none small">
                    <i class="bi bi-truck me-1"></i> Request Jemput
                </a>
                <a href="{{ route('nasabah.riwayat') }}" class="text-white text-decoration-none small">
                    <i class="bi bi-clock-history me-1"></i> Cek Riwayat
                </a>
            </div>
        </div>
    </div>

    {{-- 3. Info Operasional (Dynamic - REVISI) --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex align-items-center">
            {{-- Bagian Tanggal (Kiri) --}}
            <div class="me-3 text-center bg-light rounded p-2 d-flex flex-column justify-content-center" style="min-width: 65px; height: 65px;">
                <span class="d-block fw-bold text-danger fs-4 lh-1">{{ $tglBuka ? \Carbon\Carbon::parse($tglBuka)->format('d') : '--' }}</span>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">
                    {{ $tglBuka ? \Carbon\Carbon::parse($tglBuka)->format('M') : '-' }}
                </small>
            </div>
            
            {{-- Bagian Info & Status (Kanan) --}}
            <div>
                <h6 class="fw-bold mb-1 text-dark">
                    Jadwal Operasional
                </h6>
                
                {{-- Jam Buka --}}
                @if($tglBuka)
                    <div class="text-muted small mb-1">
                        Buka pukul <span class="fw-bold text-dark">{{ $jamBuka }} - {{ $jamTutup }} WIB</span>
                    </div>
                @else
                    <p class="text-muted small mb-1">Belum ada jadwal.</p>
                @endif

                {{-- Badge Status (Pindah ke bawah biar rapi) --}}
                @if($sedangBuka)
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2">
                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> SEDANG BUKA
                    </span>
                @else
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2">
                        <i class="bi bi-lock-fill me-1" style="font-size: 0.6rem;"></i> TUTUP
                    </span>
                @endif
            </div>
        </div>
    </div>
    
    {{-- 4. Riwayat Terakhir (Widget) --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Transaksi Terakhir</h6>
        <a href="{{ route('nasabah.riwayat') }}" class="text-decoration-none small">Lihat Semua</a>
    </div>

    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
        <div class="list-group list-group-flush">
            @forelse ($riwayatTransaksi as $transaksi)
                <div class="list-group-item px-3 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            {{-- Icon Bulat --}}
                            <div class="rounded-circle p-2 me-3 {{ $transaksi->jenis_transaksi == 'setor' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
                                <i class="bi {{ $transaksi->jenis_transaksi == 'setor' ? 'bi-arrow-down' : 'bi-arrow-up' }}"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">
                                    {{ $transaksi->jenis_transaksi == 'setor' ? 'Setor Sampah' : 'Penarikan Saldo' }}
                                </h6>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d F Y') }}
                                </small>
                            </div>
                        </div>
                        <div class="fw-bold {{ $transaksi->jenis_transaksi == 'setor' ? 'text-success' : 'text-danger' }}">
                            {{ $transaksi->jenis_transaksi == 'setor' ? '+' : '-' }}Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <small>Belum ada transaksi.</small>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 5. Pojok Edukasi (Tetap sama, cuma dirapikan dikit) --}}
    <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb text-warning me-2"></i>Pojok Edukasi</h6>
    <div class="row g-3">
        @forelse($edukasiList as $item)
        <div class="col-12">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                @if($item->kategori == 'video' && $item->youtube_id)
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/{{ $item->youtube_id }}" allowfullscreen></iframe>
                    </div>
                @elseif($item->kategori == 'poster' && $item->gambar)
                    {{-- Container Background Abu-abu --}}
                    <div class="bg-light text-center" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ asset('storage/' . $item->gambar) }}" 
                            class="img-fluid" 
                            alt="Poster" 
                            style="max-height: 100%; max-width: 100%; object-fit: contain;"> 
                            {{-- contain = paksa gambar masuk semua ke dalam kotak --}}
                    </div>                 
                @endif
                <div class="card-body p-3">
                    <h6 class="card-title fw-bold mb-1">{{ $item->judul }}</h6>
                    <p class="card-text text-muted small mb-0">{{ Str::limit($item->deskripsi, 80) }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted small py-3">Belum ada konten edukasi.</div>
        @endforelse
    </div>
@endsection