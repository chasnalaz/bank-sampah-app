<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bank Sampah Berseri Sejahtera</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <aside class="bg-white" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" height="80" class="me-2">
            </div>
            <hr class="sidebar-divider my-2">
            <div class="list-group list-group-flush my-3">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a href="{{ route('nasabah.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('nasabah.index') ? 'active' : '' }}">
                    <i class="bi bi-wallet2 me-2"></i> Catat Transaksi
                </a>
                
                @can('isAdmin')
                    <a href="#kelolaDataSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div><i class="bi bi-folder2-open me-2"></i> Kelola Data</div>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs(['nasabah.manajemen*', 'sampah.manajemen*', 'petugas.manajemen*']) ? 'show' : '' }}" id="kelolaDataSubmenu">
                        <div class="sidebar-submenu">
                            <a href="{{ route('nasabah.manajemen') }}" class="list-group-item list-group-item-action {{ request()->routeIs('nasabah.manajemen*') ? 'active' : '' }}">
                                <i class="bi bi-people me-2"></i> Data Nasabah
                            </a>
                            <a href="{{ route('sampah.manajemen') }}" class="list-group-item list-group-item-action {{ request()->routeIs('sampah.manajemen*') ? 'active' : '' }}">
                                <i class="bi bi-box-seam me-2"></i> Manajemen Sampah
                            </a>
                            <a href="{{ route('petugas.manajemen') }}" class="list-group-item list-group-item-action {{ request()->routeIs('petugas.manajemen*') ? 'active' : '' }}">
                                <i class="bi bi-person-badge me-2"></i> Data Petugas
                            </a>
                        </div>
                    </div>
                @endcan

                <a href="{{ route('penjemputan.tugas') }}" class="list-group-item list-group-item-action {{ request()->routeIs('penjemputan.tugas') ? 'active' : '' }}">
                    <i class="bi bi-truck me-2"></i> Tugas Penjemputan
                </a>
            </div>
        </aside>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4 mb-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="bi bi-list fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-4 m-0">@yield('title', 'Dashboard')</h2>
                </div>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-2"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a class="dropdown-item" href="{{ route('logout') }}" 
                                           onclick="event.preventDefault(); this.closest('form').submit();">
                                            Logout
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="container-fluid px-4">
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