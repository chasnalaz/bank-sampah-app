<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;   // Import View
use Illuminate\Support\Facades\Schema; // Import Schema (Solusi Error P1009)
use App\Models\Pengaturan;             // Import Model Pengaturan
use Illuminate\Support\Facades\Gate;
use App\Models\Penjemputan;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // PENCEGAHAN ERROR: Cek dulu apakah tabel 'pengaturans' sudah ada?
        // (Ini mencegah error saat pertama kali jalankan 'php artisan migrate')
        
        if (Schema::hasTable('pengaturans')) {
            // Logika Global Variable (Jadwal Operasional)
            View::composer('*', function ($view) {
                $tglBuka   = Pengaturan::where('key', 'tanggal_buka')->value('value');
                $jamBuka   = Pengaturan::where('key', 'jam_buka')->value('value') ?? '08:00';
                $jamTutup  = Pengaturan::where('key', 'jam_tutup')->value('value') ?? '16:00';

                $view->with('globalTglBuka', $tglBuka);
                $view->with('globalJamBuka', $jamBuka);
                $view->with('globalJamTutup', $jamTutup);
            });
        }

        // Gate: isAdmin (Hanya Admin yang boleh)
        Gate::define('isAdmin', function($user) {
            return $user->role == 'admin';
        });

        // Gate: isKetua (Hanya Ketua yang boleh)
        Gate::define('isKetua', function($user) {
            return $user->role == 'ketua';
        });

        // Gate: isManajemen (Admin DAN Ketua boleh masuk)
        // Ini dipakai untuk menu-menu yang Ketua juga boleh lihat (Dashboard, Laporan, dll)
        Gate::define('isManajemen', function($user) {
            return in_array($user->role, ['admin', 'ketua']);
        });

        // === 2. SHARE VARIABLE GLOBAL (UNTUK SIDEBAR/BADGE) ===
        // Ini agar $permintaanBaruList tersedia di SEMUA halaman (termasuk Penjualan)
        View::composer('*', function ($view) {
            // Cek dulu apakah tabel penjemputans sudah ada (biar ga error pas migrate fresh)
            if (Schema::hasTable('penjemputans')) {
                $permintaanBaruList = Penjemputan::where('status', 'Menunggu Konfirmasi')
                                                 ->whereNull('petugas_id') // Opsional: yg belum diambil
                                                 ->get();
                $view->with('permintaanBaruList', $permintaanBaruList);
            }
        });
    }
}