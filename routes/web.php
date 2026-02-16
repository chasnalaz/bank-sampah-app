<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\JenisSampahController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NasabahLoginController;
use App\Http\Controllers\NasabahDashboardController;
use App\Http\Controllers\PenjemputanController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\TengkulakController;
use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\EdukasiController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\NasabahProfileController;


Route::get('/', function () {
    return view('public.beranda'); 
})->name('public.beranda');

Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

     // NON-ADMIN ROUTES (ACCESSIBLE BY PETUGAS)
    Route::get('/nasabah', [NasabahController::class, 'index'])->name('nasabah.index');
    Route::post('/transaksi/setor', [TransaksiController::class, 'storeSetor'])->name('transaksi.storeSetor');
    
    Route::get('/tugas-penjemputan', [PenjemputanController::class, 'index'])->name('penjemputan.tugas');

    // Rute-rute aksi penjemputan
    Route::post('/penjemputan/{penjemputan}/terima', [PenjemputanController::class, 'terima'])->name('penjemputan.terima');
    Route::post('/penjemputan/{penjemputan}/selesaikan', [PenjemputanController::class, 'selesaikan'])->name('penjemputan.selesaikan');
    Route::post('/penjemputan/{penjemputan}/batalkan', [PenjemputanController::class, 'batalkan'])->name('penjemputan.batalkan');
    
    Route::post('/update-status-tugas', [PetugasController::class, 'updateStatus'])->name('petugas.status.update');

    // ====================================================
    // AREA MANAJEMEN (ADMIN & KETUA BISA MASUK)
    // ====================================================
    // Kita pakai 'can:isManajemen' agar Ketua bisa lihat-lihat data
    Route::middleware(['can:isManajemen'])->group(function () {
        
        // 1. MANAJEMEN NASABAH (READ)
        Route::get('/manajemen-nasabah', [NasabahController::class, 'showManajemen'])->name('nasabah.manajemen');
        Route::get('/manajemen-nasabah/{nasabah}', [NasabahController::class, 'show'])->name('nasabah.show'); // Detail

        // 2. MANAJEMEN SAMPAH (READ)
        Route::get('/manajemen-sampah', [JenisSampahController::class, 'index'])->name('sampah.manajemen');
        
        // 3. MANAJEMEN PETUGAS (READ)
        Route::get('/manajemen-petugas', [PetugasController::class, 'index'])->name('petugas.manajemen');
        
        // 4. MONITORING & ANALISIS (READ)
        Route::get('/admin/monitoring-penjemputan', [PenjemputanController::class, 'adminIndex'])->name('admin.penjemputan.index');
        Route::get('/manajemen-tengkulak', [TengkulakController::class, 'index'])->name('manajemen-tengkulak.index');
        Route::get('/penjualan-tengkulak', [PenjualanController::class, 'index'])->name('penjualan.index');
        
        Route::get('/analisis/rekomendasi', [AnalisisController::class, 'rekomendasi'])->name('analisis.rekomendasi');
        Route::get('/analisis/statistik', [AnalisisController::class, 'statistik'])->name('analisis.statistik');
        Route::get('/analisis', [AnalisisController::class, 'index'])->name('admin.analisis.index');

        // 5. LAPORAN & CETAK (READ)
        Route::get('/laporan/transaksi', [LaporanController::class, 'riwayatTransaksi'])->name('laporan.transaksi');
        Route::get('/laporan/transaksi/cetak', [LaporanController::class, 'cetakTransaksi'])->name('laporan.transaksi.cetak');
        Route::get('/transaksi/struk/{id}', [TransaksiController::class, 'cetakStruk'])->name('transaksi.struk');
        Route::get('/penjualan/struk/{id}', [PenjualanController::class, 'cetakStruk'])->name('penjualan.struk');


        // ====================================================
        // AREA "BERBAHAYA" (KHUSUS ADMIN SANG EKSEKUTOR)
        // ====================================================
        // Di sini kita kunci lagi pintunya. Cuma Admin yang boleh Edit/Hapus/Simpan.
        Route::middleware(['can:isAdmin'])->group(function () {
            
            // Pengaturan Toko
            Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');

            // Aksi Nasabah (CUD)
            Route::post('/manajemen-nasabah', [NasabahController::class, 'store'])->name('nasabah.store');
            Route::put('/manajemen-nasabah/{nasabah}', [NasabahController::class, 'update'])->name('nasabah.update');
            Route::delete('/manajemen-nasabah/{nasabah}', [NasabahController::class, 'destroy'])->name('nasabah.destroy');

            // Aksi Sampah (CUD)
            Route::post('/manajemen-sampah', [JenisSampahController::class, 'store'])->name('sampah.store');
            Route::put('/manajemen-sampah/{jenisSampah}', [JenisSampahController::class, 'update'])->name('sampah.update');
            Route::delete('/manajemen-sampah/{jenisSampah}', [JenisSampahController::class, 'destroy'])->name('sampah.destroy');
            
            // Aksi Petugas (CUD)
            Route::post('/manajemen-petugas', [PetugasController::class, 'store'])->name('petugas.store');
            Route::put('/manajemen-petugas/{user}', [PetugasController::class, 'update'])->name('petugas.update');
            Route::delete('/manajemen-petugas/{user}', [PetugasController::class, 'destroy'])->name('petugas.destroy');
            
            // Aksi Penjemputan (Assign/Delete)
            Route::post('/admin/penjemputan/{penjemputan}/assign', [PenjemputanController::class, 'adminAssign'])->name('admin.penjemputan.assign');
            Route::delete('/admin/penjemputan/{penjemputan}/destroy', [PenjemputanController::class, 'adminDestroy'])->name('admin.penjemputan.destroy');

            // Aksi Tengkulak & Penjualan (CUD)
            Route::post('/manajemen-tengkulak', [TengkulakController::class, 'store'])->name('manajemen-tengkulak.store');
            Route::put('/manajemen-tengkulak/{id}', [TengkulakController::class, 'update'])->name('manajemen-tengkulak.update');
            Route::delete('/manajemen-tengkulak/{id}', [TengkulakController::class, 'destroy'])->name('manajemen-tengkulak.destroy');
            
            Route::post('/penjualan-tengkulak', [PenjualanController::class, 'store'])->name('penjualan.store');
            Route::delete('/penjualan-tengkulak/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
        
            // Edukasi
            Route::get('/admin/edukasi', [EdukasiController::class, 'index'])->name('admin.edukasi.index');
            Route::post('/admin/edukasi', [EdukasiController::class, 'store'])->name('admin.edukasi.store');
            Route::delete('/admin/edukasi/{id}', [EdukasiController::class, 'destroy'])->name('admin.edukasi.destroy');
        });
    });

    });
        Route::get('/nasabah/register', [NasabahLoginController::class, 'showRegistrationForm'])->name('nasabah.register');
        Route::post('/nasabah/register', [NasabahLoginController::class, 'storeRegistration'])->name('nasabah.register.store');
        Route::get('/nasabah/login', [NasabahLoginController::class, 'showLoginForm'])->name('nasabah.login');
        Route::post('/nasabah/login', [NasabahLoginController::class, 'login'])->name('nasabah.login.store');
        Route::post('/nasabah/logout', [NasabahLoginController::class, 'logout'])->name('nasabah.logout');

        // RUTE UNTUK AREA NASABAH (SETELAH LOGIN)
        Route::middleware('auth:nasabah')->group(function () {
            Route::get('/nasabah/dashboard', [NasabahDashboardController::class, 'index'])->name('nasabah.dashboard');
            Route::get('/nasabah/penjemputan', [NasabahDashboardController::class, 'showPenjemputan'])->name('nasabah.penjemputan');
            Route::post('/nasabah/penjemputan', [NasabahDashboardController::class, 'storePenjemputan'])->name('nasabah.penjemputan.store');

            Route::get('/nasabah/riwayat', [NasabahDashboardController::class, 'riwayat'])->name('nasabah.riwayat');
            
            // FITUR PROFIL
            Route::get('/nasabah/profil', [NasabahProfileController::class, 'index'])->name('nasabah.profil');
            Route::put('/nasabah/profil', [NasabahProfileController::class, 'update'])->name('nasabah.profil.update');
            });
require __DIR__.'/auth.php';