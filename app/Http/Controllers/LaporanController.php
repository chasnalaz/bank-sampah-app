<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Penjualan; // Pastikan model ini ada
use Carbon\Carbon;

class LaporanController extends Controller
{
    // Method ini yang dicari oleh Route (sesuai error log kamu)
    public function riwayatTransaksi(Request $request)
    {
        // 1. SIAPKAN QUERY BUILDER
        // Transaksi (Pengeluaran ke Nasabah)
        $qTransaksi = Transaksi::with('nasabah')
                        ->where('jenis_transaksi', 'setor') // Hanya setor yang keluar uang
                        ->latest();

        // Penjualan (Pemasukan dari Tengkulak)
        // Asumsi kamu punya model Penjualan. Jika belum, buat dulu atau hapus bagian ini.
        $qPenjualan = Penjualan::with('tengkulak')->latest(); 

        // 2. LOGIKA FILTER WAKTU (Menerapkan ke kedua query)
        $labelFilter = "Semua Data Transaksi";

        if ($request->has('filter_jenis')) {
            switch ($request->filter_jenis) {
                case 'hari_ini':
                    $today = Carbon::today();
                    $qTransaksi->whereDate('created_at', $today);
                    $qPenjualan->whereDate('tanggal_jual', $today); // Sesuaikan nama kolom tanggal di tabel penjualan
                    $labelFilter = "Laporan Hari Ini (" . Carbon::now()->translatedFormat('d F Y') . ")";
                    break;
                
                case '7_hari':
                    $date = Carbon::today()->subDays(7);
                    $qTransaksi->where('created_at', '>=', $date);
                    $qPenjualan->where('tanggal_jual', '>=', $date);
                    $labelFilter = "7 Hari Terakhir";
                    break;
                
                case 'bulan':
                    if ($request->bulan && $request->tahun) {
                        $qTransaksi->whereMonth('created_at', $request->bulan)
                                   ->whereYear('created_at', $request->tahun);
                        $qPenjualan->whereMonth('tanggal_jual', $request->bulan)
                                   ->whereYear('tanggal_jual', $request->tahun);
                        
                        $namaBulan = Carbon::createFromDate($request->tahun, $request->bulan, 1)->translatedFormat('F Y');
                        $labelFilter = "Laporan Bulan " . $namaBulan;
                    }
                    break;
                
                case 'custom':
                    if ($request->tgl_awal && $request->tgl_akhir) {
                        $qTransaksi->whereDate('created_at', '>=', $request->tgl_awal)
                                   ->whereDate('created_at', '<=', $request->tgl_akhir);
                        $qPenjualan->whereDate('tanggal_jual', '>=', $request->tgl_awal)
                                   ->whereDate('tanggal_jual', '<=', $request->tgl_akhir);
                        
                        $tgl1 = Carbon::parse($request->tgl_awal)->translatedFormat('d M Y');
                        $tgl2 = Carbon::parse($request->tgl_akhir)->translatedFormat('d M Y');
                        $labelFilter = "Periode: $tgl1 - $tgl2";
                    }
                    break;
            }
        }

        // 3. EKSEKUSI DATA (Get Results)
        // Kita gunakan get() bukan paginate() untuk perhitungan total akurat di halaman ini
        $transaksi = $qTransaksi->get();
        $penjualan = $qPenjualan->get();

        // 4. HITUNG RINGKASAN KEUANGAN
        $totalPengeluaran = $transaksi->sum('total_harga');       // Uang keluar ke nasabah
        $totalPemasukan   = $penjualan->sum('total_pendapatan');  // Uang masuk dari tengkulak
        $keuntungan       = $totalPemasukan - $totalPengeluaran;

        // 5. RETURN VIEW
        // Pastikan nama view sesuai dengan folder kamu. 
        // Kalau file view-nya bernama 'transaksi.blade.php' di folder 'admin/laporan', pakai ini:
        return view('admin.laporan.transaksi', compact(
            'transaksi', 
            'penjualan', 
            'totalPengeluaran', 
            'totalPemasukan', 
            'keuntungan', 
            'labelFilter'
        ));
    }
    
    // Method Cetak PDF (Opsional, biar tidak error jika tombol cetak diklik)
    public function cetakPDF(Request $request)
    {
        // Logikanya nanti sama dengan di atas, tapi return-nya PDF.
        // Untuk sementara kita return string dulu biar ga error 404.
        return "Fitur Cetak PDF sedang disiapkan. Filter: " . json_encode($request->all());
    }

    // --- TAMBAHAN BARU: FITUR CETAK ---
    public function cetakTransaksi(Request $request)
    {
        // 1. COPY LOGIKA QUERY DARI METHOD riwayatTransaksi
        $qTransaksi = Transaksi::with('nasabah')->where('jenis_transaksi', 'setor')->latest();
        $qPenjualan = Penjualan::with('tengkulak')->latest(); 

        $labelFilter = "Semua Data Transaksi";

        if ($request->has('filter_jenis')) {
            switch ($request->filter_jenis) {
                case 'hari_ini':
                    $today = Carbon::today();
                    $qTransaksi->whereDate('created_at', $today);
                    $qPenjualan->whereDate('tanggal_jual', $today);
                    $labelFilter = "Laporan Hari Ini (" . Carbon::now()->translatedFormat('d F Y') . ")";
                    break;
                
                case '7_hari':
                    $date = Carbon::today()->subDays(7);
                    $qTransaksi->where('created_at', '>=', $date);
                    $qPenjualan->where('tanggal_jual', '>=', $date);
                    $labelFilter = "7 Hari Terakhir";
                    break;
                
                case 'bulan':
                    if ($request->bulan && $request->tahun) {
                        $qTransaksi->whereMonth('created_at', $request->bulan)
                                   ->whereYear('created_at', $request->tahun);
                        $qPenjualan->whereMonth('tanggal_jual', $request->bulan)
                                   ->whereYear('tanggal_jual', $request->tahun);
                        
                        $namaBulan = Carbon::createFromDate($request->tahun, $request->bulan, 1)->translatedFormat('F Y');
                        $labelFilter = "Laporan Bulan " . $namaBulan;
                    }
                    break;
                
                case 'custom':
                    if ($request->tgl_awal && $request->tgl_akhir) {
                        $qTransaksi->whereDate('created_at', '>=', $request->tgl_awal)
                                   ->whereDate('created_at', '<=', $request->tgl_akhir);
                        $qPenjualan->whereDate('tanggal_jual', '>=', $request->tgl_awal)
                                   ->whereDate('tanggal_jual', '<=', $request->tgl_akhir);
                        
                        $tgl1 = Carbon::parse($request->tgl_awal)->translatedFormat('d M Y');
                        $tgl2 = Carbon::parse($request->tgl_akhir)->translatedFormat('d M Y');
                        $labelFilter = "Periode: $tgl1 - $tgl2";
                    }
                    break;
            }
        }

        // 2. EKSEKUSI DATA
        $transaksi = $qTransaksi->get();
        $penjualan = $qPenjualan->get();

        // 3. HITUNG TOTAL
        $totalPengeluaran = $transaksi->sum('total_harga');
        $totalPemasukan   = $penjualan->sum('total_pendapatan');
        $keuntungan       = $totalPemasukan - $totalPengeluaran;

        // 4. RETURN KE VIEW KHUSUS CETAK
        return view('admin.laporan.cetak_transaksi', compact(
            'transaksi', 'penjualan', 
            'totalPengeluaran', 'totalPemasukan', 'keuntungan', 
            'labelFilter'
        ));
    }
}