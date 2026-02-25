<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bank Sampah Berseri Sejahtera</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>
    
    {{-- LOGIKA PENENTU SIAPA YANG LOGIN --}}
    @php
        // Cek Login Admin/Petugas
        $authUser = Auth::user(); 
        
        // Cek Login Nasabah
        $authNasabah = Auth::guard('nasabah')->user();

        // Tentukan Nama & Role untuk Tampilan
        if ($authUser) {
            $userName = $authUser->name;
            $userRole = $authUser->role; // admin/petugas/ketua
        } elseif ($authNasabah) {
            $userName = $authNasabah->nama;
            $userRole = 'nasabah';
        } else {
            $userName = 'Tamu';
            $userRole = 'guest';
        }
    @endphp

    <div class="d-flex" id="wrapper">
        <aside class="bg-white" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" height="80" class="me-2">
            </div>
            <hr class="sidebar-divider my-2">
            
            <div class="list-group list-group-flush my-3">
                
                {{-- ================================================= --}}
                {{-- A. SIDEBAR KHUSUS NASABAH (Cek Guard: nasabah) --}}
                {{-- ================================================= --}}
                @if(Auth::guard('nasabah')->check())
                    <a href="{{ route('nasabah.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('nasabah.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill me-2"></i> Beranda
                    </a>
                    <a href="{{ route('nasabah.riwayat') }}" class="list-group-item list-group-item-action {{ request()->routeIs('nasabah.riwayat') ? 'active' : '' }}">
                        <i class="bi bi-clock-history me-2"></i> Riwayat Transaksi
                    </a>
                    <a href="{{ route('nasabah.penjemputan') }}" class="list-group-item list-group-item-action {{ request()->routeIs('nasabah.penjemputan') ? 'active' : '' }}">
                        <i class="bi bi-truck me-2"></i> Penjemputan Sampah
                    </a>
                    {{-- Tambahan: Profil Akun (Opsional, tapi penting) --}}
                    <a href="{{ route('nasabah.profil') }}" class="list-group-item list-group-item-action {{ request()->routeIs('nasabah.profil') ? 'active' : '' }}">
                        <i class="bi bi-person-circle me-2"></i> Profil Akun
                    </a>
                @endif


                {{-- ================================================= --}}
                {{-- B. SIDEBAR ADMIN & PETUGAS (Cek Guard: web) --}}
                {{-- ================================================= --}}
                @if(Auth::guard('web')->check())
                    
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>

                    {{-- Menu Catat Transaksi (Kecuali Ketua) --}}
                    @if(Auth::user()->role !== 'ketua')
                        <a href="{{ route('nasabah.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('nasabah.index') ? 'active' : '' }}">
                            <i class="bi bi-wallet2 me-2"></i> Catat Transaksi
                        </a>
                    @endif
                    
                    {{-- Menu Kelola Data (Admin & Ketua) --}}
                    @can('isManajemen')
                        <a href="#kelolaDataSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div><i class="bi bi-folder2-open me-2"></i> Kelola Data</div>
                            <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse {{ request()->routeIs(['nasabah.manajemen*', 'sampah.manajemen*', 'petugas.manajemen*', 'manajemen-tengkulak.index*']) ? 'show' : '' }}" id="kelolaDataSubmenu">
                            <div class="sidebar-submenu">
                                <a href="{{ route('nasabah.manajemen') }}" class="list-group-item list-group-item-action {{ request()->routeIs('nasabah.manajemen*') ? 'active' : '' }}">
                                    <i class="bi bi-people me-2"></i> Data Nasabah
                                </a>
                                <a href="{{ route('sampah.manajemen') }}" class="list-group-item list-group-item-action {{ request()->routeIs('sampah.manajemen*') ? 'active' : '' }}">
                                    <i class="bi bi-trash3 me-2"></i> Data Sampah
                                </a>
                                <a href="{{ route('petugas.manajemen') }}" class="list-group-item list-group-item-action {{ request()->routeIs('petugas.manajemen*') ? 'active' : '' }}">
                                    <i class="bi bi-person-badge me-2"></i> Data Petugas
                                </a>
                                <a href="{{ route('manajemen-tengkulak.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('manajemen-tengkulak.index*') ? 'active' : '' }}">
                                    <i class="bi bi-truck me-2"></i> Data Tengkulak
                                </a>
                            </div>
                        </div>
                    @endcan

                    {{-- Menu Monitoring & Tugas --}}
                    @if(Auth::user()->role == 'petugas')
                        <a href="{{ route('penjemputan.tugas') }}" class="list-group-item list-group-item-action {{ request()->routeIs('penjemputan.tugas') ? 'active' : '' }}">
                            <i class="bi bi-truck me-2"></i> Tugas Penjemputan
                        </a>
                    @endif

                    @can('isManajemen')
                        <a href="{{ route('admin.penjemputan.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.penjemputan.index') ? 'active' : '' }}">
                            <i class="bi bi-basket me-2"></i> Monitoring Penjemputan
                        </a>
                        
                        <a href="{{ route('penjualan.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('penjualan.index') ? 'active' : '' }}">
                            <i class="bi bi-cash-coin me-2"></i> Penjualan Sampah
                        </a>

                        @can('isAdmin')
                            <a href="{{ route('admin.edukasi.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.edukasi.index') ? 'active' : '' }}">
                                <i class="bi bi-image me-2"></i> Kelola Edukasi
                            </a>
                        @endcan

                        <a href="#AnalisisSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div><i class="bi bi-file-earmark-bar-graph me-2"></i> Analisis & Laporan</div>
                            <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse {{ request()->routeIs(['analisis.*', 'laporan.*']) ? 'show' : '' }}" id="AnalisisSubmenu">
                            <div class="sidebar-submenu">
                                {{-- 1. Rekomendasi --}}
                                <a href="{{ route('analisis.rekomendasi') }}" 
                                   class="list-group-item list-group-item-action {{ request()->routeIs('analisis.rekomendasi*') ? 'active' : '' }}">
                                    <i class="bi bi-lightbulb me-2"></i>Rek. Tengkulak
                                </a>

                                {{-- 2. Analisis Data --}}
                                <a href="{{ route('analisis.statistik') }}" 
                                   class="list-group-item list-group-item-action {{ request()->routeIs('analisis.statistik*') ? 'active' : '' }}">
                                    <i class="bi bi-pie-chart me-2"></i>Analisis Data
                                </a>

                                {{-- 3. Laporan Transaksi (YANG ERROR TADI) --}}
                                <a href="{{ route('laporan.transaksi') }}" 
                                   class="list-group-item list-group-item-action {{ request()->routeIs('laporan.transaksi*') ? 'active' : '' }}">
                                    <i class="bi bi-file-earmark-text me-2"></i>Laporan Transaksi
                                </a>
                            </div>
                        </div>
                    @endcan

                @endif
            </div>
        </aside>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4 mb-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="bi bi-list fs-4 me-3" id="menu-toggle" style="cursor: pointer;"></i>
                    <h2 class="fs-4 m-0">@yield('title', 'Dashboard')</h2>
                </div>
                
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-2"></i> 
                                {{-- LOGIKA NAMA USER --}}
                                @if(Auth::guard('nasabah')->check())
                                    {{ Auth::guard('nasabah')->user()->nama }}
                                @elseif(Auth::check())
                                    {{ Auth::user()->name }}
                                @else
                                    Guest
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                
                                {{-- LOGIKA MENU DROPDOWN --}}
                                @if(Auth::guard('nasabah')->check())
                                    {{-- MENU UNTUK NASABAH --}}
                                    <li>
                                        <a class="dropdown-item" href="{{ route('nasabah.profil') }}">
                                            <i class="bi bi-person me-2"></i> Profil Akun
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('nasabah.logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                                            </button>
                                        </form>
                                    </li>

                                @elseif(Auth::check())
                                    {{-- MENU UNTUK ADMIN/PETUGAS --}}
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="bi bi-gear me-2"></i> Pengaturan Akun
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                                            </button>
                                        </form>
                                    </li>
                                @endif

                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="container-fluid px-4 pb-5">
                @yield('content')
            </main>
        </div>
    </div>
    
    @yield('modal')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");
        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };
    </script>
    @stack('scripts')
</body>
</html>