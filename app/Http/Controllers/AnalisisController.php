<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tengkulak;
use App\Models\JenisSampah;
use App\Models\Transaksi; // Tambahan untuk analisis
use App\Models\Nasabah;   // Tambahan untuk analisis
use App\Models\User;      // Tambahan untuk analisis
use Carbon\Carbon;        // Tambahan untuk tanggal
use Illuminate\Support\Facades\DB;

class AnalisisController extends Controller
{
    // === FITUR 1: REKOMENDASI TENGKULAK (SPK) ===
    public function rekomendasi()
    {
        // Logika DSS: Mencari harga beli tertinggi untuk setiap jenis sampah
        $rekomendasi = JenisSampah::with(['tengkulaks' => function($query) {
            $query->orderBy('harga_beli', 'desc');
        }])->get();

        return view('analisis.rekomendasi', compact('rekomendasi'));
    }

    // === FITUR 2: ANALISIS & STATISTIK CERDAS ===
    public function statistik()
    {
        // ------------------------------------------
        // 1. ANALISIS VOLUME SAMPAH (TREN)
        // ------------------------------------------
        $bulanIni = Carbon::now();
        $bulanLalu = Carbon::now()->subMonth();

        // Hitung Berat
        $beratIni = Transaksi::where('jenis_transaksi', 'setor')
                    ->whereMonth('created_at', $bulanIni->month)
                    ->whereYear('created_at', $bulanIni->year)
                    ->sum('berat');
        
        $beratLalu = Transaksi::where('jenis_transaksi', 'setor')
                    ->whereMonth('created_at', $bulanLalu->month)
                    ->whereYear('created_at', $bulanLalu->year)
                    ->sum('berat');

        // Logika Pemberi Saran Volume
        $volumeDiff = $beratIni - $beratLalu;
        
        // Default values
        $analisisVolume = [
            'status' => 'warning',
            'deskripsi' => 'Data tidak cukup untuk perbandingan.',
            'kesimpulan' => 'Menunggu data lebih lanjut.',
            'saran' => 'Terus lakukan pencatatan transaksi.'
        ];

        if ($beratIni == 0) {
            $analisisVolume = [
                'status' => 'danger', 
                'deskripsi' => 'Belum ada sampah masuk bulan ini.',
                'kesimpulan' => 'Operasional vakum.',
                'saran' => 'Segera buka bank sampah dan hubungi nasabah prioritas.'
            ];
        } elseif ($volumeDiff > 0) {
            $analisisVolume = [
                'status' => 'success',
                'deskripsi' => "Volume sampah NAIK seberat " . number_format($volumeDiff) . "kg dibanding bulan lalu.",
                'kesimpulan' => 'Tren positif. Partisipasi warga meningkat.',
                'saran' => 'Pertahankan layanan. Pertimbangkan memberi bonus poin.'
            ];
        } else {
            $analisisVolume = [
                'status' => 'warning',
                'deskripsi' => "Volume sampah TURUN seberat " . number_format(abs($volumeDiff)) . "kg dibanding bulan lalu.",
                'kesimpulan' => 'Tren negatif. Minat nasabah sedang turun.',
                'saran' => 'Lakukan sosialisasi ulang atau broadcast pesan WA.'
            ];
        }

        // ------------------------------------------
        // 2. ANALISIS NASABAH (RETENSI)
        // ------------------------------------------
        $totalNasabah = Nasabah::count();
        $nasabahAktif = Transaksi::where('jenis_transaksi', 'setor')
                        ->whereMonth('created_at', $bulanIni->month)
                        ->whereYear('created_at', $bulanIni->year)
                        ->distinct('nasabah_id')
                        ->count('nasabah_id');

        $persenAktif = $totalNasabah > 0 ? ($nasabahAktif / $totalNasabah) * 100 : 0;
        
        $analisisNasabah = [];
        if ($persenAktif >= 50) {
            $analisisNasabah = [
                'status' => 'success',
                'kesimpulan' => 'Engagement Sangat Baik (>50%)',
                'saran' => 'Komunitas solid. Kenalkan program baru.'
            ];
        } elseif ($persenAktif >= 20) {
            $analisisNasabah = [
                'status' => 'warning',
                'kesimpulan' => 'Engagement Sedang. Mayoritas pasif.',
                'saran' => 'Lakukan pendekatan personal ke nasabah pasif.'
            ];
        } else {
            $analisisNasabah = [
                'status' => 'danger',
                'kesimpulan' => 'Kritis. Hampir tidak ada nasabah aktif.',
                'saran' => 'Dibutuhkan event besar untuk menarik perhatian warga.'
            ];
        }

        // ------------------------------------------
        // 3. ANALISIS KEAKTIFAN PETUGAS (DUMMY DATA - SAFE MODE)
        // ------------------------------------------
        // Kita matikan dulu query database karena kolom user_id belum ada
        // $kinerjaPetugas = ... (Dihapus sementara agar tidak error)
        
        $topPetugas = null; 
        $analisisPetugas = [
            'saran' => "Fitur analisis kinerja petugas belum dapat digunakan (Data pencatat transaksi belum tersedia di database)."
        ];

        // Return ke view yang sesuai dengan struktur folder kamu
        return view('analisis.statistik', compact(
            'beratIni', 'beratLalu', 'analisisVolume',
            'totalNasabah', 'nasabahAktif', 'persenAktif', 'analisisNasabah',
            'topPetugas', 'analisisPetugas'
        ));
    }
}