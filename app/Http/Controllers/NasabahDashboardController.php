<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Transaksi; // <-- TAMBAHKAN INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Penjemputan;
use App\Models\JenisSampah;

class NasabahDashboardController extends Controller
{
    public function index()
    {
        // Ambil data nasabah yang sedang login
        $nasabah = Auth::guard('nasabah')->user();

        // Ambil riwayat transaksi milik nasabah tersebut, urutkan dari yang paling baru
        $riwayatTransaksi = Transaksi::where('nasabah_id', $nasabah->id)
                                    ->latest() // Mengurutkan berdasarkan 'created_at' dari terbaru
                                    ->paginate(10); // Mengambil 10 transaksi per halaman

        // Kirim data nasabah dan riwayat transaksinya ke view
        return view('nasabah.dashboard', [
            'nasabah' => $nasabah,
            'riwayatTransaksi' => $riwayatTransaksi
        ]);
    }

   public function showPenjemputan()
    {
        $nasabah = Auth::guard('nasabah')->user();
        
        // Ambil data untuk dropdown form
        $jenisSampahList = JenisSampah::orderBy('nama_sampah', 'asc')->get();
        
        // Ambil riwayat penjemputan milik nasabah ini
        $riwayatPenjemputan = Penjemputan::where('nasabah_id', $nasabah->id)
                                    ->latest()
                                    ->get();

        return view('nasabah.penjemputan', [
            'nasabah' => $nasabah,
            'riwayatPenjemputan' => $riwayatPenjemputan,
            'jenisSampahList' => $jenisSampahList, // Kirim data sampah ke view
        ]);
    }

    public function storePenjemputan(Request $request)
    {
        $nasabah = Auth::guard('nasabah')->user();

        $validated = $request->validate([
            'alamat_penjemputan' => 'required|string',
            'usulan_tanggal' => 'required|date|after_or_equal:today',
            'jenis_sampah_id' => 'required|exists:jenis_sampahs,id',
            'estimasi_berat' => 'nullable|string|max:100',
            'catatan_nasabah' => 'nullable|string|max:255',
        ]);

        Penjemputan::create([
            'nasabah_id' => $nasabah->id,
            'alamat_penjemputan' => $validated['alamat_penjemputan'],
            'usulan_tanggal' => $validated['usulan_tanggal'],
            'jenis_sampah_id' => $validated['jenis_sampah_id'],
            'estimasi_berat' => $validated['estimasi_berat'],
            'catatan_nasabah' => $validated['catatan_nasabah'],
            'status' => 'Menunggu Konfirmasi',
        ]);

        return back()->with('success', 'Permintaan penjemputan berhasil diajukan!');
    }
}