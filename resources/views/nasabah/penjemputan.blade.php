@extends('layouts.nasabah-mobile')

@section('title', 'Permintaan Penjemputan')

@section('content')
    {{-- HEADER HALAMAN (DENGAN GARIS PEMISAH) --}}
    {{-- pb-3 mb-4 border-bottom: Memberi jarak & garis bawah agar terpisah dari konten --}}
    <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom border-2">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Penjemputan</h4>
            <small class="text-muted" style="font-size: 0.8rem;">Request Penjemputan Sampah</small>
        </div>
        
        {{-- TOMBOL SINGKAT & JELAS --}}
        <button type="button" class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm" 
                data-bs-toggle="modal" data-bs-target="#formPenjemputanModal"
                style="font-weight: 600;">
            <i class="bi bi-plus-lg me-1"></i> Tambah
        </button>
    </div>

    {{-- Alert Sukses (Tetap Sama) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm fs-6" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    {{-- JUDUL SEKSI (DIPERJELAS) --}}
    <div class="d-flex align-items-center mb-3">
        <i class="bi bi-clock-history text-primary me-2"></i>
        <h6 class="fw-bold mb-0 text-secondary">Riwayat Permintaan</h6>
    </div>

    {{-- LIST ITEM (LANJUT KE BAWAH TETAP SAMA...) --}}
    <div class="list-group">
        {{-- ... kodingan list kamu yang lama ... --}}

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="list-group">
        @forelse ($riwayatPenjemputan as $permintaan)
            <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">{{ \Carbon\Carbon::parse($permintaan->usulan_tanggal)->translatedFormat('d F Y') }}</h6>
                    
                    {{-- === BLOK YANG DIPERBARUI === --}}
                    @if ($permintaan->status == 'Selesai')
                        <span class="badge bg-success-subtle text-success-emphasis">Selesai</span>
                    @elseif ($permintaan->status == 'Diterima')
                        <span class="badge bg-info-subtle text-info-emphasis">Sedang Diproses</span>
                    @else
                        {{-- Default untuk 'Menunggu Konfirmasi' --}}
                        <span class="badge bg-warning-subtle text-warning-emphasis">Menunggu Konfirmasi</span>
                    @endif
                    {{-- === AKHIR BLOK === --}}

                </div>
                <p class="mb-1 small text-muted">{{ $permintaan->alamat_penjemputan }}</p>
            </div>
        @empty
            <div class="list-group-item text-center text-muted">
                Belum ada riwayat permintaan.
            </div>
        @endforelse
    </div>
@endsection


@section('modal')
<div class="modal fade" id="formPenjemputanModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Formulir Permintaan Penjemputan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('nasabah.penjemputan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="usulan_tanggal" class="form-label">Usulan Tanggal</label>
                        <input type="date" class="form-control" name="usulan_tanggal" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat_penjemputan" class="form-label">Alamat Penjemputan</label>
                        <textarea class="form-control" name="alamat_penjemputan" rows="3" required>{{ $nasabah->alamat }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_sampah_id" class="form-label">Jenis Sampah Utama</label>
                        <select class="form-select" name="jenis_sampah_id" required>
                            <option value="" disabled selected>Pilih jenis sampah...</option>
                            @foreach ($jenisSampahList as $sampah)
                                <option value="{{ $sampah->id }}">{{ $sampah->nama_sampah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="estimasi_berat" class="form-label">Estimasi Berat (Contoh: 1 karung, 2 Kg)</label>
                        <input type="text" class="form-control" name="estimasi_berat">
                    </div>
                    <div class="mb-3">
                        <label for="catatan_nasabah" class="form-label">Catatan Tambahan (Opsional)</label>
                        <input type="text" class="form-control" name="catatan_nasabah" placeholder="Contoh: Ada botol kaca juga">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Permintaan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection