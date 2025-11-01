<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Bank Sampah Berseri Sejahtera</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root { 
            --bs-primary-rgb: 253, 126, 20; 
            --bs-font-sans-serif: 'Poppins', sans-serif; 
        }
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .register-card {
            width: 100%;
            max-width: 450px;
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            border-radius: 1rem;
        }
        .btn-primary {
            --bs-btn-bg: rgb(var(--bs-primary-rgb));
            --bs-btn-border-color: rgb(var(--bs-primary-rgb));
            --bs-btn-hover-bg: rgb(227, 106, 0);
            --bs-btn-hover-border-color: rgb(227, 106, 0);
        }
    </style>
</head>
<body>
    <div class="card register-card">
        <div class="card-body p-4 p-lg-5">
            <div class="text-center mb-4">
                {{-- 1. Ukuran logo disamakan dengan halaman login --}}
                <img src="{{ asset('img/logo.png') }}" alt="Logo" height="80">
                <h4 class="mt-3 fw-bold">Buat Akun Baru</h4>
                {{-- 2. Teks diubah --}}
                <p class="text-muted">Silahkan daftarkan diri Anda.</p>
            </div>
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                {{-- 3. Semua input menggunakan 'form-control-lg' agar sama dengan login --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input id="name" class="form-control form-control-lg" type="text" name="name" value="{{ old('name') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" class="form-control form-control-lg" type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" class="form-control form-control-lg" type="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input id="password_confirmation" class="form-control form-control-lg" type="password" name="password_confirmation" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Daftar</button>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('login') }}">Sudah punya akun? Login di sini</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>