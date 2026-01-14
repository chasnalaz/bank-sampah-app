<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Penjemputan;
use App\Models\JenisSampah;
use App\Models\Edukasi;
use App\Models\Pengaturan;
use Carbon\Carbon; // Pastikan import Carbon di sini

class NasabahDashboardController extends Controller
{
    // --- HALAMAN BERANDA (DASHBOARD) ---
    public function index()
    {
        $nasabah = Auth::guard('nasabah')->user();

        // 1. Ambil Riwayat Transaksi (Limit 5)
        $riwayatTransaksi = Transaksi::where('nasabah_id', $nasabah->id)
                                    ->latest()
                                    ->take(5) 
                                    ->get();

        // 2. Ambil Edukasi (Limit 3)
        $edukasiList = Edukasi::latest()->take(3)->get();
        
        // 3. Ambil Jadwal Operasional (String dari Database)
        // Kita pakai nama variabel $jamBuka (String) agar sesuai dengan View
        $tglBuka  = Pengaturan::where('key', 'tanggal_buka')->value('value');
        $jamBuka  = Pengaturan::where('key', 'jam_buka')->value('value') ?? '08:00';
        $jamTutup = Pengaturan::where('key', 'jam_tutup')->value('value') ?? '16:00';

        // 4. LOGIKA STATUS BUKA/TUTUP (Menggunakan Carbon)
        $sekarang = Carbon::now('Asia/Jakarta');
        $isHariH  = ($tglBuka == $sekarang->format('Y-m-d'));

        // Buat objek Carbon UNTUK LOGIKA (Pakai nama beda biar $jamBuka string tidak tertimpa)
        $waktuBuka  = Carbon::createFromTimeString($jamBuka, 'Asia/Jakarta');
        $waktuTutup = Carbon::createFromTimeString($jamTutup, 'Asia/Jakarta');
        
        // Cek Logic
        $isJamKerja = $sekarang->between($waktuBuka, $waktuTutup);
        $sedangBuka = $isHariH && $isJamKerja; 

        return view('nasabah.dashboard', compact(
            'nasabah', 
            'riwayatTransaksi', 
            'edukasiList',
            'tglBuka', 
            'jamBuka',   // <-- Sudah dikembalikan jadi $jamBuka (String)
            'jamTutup',  // <-- Sudah dikembalikan jadi $jamTutup (String)
            'sedangBuka'
        ));
    }

    // --- HALAMAN RIWAYAT ---
    public function riwayat()
    {
        $nasabah = Auth::guard('nasabah')->user();
        $semuaTransaksi = Transaksi::where('nasabah_id', $nasabah->id)
                                   ->latest()
                                   ->paginate(15); 

        return view('nasabah.riwayat', compact('nasabah', 'semuaTransaksi'));
    }

    // --- HALAMAN PENJEMPUTAN ---
    public function showPenjemputan()
    {
        $nasabah = Auth::guard('nasabah')->user();
        $jenisSampahList = JenisSampah::orderBy('nama_sampah', 'asc')->get();
        $riwayatPenjemputan = Penjemputan::where('nasabah_id', $nasabah->id)
                                    ->latest()
                                    ->get();

        return view('nasabah.penjemputan', [
            'nasabah' => $nasabah,
            'riwayatPenjemputan' => $riwayatPenjemputan,
            'jenisSampahList' => $jenisSampahList,
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