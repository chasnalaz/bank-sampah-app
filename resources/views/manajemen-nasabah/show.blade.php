@extends('layouts.main')

@section('title', 'Detail Nasabah')

@section('content')
<div class="container">
    {{-- Tombol Kembali --}}
    <a href="{{ route('nasabah.manajemen') }}" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a>

    <div class="row">
        {{-- KARTU 1: BIODATA NASABAH --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow border-left-primary h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profil Nasabah</h5>
                </div>
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        {{-- Avatar Inisial --}}
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-primary fw-bold border" 
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ substr($nasabah->nama, 0, 1) }}
                        </div>
                    </div>
                    <h4 class="fw-bold text-gray-800">{{ $nasabah->nama }}</h4>
                    <p class="text-muted small mb-4">Bergabung sejak: {{ $nasabah->created_at->format('d M Y') }}</p>
                    
                    <hr>
                    
                    <div class="text-start px-3">
                        <div class="mb-3">
                            <small class="text-uppercase text-muted fw-bold">Saldo Saat Ini</small>
                            <h3 class="text-success fw-bold">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</h3>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted"><i class="bi bi-telephone me-1"></i> Telepon:</small><br>
                            <span class="fw-bold">{{ $nasabah->telepon ?? '-' }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i> Alamat:</small><br>
                            <span>{{ $nasabah->alamat ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KARTU 2: RIWAYAT TRANSAKSI --}}
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Transaksi</h6>
                    <span class="badge bg-secondary">{{ $riwayatTransaksi->count() }} Transaksi</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Detail</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatTransaksi as $trx)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') }}</td>
                                        <td>
                                            @if($trx->jenis_transaksi == 'setor')
                                                <span class="badge bg-success">Setor Sampah</span>
                                            @else
                                                <span class="badge bg-danger">Tarik Saldo</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($trx->jenis_transaksi == 'setor')
                                                {{ $trx->jenis_sampah }} ({{ $trx->berat }} kg)
                                            @else
                                                Penarikan Tunai
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold {{ $trx->jenis_transaksi == 'setor' ? 'text-success' : 'text-danger' }}">
                                            {{ $trx->jenis_transaksi == 'setor' ? '+' : '-' }} 
                                            Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            Belum ada riwayat transaksi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection