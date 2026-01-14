@extends('layouts.nasabah-mobile')

@section('title', 'Akun Saya')

@section('content')
    
    {{-- 1. Header Profil --}}
    <div class="text-center mb-4 mt-2">
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" 
             style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">
            {{ substr($nasabah->nama, 0, 1) }}
        </div>
        <h5 class="fw-bold mb-0">{{ $nasabah->nama }}</h5>
        <p class="text-muted small">{{ $nasabah->telepon }}</p>
    </div>

    {{-- Alert Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 2. Form Edit Profil --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Biodata</h6>
            
            <form action="{{ route('nasabah.profil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label small text-muted fw-bold">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $nasabah->nama) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted fw-bold">Nomor Telepon (Login)</label>
                    <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $nasabah->telepon) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted fw-bold">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat', $nasabah->alamat) }}</textarea>
                </div>

                <hr class="my-4 border-light">

                <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock me-2 text-warning"></i>Ganti Password</h6>
                <div class="alert alert-light border small text-muted mb-3">
                    <i class="bi bi-info-circle me-1"></i> Kosongkan jika tidak ingin mengganti password.
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted fw-bold">Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password" id="passBaru" class="form-control" placeholder="Min. 6 karakter">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passBaru')"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small text-muted fw-bold">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="passKonfirm" class="form-control" placeholder="Ulangi password">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passKonfirm')"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. Tombol Logout --}}
    <div class="d-grid mb-4">
        <form action="{{ route('nasabah.logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100 py-2 border-0 bg-white shadow-sm text-danger fw-bold" onclick="return confirm('Yakin ingin keluar?')">
                <i class="bi bi-box-arrow-right me-2"></i> Keluar Aplikasi
            </button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function togglePass(id) {
        var x = document.getElementById(id);
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>
@endpush