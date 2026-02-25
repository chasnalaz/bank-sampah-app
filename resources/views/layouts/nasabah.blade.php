<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Nasabah') - Bank Sampah Berseri Sejahtera</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root { 
            --bs-primary-rgb: 253, 126, 20; 
            --bs-font-sans-serif: 'Poppins', sans-serif; 
        }
        body {
            background-color: #f8f9fa;
        }

        .pagination {
            justify-content: flex-end;
            gap: 5px;
        }
        .page-item .page-link {
            border: none;
            border-radius: 8px;
            color: #6c757d;
            font-weight: 600;
            padding: 8px 16px;
            background-color: transparent;
            transition: all 0.2s;
        }
        .page-item .page-link:hover {
            background-color: #fff3cd;
            color: #fd7e14;
        }
        .page-item.active .page-link {
            background-color: #fd7e14;
            color: #ffffff;
            box-shadow: 0 4px 6px rgba(253, 126, 20, 0.3);
        }
        .page-item.disabled .page-link {
            background-color: transparent;
            color: #dee2e6;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('nasabah.dashboard') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" height="40">
            </a>
            <form method="POST" action="{{ route('nasabah.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <main class="container py-5">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>