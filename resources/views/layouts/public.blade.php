{{-- File: resources/views/layouts/public.blade.php --}}
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>@yield('title', 'Bank Sampah Berseri Sejahtera')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    {{-- CSS Kustom (Termasuk Tombol Orange) --}}
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; }
        .content-section { padding-top: 70px; }
        
        /* CSS untuk Peta */
        .map-container { position: relative; overflow: hidden; width: 100%; padding-top: 56.25%; border-radius: 0.5rem; border: 1px solid #ddd; }
        .map-container iframe { position: absolute; top: 0; left: 0; bottom: 0; right: 0; width: 100%; height: 100%; }
        
        /* === CSS TOMBOL ORANGE (DARI SEBELUMNYA) === */
        .btn-orange {
            background-color: #fd7e14;
            border-color: #fd7e14;
            color: #ffffff !important;
        }
        .btn-orange:hover,
        .btn-orange:focus,
        .btn-orange:active {
            background-color: #e67312;
            border-color: #e67312;
            color: #ffffff !important;
        }
        .btn-orange.dropdown-toggle {
            color: #ffffff !important;
        }
        
        /* === INI DIA PERBAIKAN ANDA === */
        /* Mengganti warna hover/active biru di dropdown */
        .dropdown-menu .dropdown-item:hover,
        .dropdown-menu .dropdown-item:focus,
        .dropdown-menu .dropdown-item:active {
            background-color: #fd7e14; /* Latar belakang orange */
            color: #ffffff;            /* Teks putih */
        }
        /* === AKHIR PERBAIKAN === */

    </style>
    @stack('styles')
</head>

<body data-bs-spy="scroll" data-bs-target="#navbarPublic" data-bs-offset="100">

    {{-- ======== NAVBAR PUBLIK ======== --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            
            <a class="navbar-brand d-flex align-items-center" href="{{ route('public.beranda') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" height="50" class="d-inline-block align-text-top me-3">
                <div class="lh-1">
                    <span style="font-size: 1.1rem; font-weight: 500;">Bank Sampah</span><br>
                    <span style="font-size: 1.4rem; font-weight: 700;">Berseri Sejahtera</span>
                </div>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublic" aria-controls="navbarPublic" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarPublic">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item"> <a class="nav-link" href="#beranda">Beranda</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="#tentang">Tentang Kami</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="#layanan">Layanan</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="#kontak">Kontak</a> </li>
                    
                    {{-- Tombol Dropdown (Sudah Benar) --}}
                    <li class="nav-item dropdown ms-lg-3 mt-3 mt-lg-0">
                        <a class="btn btn-orange dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk / Daftar
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('nasabah.login') }}"><i class="bi bi-person me-2"></i> Login Nasabah</a></li>
                            <li><a class="dropdown-item" href="{{ route('nasabah.register') }}"><i class="bi bi-person-plus me-2"></i> Daftar Nasabah</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('login') }}"><i class="bi bi-shield-lock me-2"></i> Login Staf (Admin/Petugas)</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-dark text-white p-4 mt-5">
        <div class="container text-center text-md-start">
            <div class="row gy-3">
                <div class="col-md-8">
                    <h5 class="fw-bold">Bank Sampah Berseri Sejahtera</h5>
                </div>
                <div class="col-md-4 text-center text-md-end align-self-center">
                    <p class="small text-white-50 mb-0">© {{ date('Y') }} Dibuat untuk Skripsi.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>