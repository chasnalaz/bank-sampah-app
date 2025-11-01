<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bank Sampah Berseri Sejahtera</title>
    
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
            font-size: 1rem; /* Menetapkan ukuran font dasar agar konsisten */
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            border-radius: 1rem;
        }
        .form-control-lg {
        font-size: 1rem !important;
        }
        .btn-primary {
            --bs-btn-bg: rgb(var(--bs-primary-rgb));
            --bs-btn-border-color: rgb(var(--bs-primary-rgb));
            --bs-btn-hover-bg: rgb(227, 106, 0); /* Oranye sedikit lebih gelap */
            --bs-btn-hover-border-color: rgb(227, 106, 0);
            --bs-btn-active-bg: rgb(204, 95, 0); /* Oranye lebih gelap lagi */
            --bs-btn-active-border-color: rgb(204, 95, 0);
        }

        /* Mencegah font berubah ukuran saat diklik */
        .btn:focus, .btn:active {
            font-size: inherit;
        }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="card-body p-4 p-lg-5">
            <div class="text-center mb-4">
                {{-- 1. Logo diperbesar dari 60 menjadi 80 --}}
                <img src="{{ asset('img/logo.png') }}" alt="Logo" height="80">
                {{-- 2. Judul diubah dari h3 menjadi h4 agar tidak terpotong --}}
                <h4 class="mt-3 fw-bold">Selamat Datang Kembali!</h4>
                <p class="text-muted">Silakan login untuk melanjutkan.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" class="form-control form-control-lg" type="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" class="form-control form-control-lg" type="password" name="password" required>
                </div>
                <div class="d-grid mt-4">
                    {{-- 3. Tombol tetap menggunakan btn-primary yang sudah bertema oranye --}}
                    <button type="submit" class="btn btn-primary btn-lg">Login</button>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('register') }}">Belum punya akun? Daftar di sini</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>