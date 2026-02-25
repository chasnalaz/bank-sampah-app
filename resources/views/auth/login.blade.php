<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas - Bank Sampah</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5; /* Background abu-abu soft */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .btn-back {
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            color: #6c757d;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-back:hover {
            color: #fd7e14; /* Warna oranye primary */
            transform: translateX(-5px);
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            background: white;
            padding: 2.5rem;
        }
        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .form-control:focus {
            background-color: #fff;
            border-color: #fd7e14;
            box-shadow: 0 0 0 0.25rem rgba(253, 126, 20, 0.25);
        }
        .btn-primary {
            background-color: #fd7e14;
            border-color: #fd7e14;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #e36a00;
            border-color: #e36a00;
        }
    </style>
</head>
<body>
    {{-- TOMBOL KEMBALI KE BERANDA --}}
    <a href="/" class="btn-back">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
    </a>

    <div class="login-card">
        <div class="text-center mb-4">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" height="70" class="mb-3">
            <h4 class="fw-bold text-dark">Portal Petugas</h4>
            <p class="text-muted small">Masuk untuk mengelola sistem bank sampah.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 small rounded-3">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Email Petugas</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="admin@bsberseri.id" required autofocus>
            </div>
            
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">Password</label>
                <div class="position-relative">
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                    <span class="position-absolute top-50 end-0 translate-middle-y me-3 toggle-password" style="cursor: pointer;">
                        <i class="bi bi-eye-fill text-muted"></i>
                    </span>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Masuk Sistem</button>
            </div>
            
            {{-- LINK REGISTER DIHAPUS DEMI KEAMANAN --}}
        </form>
    </div>

    <script>
        document.querySelector('.toggle-password').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        });
    </script>
</body>
</html>