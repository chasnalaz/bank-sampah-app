@extends('layouts.main')

@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid">

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="row">
        
        <div class="col-xl-4 col-md-5 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body text-center p-5">
                    
                    {{-- Avatar Inisial --}}
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" 
                         style="width: 100px; height: 100px; font-size: 2.5rem; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white;">
                        {{ substr($user->name, 0, 1) }}
                    </div>

                    <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>

                    {{-- Badge Role --}}
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill px-3 py-2 text-uppercase letter-spacing-1">
                        {{ $user->role }}
                    </span>

                    <hr class="my-4 opacity-10">

                    <div class="text-start">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Bergabung Sejak</small>
                        <p class="fw-bold text-dark mb-0">{{ $user->created_at->format('d F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-md-7 mb-4">
            
            {{-- FORM 1: UPDATE INFO --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="bi bi-person-gear me-2 text-primary"></i>Informasi Pribadi</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold text-uppercase">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold text-uppercase">Alamat Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- FORM 2: UPDATE PASSWORD --}}
            {{-- Note: Ini butuh Route update password khusus (biasanya 'password.update') --}}
            {{-- Kalau kamu pakai Laravel Breeze default, route-nya adalah 'password.update' --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="bi bi-shield-lock me-2 text-danger"></i>Ganti Password</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold text-uppercase">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control">
                            @error('current_password', 'updatePassword')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted fw-bold text-uppercase">Password Baru</label>
                                <input type="password" name="password" class="form-control">
                                @error('password', 'updatePassword')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted fw-bold text-uppercase">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="bi bi-key me-1"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection