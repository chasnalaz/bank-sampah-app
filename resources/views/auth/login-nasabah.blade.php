{{-- File: resources/views/auth/login-nasabah.blade.php --}}
@extends('layouts.guest-bootstrap')

@section('title', 'Login Nasabah')

@section('content')

    <div class="text-center mb-4">
        <a href="/">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" style="height: 80px;">
        </a>
        <h1 class="h4 fw-bold text-dark mt-3">
            Login Nasabah
        </h1>
        <p class="text-muted small">Selamat datang kembali!</p>
    </div>

    {{-- Tampilkan Error Validasi (jika ada) --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif
    
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('nasabah.login.store') }}">
        @csrf

        {{-- === INI DIA PERUBAHANNYA === --}}
        <div class="mb-3">
            <label for="telepon" class="form-label">Nomor Telepon</label>
            <input id="telepon" class="form-control" type="text" name="telepon" value="{{ old('telepon') }}" required autofocus>
        </div>
        {{-- === AKHIR PERUBAHAN === --}}

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="position-relative">
                <input id="password" class="form-control" type="password" name="password" required style="padding-right: 3rem;">
                {{-- Posisikan ikon di dalam input --}}
                <span class="position-absolute top-50 end-0 translate-middle-y me-3 toggle-password-icon" data-target="password">
                    <i class="bi bi-eye-fill"></i>
                </span>
            </div>
        </div>

        <div class="mb-3 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label for="remember_me" class="form-check-label small text-muted">Ingat saya</label>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-orange fw-bold btn-lg">
                Login
            </button>
        </div>
        
        <div class="text-center mt-4">
             <a class="small text-decoration-none" href="{{ route('nasabah.register') }}">
                Belum punya akun? Daftar di sini
            </a>
        </div>
    </form>
@endsection

@push('scripts')
    {{-- TAMBAHKAN SCRIPT INI --}}
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