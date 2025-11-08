{{-- File: resources/views/auth/register-nasabah.blade.php --}}
@extends('layouts.guest-bootstrap')

@section('title', 'Daftar Akun Nasabah')

@section('content')
    
    <div class="text-center mb-4">
        <a href="/">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" style="height: 80px;">
        </a>
        <h1 class="h4 fw-bold text-dark mt-3">
            Daftar Akun Nasabah
        </h1>
        <p class="text-muted small">Buat akun Anda untuk mulai menabung sampah.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('nasabah.register.store') }}">
        @csrf

        <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input id="nama" class="form-control" type="text" name="nama" value="{{ old('nama') }}" required autofocus>
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat Lengkap</label>
            <textarea id="alamat" name="alamat" rows="3" class="form-control" required>{{ old('alamat') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="telepon" class="form-label">Nomor Telepon (WhatsApp)</label>
            <input id="telepon" class="form-control" type="text" name="telepon" value="{{ old('telepon') }}" required>
        </div>

        {{-- === BLOK INPUT EMAIL DIHAPUS DARI SINI === --}}

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            {{-- Gunakan position-relative --}}
            <div class="position-relative">
                <input id="password" class="form-control" type="password" name="password" required style="padding-right: 3rem;">
                {{-- Posisikan ikon di dalam input --}}
                <span class="position-absolute top-50 end-0 translate-middle-y me-3 toggle-password-icon" data-target="password">
                    <i class="bi bi-eye-fill"></i>
                </span>
            </div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <div class="position-relative">
                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required style="padding-right: 3rem;">
                {{-- Posisikan ikon di dalam input --}}
                <span class="position-absolute top-50 end-0 translate-middle-y me-3 toggle-password-icon" data-target="password_confirmation">
                    <i class="bi bi-eye-fill"></i>
                </span>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-4">
            <a class="small text-decoration-none" href="{{ route('nasabah.login') }}">
                Sudah punya akun? Login
            </a>

            <button type="submit" class="btn btn-orange fw-bold px-4">
                Daftar
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    {{-- TAMBAHKAN SCRIPT INI (Sama seperti di login) --}}
    <script>
        document.querySelectorAll('.toggle-password-icon').forEach(icon => {
            icon.addEventListener('click', function (e) {
                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);
                const iconElement = this.querySelector('i');

                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    iconElement.classList.remove('bi-eye-fill');
                    iconElement.classList.add('bi-eye-slash-fill');
                } else {
                    targetInput.type = 'password';
                    iconElement.classList.remove('bi-eye-slash-fill');
                    iconElement.classList.add('bi-eye-fill');
                }
            });
        });
    </script>
@endpush