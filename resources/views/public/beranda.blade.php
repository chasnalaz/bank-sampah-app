@extends('layouts.public')

@section('title', 'Beranda - Bank Sampah Berseri Sejahtera')

@push('styles')
    <style>
        /* CSS untuk Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1611284446314-60a58ac0deb9?q=80&w=1950');
            background-size: cover;
            background-position: center;
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 4rem 1rem;
        }
        /* Jargon Utama (Judul) */
        .hero-section h1 {
            font-weight: 700;
            font-size: 3rem; 
        }
        /* Jargon Kedua (Tagline) */
        .hero-section p.lead {
            font-size: 1.5rem; /* Sedikit lebih besar */
            font-weight: 500;
            opacity: 0.9;
        }
    </style>
@endpush

@section('content')
    
    {{-- =================================== --}}
    {{-- 1. HERO SECTION (DIPERBARUI DENGAN JARGON) --}}
    {{-- =================================== --}}
    <div class="hero-section content-section" id="beranda">
        <div class="container">
            {{-- JARGON 1 (UTAMA) --}}
            <h1 class="display-4">"Sampah Rumah Jadi Rupiah, Ubah Sampah Jadi Berkah"</h1>
            {{-- JARGON 2 (TAGLINE) --}}
            <p class="lead mt-4">Bersih • Sehat • Asri • Sejahtera</p>
        </div>
    </div>

    {{-- =================================== --}}
    {{-- 2. BAGIAN TENTANG KAMI --}}
    {{-- =================================== --}}
    <div class="container content-section" id="tentang">
        <div class="card shadow-lg border-0">
            <div class="card-body p-4 p-md-5">
                <div class="row g-5 align-items-center">
                    {{-- Kolom Kiri (Deskripsi) --}}
                    <div class="col-lg-7">
                        <h2 class="display-5 fw-bold mb-4">Tentang Kami</h2>
                        <p class="lead" style="text-align: justify;">
                            Bank Sampah Berseri Sejahtera adalah lembaga yang berlokasi di Desa Kedawung, Kroya, Cilacap. 
                            Kami berfokus pada pengelolaan sampah anorganik dengan sistem 3R (Reduce, Reuse, Recycle). 
                            Kami hadir untuk memberikan solusi pengelolaan sampah sekaligus memberikan nilai ekonomi bagi masyarakat.
                        </p>
                        <p style="text-align: justify;">
                            Visi kami adalah menciptakan lingkungan Desa Kedawung yang bersih, sehat, dan mandiri melalui pengelolaan sampah yang bijak. Misi kami adalah mengedukasi masyarakat tentang pentingnya pemilahan sampah, memfasilitasi penabungan sampah, dan mengubah sampah menjadi produk yang bernilai guna.
                        </p>
                    </div>
                    {{-- Kolom Kanan (Gambar) --}}
                    <div class="col-lg-5">
                        <img src="https://cdn.rri.co.id/berita/21/images/1676971290265-Bank_Sampah/1676971290265-Bank_Sampah.jpg" class="img-fluid rounded-3 shadow" alt="Pemilahan sampah">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =================================== --}}
    {{-- 3. BAGIAN CARA KERJA --}}
    {{-- =================================== --}}
    <div class="bg-light content-section" id="cara-kerja">
        <div class="container">
            <div class="row text-center">
                <h2 class="display-5 fw-bold mb-5">Bagaimana Cara Kerjanya?</h2>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="card shadow-sm border-0 h-100 p-3">
                        <i class="bi bi-person-plus-fill fs-1 text-primary"></i>
                        <h4 class="mt-3">1. Daftar</h4>
                        <p>Daftarkan diri Anda sebagai nasabah baru untuk mulai menabung sampah.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="card shadow-sm border-0 h-100 p-3">
                        <i class="bi bi-box-seam-fill fs-1 text-primary"></i>
                        <h4 class="mt-3">2. Pilah & Setor</h4>
                        <p>Pilah sampah anorganik Anda dan setorkan ke bank sampah atau ajukan penjemputan.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="card shadow-sm border-0 h-100 p-3">
                        <i class="bi bi-cash-coin fs-1 text-primary"></i>
                        <h4 class="mt-3">3. Dapatkan Saldo</h4>
                        <p>Sampah Anda akan ditimbang dan dikonversi menjadi saldo di rekening Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =================================== --}}
    {{-- 4. BAGIAN LAYANAN --}}
    {{-- =================================== --}}
    <div class="container content-section" id="layanan">
        <div class="row text-center mb-5">
            <div class="col-md-12">
                <h2 class="display-5 fw-bold mb-4">Layanan Kami</h2>
                <p class="lead">Kami menyediakan berbagai layanan untuk memudahkan Anda mengelola sampah.</p>
            </div>
        </div>
        <div class="row justify-content-center g-4">
            <div class="col-md-5">
                <div class="card shadow-sm border-0 h-100 p-4 text-center">
                    <i class="bi bi-house-door-fill fs-1 text-primary"></i>
                    <h4 class="mt-3">Setor Langsung</h4>
                    <p>Datang langsung ke lokasi kami di Desa Kedawung pada jam operasional untuk menimbang dan menabungkan sampah anorganik Anda.</p>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card shadow-sm border-0 h-100 p-4 text-center">
                    <i class="bi bi-truck fs-1 text-primary"></i>
                    <h4 class="mt-3">Layanan Jemput Sampah</h4>
                    <p>Bagi nasabah terdaftar, kami menyediakan layanan penjemputan sampah langsung ke rumah Anda. Cukup ajukan permintaan melalui dashboard nasabah.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- =================================== --}}
    {{-- 5. BAGIAN KONTAK & PETA (DIPERBARUI DENGAN LINK PETA BARU) --}}
    {{-- =================================== --}}
    <div class="bg-light content-section" id="kontak">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-md-12">
                    <h2 class="display-5 fw-bold mb-4">Kontak & Lokasi</h2>
                    <p class="lead">Kami siap membantu Anda. Kunjungi kami di lokasi atau hubungi kami.</p>
                </div>
            </div>
            <div class="row g-5 align-items-center">
                {{-- Kolom Info Alamat --}}
                <div class="col-md-6">
                    <h4>Bank Sampah Berseri Sejahtera</h4>
                    <p class="lead">
                        Jalan Jeruk Manis, RT 01 RW 06, Desa Kedawung, 
                        Kec. Kroya, Kab. Cilacap, Jawa Tengah
                    </p>
                    <hr class="my-4">
                    <ul class="list-unstyled fs-5">
                        <li class="mb-3">
                            <i class="bi bi-telephone-fill text-primary me-2"></i> 
                            <a href="tel:+628123456789">+62 812-3456-789</a>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-whatsapp text-primary me-2"></i> 
                            <a href="https://wa.me/628123456789" target="_blank">+62 812-3456-789</a>
                        </li>
                        <li>
                            <i class="bi bi-envelope-fill text-primary me-2"></i> 
                            <a href="mailto:info@bsberseri.id">info@bsberseri.id</a>
                        </li>
                    </ul>
                </div>
                {{-- Kolom Peta --}}
                <div class="col-md-6">
                    <div class="map-container shadow">
                        {{-- 
                          INI ADALAH KODE EMBED YANG BENAR UNTUK "Kedawung, Kroya, Cilacap"
                          Link "googleusercontent.com/..." tidak akan berfungsi.
                        --}}
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3954.539634694449!2d109.244372!3d-7.6249639999999985!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zN8KwMzcnMjkuOSJTIDEwOcKwMTQnMzkuNyJF!5e0!3m2!1sid!2sid!4v1762582225911!5m2!1sid!2sid"
                            width="600"
                            height="450" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection