<?php

namespace App\Http\Controllers;

// PASTIKAN SEMUA MODEL INI DI-IMPORT
use App\Models\Nasabah;
use App\Models\Transaksi;
use App\Models\JenisSampah;
use App\Models\Penjemputan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'admin') {
            
            // --- LOGIKA LENGKAP UNTUK ADMIN ---
            $jumlahNasabah = Nasabah::count();
            $totalSaldo = Nasabah::sum('saldo');
            $jumlahSetoran = Transaksi::where('jenis_transaksi', 'setor')->count();
            $jumlahPenarikan = Transaksi::where('jenis_transaksi', 'tarik')->count();
            $jumlahPetugas = User::where('role', 'petugas')->count();
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

            // --- LOGIKA BARU UNTUK PETUGAS ---
            
            $permintaanBaruCount = Penjemputan::whereNull('petugas_id')
                                            ->where('status', 'Menunggu Konfirmasi')
                                            ->count();

            $tugasAktifCount = Penjemputan::where('petugas_id', $user->id)
                                        ->where('status', 'Diterima')
                                        ->count();

            // <-- PERBAIKAN DI SINI
            $daftarHargaSampah = JenisSampah::orderBy('nama_sampah', 'asc')->get(); 
            
            return view('dashboard-petugas', [
                'permintaanBaruCount' => $permintaanBaruCount,
                'tugasAktifCount' => $tugasAktifCount,
                'daftarHargaSampah' => $daftarHargaSampah,
            ]);
        }
    }
}