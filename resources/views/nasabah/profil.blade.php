@extends('layouts.main')

@section('title', 'Profil Akun')

@section('content')
    <div class="row g-4">
        
        {{-- KOLOM KIRI: KARTU IDENTITAS --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body p-4">
                    {{-- Avatar --}}
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" 
                         style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: bold;">
                        {{ substr($nasabah->nama, 0, 1) }}
                    </div>
                    
                    <h5 class="fw-bold mb-1">{{ $nasabah->nama }}</h5>
                    <p class="text-muted mb-4">{{ $nasabah->telepon }}</p>

                    <div class="d-grid">
                        <form action="{{ route('nasabah.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 fw-bold" onclick="return confirm('Yakin ingin keluar?')">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: FORM EDIT --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Biodata</h6>
                </div>
                <div class="card-body p-4">
                    
                    {{-- Alert Sukses --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('nasabah.profil.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama', $nasabah->nama) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Nomor Telepon</label>
                                <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $nasabah->telepon) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small text-muted fw-bold">Alamat Lengkap</label>
                                <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat', $nasabah->alamat) }}</textarea>
                            </div>
                        </div>

                        <hr class="my-4 border-light">

                        <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-key me-2 text-warning"></i>Ganti Password</h6>
                        <div class="alert alert-light border small text-muted mb-3 p-2">
                            <i class="bi bi-info-circle me-1"></i> Kosongkan jika tidak ingin mengubah password.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Password Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Ulangi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi password">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection