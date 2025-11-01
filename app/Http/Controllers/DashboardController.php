<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Transaksi;
use App\Models\JenisSampah; // Pastikan ini ada
use App\Models\Penjemputan; // Pastikan ini ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Periksa peran pengguna yang sedang login
        if (Auth::user()->role == 'admin') {
            
            // --- LOGIKA UNTUK ADMIN ---
            $jumlahNasabah = Nasabah::count();
            $totalSaldo = Nasabah::sum('saldo');
            $jumlahSetoran = Transaksi::where('jenis_transaksi', 'setor')->count();
            $jumlahPenarikan = Transaksi::where('jenis_transaksi', 'tarik')->count();

            // Kirim data ke view dashboard admin
            return view('dashboard', [
                'jumlahNasabah' => $jumlahNasabah,
                'totalSaldo' => $totalSaldo,
                'jumlahSetoran' => $jumlahSetoran,
                'jumlahPenarikan' => $jumlahPenarikan,
            ]);

        } else {

            // --- LOGIKA UNTUK PETUGAS ---
            $daftarHargaSampah = JenisSampah::orderBy('nama_sampah', 'asc')->get();
            
            $permintaanPenjemputan = Penjemputan::where('status', 'Menunggu Konfirmasi')
                                                ->with('nasabah', 'jenisSampah')
                                                ->latest()
                                                ->get();

            // Kirim data ke view dashboard petugas
            return view('dashboard-petugas', [
                'daftarHargaSampah' => $daftarHargaSampah,
                'permintaanPenjemputan' => $permintaanPenjemputan,
            ]);
        }
    }
}