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


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Halaman ini untuk mendaftarkan semua URL aplikasi Anda.
*/

// Halaman utama (welcome/login) - Biarkan di luar
Route::get('/', function () {
    return redirect()->route('login');
});

// SEMUA RUTE YANG PERLU LOGIN MASUK KE DALAM GRUP INI
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Alihkan halaman dashboard default ke halaman utama aplikasi Anda
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute profil bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // RUTE-RUTE APLIKASI ANDA YANG SUDAH DIBUAT
    // ===================================================
    
    // NON-ADMIN ROUTES (ACCESSIBLE BY PETUGAS)
    Route::get('/nasabah', [NasabahController::class, 'index'])->name('nasabah.index');
    Route::post('/transaksi/setor', [TransaksiController::class, 'storeSetor'])->name('transaksi.storeSetor');
    Route::post('/transaksi/tarik', [TransaksiController::class, 'storeTarik'])->name('transaksi.storeTarik');

    // Rute untuk menampilkan halaman "Tugas Penjemputan" (dengan 3 Tab)
    Route::get('/tugas-penjemputan', [PenjemputanController::class, 'index'])->name('penjemputan.tugas');

    // Rute-rute aksi penjemputan
    Route::post('/penjemputan/{penjemputan}/terima', [PenjemputanController::class, 'terima'])->name('penjemputan.terima');
    Route::post('/penjemputan/{penjemputan}/selesaikan', [PenjemputanController::class, 'selesaikan'])->name('penjemputan.selesaikan');
    Route::post('/penjemputan/{penjemputan}/batalkan', [PenjemputanController::class, 'batalkan'])->name('penjemputan.batalkan');
    
    // ADMIN-ONLY ROUTES
    Route::middleware('can:isAdmin')->group(function () {
        Route::get('/manajemen-nasabah', [NasabahController::class, 'showManajemen'])->name('nasabah.manajemen');
        Route::post('/manajemen-nasabah', [NasabahController::class, 'store'])->name('nasabah.store');
        Route::put('/manajemen-nasabah/{nasabah}', [NasabahController::class, 'update'])->name('nasabah.update');
        Route::delete('/manajemen-nasabah/{nasabah}', [NasabahController::class, 'destroy'])->name('nasabah.destroy');

        // RUTE MANAJEMEN SAMPAH
        Route::get('/manajemen-sampah', [JenisSampahController::class, 'index'])->name('sampah.manajemen');
        Route::post('/manajemen-sampah', [JenisSampahController::class, 'store'])->name('sampah.store');
        Route::put('/manajemen-sampah/{jenisSampah}', [JenisSampahController::class, 'update'])->name('sampah.update');
        Route::delete('/manajemen-sampah/{jenisSampah}', [JenisSampahController::class, 'destroy'])->name('sampah.destroy');
        
        // RUTE MANAJEMEN PETUGAS
        Route::get('/manajemen-petugas', [PetugasController::class, 'index'])->name('petugas.manajemen');
        Route::post('/manajemen-petugas', [PetugasController::class, 'store'])->name('petugas.store');
        Route::put('/manajemen-petugas/{user}', [PetugasController::class, 'update'])->name('petugas.update');
        Route::delete('/manajemen-petugas/{user}', [PetugasController::class, 'destroy'])->name('petugas.destroy');
        
        // RUTE MONITORING PENJEMPUTAN ADMIN
        Route::get('/admin/monitoring-penjemputan', [PenjemputanController::class, 'adminIndex'])
            ->name('admin.penjemputan.index');

        Route::post('/admin/penjemputan/{penjemputan}/assign', [PenjemputanController::class, 'adminAssign'])
            ->name('admin.penjemputan.assign');
            
        Route::delete('/admin/penjemputan/{penjemputan}/destroy', [PenjemputanController::class, 'adminDestroy'])
            ->name('admin.penjemputan.destroy');
        });
    });

        Route::get('/nasabah/login', [NasabahLoginController::class, 'showLoginForm'])->name('nasabah.login');
        Route::post('/nasabah/login', [NasabahLoginController::class, 'login'])->name('nasabah.login.store');
        Route::post('/nasabah/logout', [NasabahLoginController::class, 'logout'])->name('nasabah.logout');

        // RUTE UNTUK AREA NASABAH (SETELAH LOGIN)
        Route::middleware('auth:nasabah')->group(function () {
            Route::get('/nasabah/dashboard', [NasabahDashboardController::class, 'index'])->name('nasabah.dashboard');
            Route::get('/nasabah/penjemputan', [NasabahDashboardController::class, 'showPenjemputan'])->name('nasabah.penjemputan');
            Route::post('/nasabah/penjemputan', [NasabahDashboardController::class, 'storePenjemputan'])->name('nasabah.penjemputan.store');
        });
        // File rute autentikasi - Biarkan di luar
require __DIR__.'/auth.php';