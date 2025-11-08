{{-- File: resources/views/layouts/guest-bootstrap.blade.php --}}
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Bank Sampah Berseri Sejahtera</title>
    
    {{-- Aset (Font, Ikon, CSS) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body.guest-page {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa; /* Latar belakang abu-abu muda */
        }
        .guest-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .guest-card {
            width: 100%;
            max-width: 450px; /* Lebar maksimum kartu form */
            border-radius: 0.75rem;
        }
        
        /* Tombol Orange Kustom Anda */
        .btn-orange {
            background-color: #fd7e14;
            border-color: #fd7e14;
            color: #ffffff;
        }
        .btn-orange:hover {
            background-color: #e67312;
            border-color: #e67312;
            color: #ffffff;
        }
        .toggle-password-icon {
            cursor: pointer;
        }
    </style>
</head>
<body class="guest-page">

    <div class="guest-container container py-5">
        <div class="card shadow-sm border-0 guest-card">
            <div class="card-body p-4 p-md-5">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>