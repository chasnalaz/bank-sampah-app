<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Nasabah - Bank Sampah Berseri Sejahtera</title>
    
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
        .login-card {
            width: 100%;
            max-width: 450px;
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            border-radius: 1rem;
        }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="card-body p-4 p-lg-5">
            <div class="text-center mb-4">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" height="80">
                <h4 class="mt-3 fw-bold">Login Nasabah</h4>
                <p class="text-muted">Masukkan nomor telepon dan password Anda.</p>
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
            
            <form method="POST" action="{{ route('nasabah.login.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="telepon" class="form-label">Nomor Telepon</label>
                    <input id="telepon" class="form-control form-control-lg" type="text" name="telepon" value="{{ old('telepon') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" class="form-control form-control-lg" type="password" name="password" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>