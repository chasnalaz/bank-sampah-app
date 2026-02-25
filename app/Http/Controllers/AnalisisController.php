<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Nasabah;
use App\Models\Absensi;
use App\Models\User;
use App\Models\JenisSampah; // <--- Jangan lupa import ini
use App\Models\RiwayatHarga;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalisisController extends Controller
{
    // --- 1. HALAMAN UTAMA / DASHBOARD ANALISIS (Opsional, buat jaga-jaga) ---
    public function index()
    {
        return redirect()->route('analisis.statistik');
    }

    public function statistik(Request $request)
    {
        // 1. SETUP PERIODE (DEFAULT: TAHUN INI)
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan'); 
        
        // Setup Tanggal Saat Ini & Tanggal Lalu (Untuk perbandingan tren)
        if ($request->filter_jenis == 'bulan' && $request->bulan) {
            // MODE BULANAN
            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $endDate   = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
            
            $startDateLalu = $startDate->copy()->subMonth(); // Bandingkan dg Bulan Lalu
            $endDateLalu   = $endDate->copy()->subMonth();
            
            // PERBAIKAN DI SINI: Gunakan $labelFilter
            $labelFilter = "Bulan " . $startDate->translatedFormat('F Y');
            $modeGrafik = 'harian';
        } else {
            // MODE TAHUNAN (DEFAULT)
            $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfYear();
            $endDate   = Carbon::createFromDate($tahun, 12, 31)->endOfYear();
            
            $startDateLalu = $startDate->copy()->subYear(); // Bandingkan dg Tahun Lalu
            $endDateLalu   = $endDate->copy()->subYear();
            
            // PERBAIKAN DI SINI: Gunakan $labelFilter
            $labelFilter = "Tahun " . $tahun;
            $modeGrafik = 'bulanan';
        }

        // 2. QUERY DATA UTAMA
        $uangKeluar = Transaksi::where('jenis_transaksi', 'setor')
            ->whereBetween('created_at', [$startDate, $endDate])->sum('total_harga');
        
        $totalBerat = Transaksi::where('jenis_transaksi', 'setor')
            ->whereBetween('created_at', [$startDate, $endDate])->sum('berat');

        // Data Periode Lalu (Untuk Cek Tren Naik/Turun)
        $beratLalu = Transaksi::where('jenis_transaksi', 'setor')
            ->whereBetween('created_at', [$startDateLalu, $endDateLalu])->sum('berat');

        // 3. KOMPOSISI SAMPAH
        $komposisi = DB::table('transaksis')
            ->join('jenis_sampahs', 'transaksis.jenis_sampah_id', '=', 'jenis_sampahs.id')
            ->select('jenis_sampahs.nama_sampah', DB::raw('sum(transaksis.berat) as total'))
            ->where('transaksis.jenis_transaksi', 'setor')
            ->whereBetween('transaksis.created_at', [$startDate, $endDate])
            ->groupBy('jenis_sampahs.nama_sampah')
            ->get();
        
        $lblKomposisi = $komposisi->pluck('nama_sampah');
        $valKomposisi = $komposisi->pluck('total');

        // 4. GRAFIK TREN (DINAMIS)
        $trenLabels = [];
        $trenValues = [];

        if ($modeGrafik == 'bulanan') {
            for ($i = 1; $i <= 12; $i++) {
                $trenLabels[] = Carbon::create()->month($i)->translatedFormat('M');
                $trenValues[] = Transaksi::where('jenis_transaksi', 'setor')
                    ->whereYear('created_at', $tahun)->whereMonth('created_at', $i)->sum('berat');
            }
        } else {
            $daysInMonth = $startDate->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $trenLabels[] = $i;
                $trenValues[] = Transaksi::where('jenis_transaksi', 'setor')
                    ->whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->whereDay('created_at', $i)->sum('berat');
            }
        }

        // 5. DATA PENDUKUNG (Nasabah & Petugas)
        $totalNasabah = Nasabah::count();
        $nasabahAktif = Transaksi::where('jenis_transaksi', 'setor')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('nasabah_id')->count('nasabah_id');
        
        $persenAktif = $totalNasabah > 0 ? ($nasabahAktif / $totalNasabah) * 100 : 0;

        $topNasabah = Transaksi::with('nasabah')
            ->select('nasabah_id', DB::raw('sum(berat) as total_berat'), DB::raw('count(*) as frekuensi'))
            ->where('jenis_transaksi', 'setor')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('nasabah_id')->orderByDesc('total_berat')->limit(5)->get();

        $kinerjaPetugas = User::where('role', 'petugas')
            ->withCount(['transaksis' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])->get()->sortByDesc('transaksis_count');


        // --- 6. LOGIKA OTAK AI (SARAN CERDAS) ---
        $saran = [];

        // Cek 1: Aktivitas Nol
        if ($totalBerat == 0) {
            $saran[] = "⚠️ Belum ada aktivitas sampah pada periode ini. Operasional tampak terhenti.";
        }

        // Cek 2: Partisipasi Nasabah
        if ($totalNasabah > 0 && $persenAktif < 15) {
            $saran[] = "📢 Partisipasi rendah (<15%). Lakukan sosialisasi ulang kepada warga.";
        }

        // Cek 3: Tren Naik/Turun (Bandingkan dengan periode lalu)
        if ($totalBerat > 0 && $beratLalu > 0) {
            if ($totalBerat < $beratLalu) {
                $selisih = number_format($beratLalu - $totalBerat, 1);
                $saran[] = "📉 Tren Negatif: Volume sampah TURUN $selisih Kg dibanding periode sebelumnya.";
            } elseif ($totalBerat > $beratLalu * 1.2) {
                 $saran[] = "📈 Tren Positif: Volume melonjak signifikan (>20%). Pastikan kapasitas gudang cukup.";
            }
        }

        // Cek 4: Dominasi Jenis Sampah
        foreach ($komposisi as $data) {
            $persenJenis = ($totalBerat > 0) ? ($data->total / $totalBerat) * 100 : 0;
            if ($persenJenis > 50) {
                $saran[] = "💡 Insight: Sampah '{$data->nama_sampah}' mendominasi (" . number_format($persenJenis, 0) . "%). Pertimbangkan cari pengepul spesialis {$data->nama_sampah}.";
            }
        }

        // Cek 5: Keuangan
        if ($uangKeluar > 2000000) { 
            $saran[] = "💰 Pengeluaran Tinggi: Rp " . number_format($uangKeluar) . " keluar. Cek ketersediaan kas tunai.";
        }

        // Jika semua aman
        if (empty($saran) && $totalBerat > 0) {
            $saran[] = "✅ Performa Stabil: Tidak ada isu kritikal yang terdeteksi.";
        }


        // 7. RIWAYAT HARGA
        $daftarSampah = JenisSampah::orderBy('nama_sampah', 'asc')->get();
        $rawRiwayat = RiwayatHarga::orderBy('created_at', 'asc')->get();
        $chartRiwayat = [];
        foreach($daftarSampah as $s) {
            $history = $rawRiwayat->where('jenis_sampah_id', $s->id);
            $dataPoints = [];
            foreach($history as $h) {
                $dataPoints[] = ['tgl' => $h->created_at->format('d M Y'), 'harga' => $h->harga_baru];
            }
            $dataPoints[] = ['tgl' => 'Sekarang', 'harga' => $s->harga_per_kg];
            $chartRiwayat[$s->id] = $dataPoints;
        }

        return view('analisis.statistik', compact(
            'labelFilter', // SEKARANG VARIABLE INI SUDAH ADA ISINYA
            'modeGrafik', 'tahun', 'bulan', 
            'uangKeluar', 'totalBerat', 'beratLalu',
            'lblKomposisi', 'valKomposisi', 
            'trenLabels', 'trenValues',
            'topNasabah', 'kinerjaPetugas', 'saran', 'persenAktif', 'totalNasabah',
            'daftarSampah', 'chartRiwayat'
        ));
    }

    // --- 3. HALAMAN REKOMENDASI TENGKULAK (INI YANG DITAMBAHKAN) ---
    public function rekomendasi()
    {
        // Ambil Jenis Sampah beserta Data Tengkulak, urutkan dari harga tertinggi
        $rekomendasi = JenisSampah::with(['tengkulaks' => function($query) {
            $query->orderBy('harga_beli', 'desc');
        }])->get();

        // Pastikan view ada di folder: resources/views/analisis/rekomendasi.blade.php
        return view('analisis.rekomendasi', compact('rekomendasi'));
    }
}