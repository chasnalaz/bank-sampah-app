<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Transaksi; // <--- Pastikan ini ada
use App\Models\JenisSampah;
use App\Models\Penjemputan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // <--- Pastikan ini ada

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. LOGIKA ADMIN & KETUA
        if ($user->role == 'admin' || $user->role == 'ketua') {
            
            $jumlahNasabah = Nasabah::count();
            $totalSaldo = Nasabah::sum('saldo');
            
            $jumlahSetoran = Transaksi::where('jenis_transaksi', 'setor')->count();
            $jumlahPenarikan = Transaksi::where('jenis_transaksi', 'tarik')->count();

            // Hitung Petugas Siap Hari Ini
            $jumlahPetugas = User::where('role', 'petugas')
                                 ->where('status_tugas', 'siap')
                                 ->whereDate('updated_at', Carbon::today())
                                 ->count();

            $permintaanBaru = Penjemputan::where('status', 'Menunggu Konfirmasi')->count();
            $tugasBerlangsung = Penjemputan::where('status', 'Diterima')->count();
            
            return view('dashboard', [
                'jumlahNasabah' => $jumlahNasabah,
                'totalSaldo' => $totalSaldo,
                'jumlahSetoran' => $jumlahSetoran,
                'jumlahPenarikan' => $jumlahPenarikan,
                'jumlahPetugas' => $jumlahPetugas,
                'permintaanBaru' => $permintaanBaru,
                'tugasBerlangsung' => $tugasBerlangsung,
            ]);

        } else {
            // 2. LOGIKA PETUGAS (ELSE)
            
            // A. Logika Reset Status Absen (Auto-Reset)
            if ($user->status_tugas == 'siap' && !$user->updated_at->isToday()) {
                $user->status_tugas = 'izin';
                $user->save();
                $user = $user->fresh(); 
            }

            // B. Hitung Data Tugas
            $permintaanBaruCount = Penjemputan::where('status', 'Menunggu Konfirmasi')->count();
            $tugasAktifCount = Penjemputan::where('petugas_id', $user->id)
                                        ->where('status', 'Diterima')
                                        ->count();

            // C. Hitung Kinerja Hari Ini (YANG TADINYA ERROR)
            $totalBeratHariIni = Transaksi::where('petugas_id', $user->id)
                                          ->whereDate('created_at', Carbon::today())
                                          ->sum('berat'); 

            $totalUangHariIni = Transaksi::where('petugas_id', $user->id)
                                         ->whereDate('created_at', Carbon::today())
                                         ->where('jenis_transaksi', 'setor')
                                         ->sum('total_harga');

            $daftarHargaSampah = JenisSampah::orderBy('nama_sampah', 'asc')->get(); 
            
            // D. Kirim ke View
            return view('dashboard-petugas', [
                'permintaanBaruCount' => $permintaanBaruCount,
                'tugasAktifCount' => $tugasAktifCount,
                'daftarHargaSampah' => $daftarHargaSampah,
                'totalBeratHariIni' => $totalBeratHariIni, // <--- Sudah ditambahkan
                'totalUangHariIni' => $totalUangHariIni    // <--- Sudah ditambahkan
            ]);
        }
    }
}