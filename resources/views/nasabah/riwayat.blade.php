@extends('layouts.nasabah-mobile')

@section('title', 'Riwayat Transaksi')

@section('content')
    {{-- HEADER HALAMAN (CONSISTENT STYLE) --}}
    <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom border-2">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Riwayat Transaksi</h4>
            <small class="text-muted" style="font-size: 0.8rem;">Transaksi Setor dan Tarik Anda</small>
        </div>
        
        {{-- Ikon Pemanis (Biar seimbang, karena tidak ada tombol tambah) --}}
        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
             style="width: 45px; height: 45px;">
            <i class="bi bi-wallet2 text-primary fs-5"></i>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="list-group list-group-flush">
            @forelse ($semuaTransaksi as $trx)
                <div class="list-group-item p-3 border-bottom-0 mb-1">
                    <div class="d-flex justify-content-between mb-1">
                        {{-- Tanggal --}}
                        <small class="text-muted">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d F Y') }}
                        </small>
                        
                        {{-- Badge Status --}}
                        @if($trx->jenis_transaksi == 'setor')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Setor</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Tarik</span>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">
                                @if($trx->jenis_transaksi == 'setor')
                                    {{ $trx->jenis_sampah }} <span class="text-muted fw-normal">({{ $trx->berat }} kg)</span>
                                @else
                                    Penarikan Tunai
                                @endif
                            </h6>
                            @if($trx->petugas_id)
                                <small class="text-muted" style="font-size: 0.7rem;">Petugas: {{ $trx->petugas->name ?? '-' }}</small>
                            @endif
                        </div>
                        <div class="fs-6 fw-bold {{ $trx->jenis_transaksi == 'setor' ? 'text-success' : 'text-danger' }}">
                            {{ $trx->jenis_transaksi == 'setor' ? '+' : '-' }} Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                {{-- Divider tipis antar item --}}
                <hr class="my-0 border-light">
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-receipt-cutoff fs-1 text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">Belum ada riwayat transaksi.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination (Next/Prev) --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $semuaTransaksi->links('pagination::bootstrap-4') }} 
        {{-- Kalau pagination error tampilan, bisa pakai simple-bootstrap-4 atau abaikan dulu --}}
    </div>
@endsection