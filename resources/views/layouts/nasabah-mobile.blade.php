<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bank Sampah')</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root { 
            --bs-primary-rgb: 253, 126, 20; 
            --bs-font-sans-serif: 'Poppins', sans-serif; 
        }
        body {
            background-color: #ffffff; /* Latar belakang abu-abu */
        }
        /* CSS untuk wadah utama yang menyerupai HP */
        .mobile-container {
            max-width: 450px; /* Lebar maksimal seperti layar HP */
            min-height: 100vh; /* Tinggi penuh layar */
            margin: 0 auto; /* Posisi di tengah layar desktop */
            background-color: #ffffff; /* Latar belakang konten putih */
            box-shadow: none;
            padding-bottom: 80px; /* Memberi ruang untuk navbar bawah */
        }
        /* CSS untuk Navbar Bawah */
        .bottom-nav {
            max-width: 450px;
            margin: 0 auto;
            border-top: 1px solid #dee2e6;
        }
        .bottom-nav .nav-link {
            color: #6c757d; /* Warna ikon tidak aktif */
            padding: 0.75rem 0;
        }
        .bottom-nav .nav-link.active {
            color: rgb(var(--bs-primary-rgb)); /* Warna oranye untuk ikon aktif */
        }
        .btn-primary {
            --bs-btn-bg: rgb(var(--bs-primary-rgb));
            --bs-btn-border-color: rgb(var(--bs-primary-rgb));
            --bs-btn-hover-bg: rgb(227, 106, 0);
            --bs-btn-hover-border-color: rgb(227, 106, 0);
            --bs-btn-active-bg: rgb(204, 95, 0);
            --bs-btn-active-border-color: rgb(204, 95, 0);
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <main class="p-3">
            @yield('content')
        </main>
    </div>

    {{-- Navbar Bawah --}}
    <nav class="navbar navbar-light bg-white fixed-bottom bottom-nav">
        <div class="container-fluid d-flex justify-content-around">
            <a href="{{ route('nasabah.dashboard') }}" class="nav-link text-center {{ request()->routeIs('nasabah.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door-fill fs-4"></i>
                <div class="small">Beranda</div>
            </a>
            <a href="#" class="nav-link text-center">
                <i class="bi bi-clock-history fs-4"></i>
                <div class="small">Riwayat</div>
            </a>
           <a href="{{ route('nasabah.penjemputan') }}" class="nav-link text-center {{ request()->routeIs('nasabah.penjemputan') ? 'active' : '' }}">
                <i class="bi bi-truck fs-4"></i>
                <div class="small">Penjemputan</div>
            </a>
            <a href="#" class="nav-link text-center">
                <i class="bi bi-person-fill fs-4"></i>
                <div class="small">Akun</div>
            </a>
        </div>
    </nav>
    @yield('modal') {{-- <-- TAMBAHKAN BARIS INI --}}

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        @stack('scripts') {{-- Sekalian tambahkan ini untuk JavaScript kustom nanti --}}
</body>
</html>