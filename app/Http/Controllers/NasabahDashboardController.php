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
use App\Models\User; // <--- TAMBAHAN: Biar bisa cari petugas
use App\Models\RiwayatHarga;
use Carbon\Carbon;
use App\Services\WA; // <--- TAMBAHAN: Panggil Tukang Pos WA

class NasabahDashboardController extends Controller
{
    // --- HALAMAN BERANDA (DASHBOARD) ---
    public function index()
    {
        $nasabah = Auth::guard('nasabah')->user();

        $edukasiList = Edukasi::latest()->take(3)->get();
        
        $tglBuka  = Pengaturan::where('key', 'tanggal_buka')->value('value');
        $jamBuka  = Pengaturan::where('key', 'jam_buka')->value('value') ?? '08:00';
        $jamTutup = Pengaturan::where('key', 'jam_tutup')->value('value') ?? '16:00';

        $sekarang = Carbon::now('Asia/Jakarta');
        $isHariH  = ($tglBuka == $sekarang->format('Y-m-d'));

        $waktuBuka  = Carbon::createFromTimeString($jamBuka, 'Asia/Jakarta');
        $waktuTutup = Carbon::createFromTimeString($jamTutup, 'Asia/Jakarta');
        
        $isJamKerja = $sekarang->between($waktuBuka, $waktuTutup);
        $sedangBuka = $isHariH && $isJamKerja; 

        $daftarSampah = JenisSampah::orderBy('nama_sampah', 'asc')->get();

        // --- [TAMBAHAN BARU] PERSIAPAN DATA GRAFIK RIWAYAT ---
        // Kita siapkan data JSON agar saat diklik langsung muncul grafiknya (tanpa loading)
        $rawRiwayat = RiwayatHarga::orderBy('created_at', 'asc')->get();
        $chartRiwayat = [];

        foreach($daftarSampah as $s) {
            // Ambil history milik sampah ini
            $history = $rawRiwayat->where('jenis_sampah_id', $s->id);
            $dataPoints = [];
            
            // Masukkan titik history lama
            foreach($history as $h) {
                $dataPoints[] = [
                    'tgl'   => $h->created_at->format('d M Y'),
                    'harga' => $h->harga_baru
                ];
            }
            
            // Masukkan harga HARI INI sebagai titik terakhir
            $dataPoints[] = [
                'tgl'   => 'Hari Ini',
                'harga' => $s->harga_per_kg
            ];

            $chartRiwayat[$s->id] = $dataPoints;
        }

        return view('nasabah.dashboard', compact(
            'nasabah', 
            'edukasiList',
            'tglBuka', 
            'jamBuka',   
            'jamTutup',  
            'sedangBuka',
            'daftarSampah',
            'chartRiwayat'
        ));
    }

    // --- HALAMAN RIWAYAT ---
    public function riwayat(Request $request)
    {
        $nasabah = Auth::guard('nasabah')->user();
        
        $query = Transaksi::where('nasabah_id', $nasabah->id);
        
        // DEFAULT LABEL (Jika tidak ada filter)
        $labelFilter = "Semua Riwayat Transaksi";

        if ($request->has('filter_jenis')) {
            switch ($request->filter_jenis) {
                case 'hari_ini':
                    $query->whereDate('created_at', Carbon::today());
                    $labelFilter = "Transaksi Hari Ini (" . Carbon::now()->translatedFormat('d F Y') . ")";
                    break;
                
                case '7_hari':
                    $query->where('created_at', '>=', Carbon::today()->subDays(7));
                    $labelFilter = "7 Hari Terakhir";
                    break;
                
                case 'bulan':
                    if ($request->bulan && $request->tahun) {
                        $query->whereMonth('created_at', $request->bulan)
                              ->whereYear('created_at', $request->tahun);
                        
                        // Bikin nama bulan bahasa Indonesia
                        $namaBulan = Carbon::createFromDate($request->tahun, $request->bulan, 1)->translatedFormat('F Y');
                        $labelFilter = "Riwayat Bulan " . $namaBulan;
                    }
                    break;
                
                case 'custom':
                    if ($request->tgl_awal && $request->tgl_akhir) {
                        $query->whereDate('created_at', '>=', $request->tgl_awal)
                              ->whereDate('created_at', '<=', $request->tgl_akhir);
                        
                        $tgl1 = Carbon::parse($request->tgl_awal)->translatedFormat('d M Y');
                        $tgl2 = Carbon::parse($request->tgl_akhir)->translatedFormat('d M Y');
                        $labelFilter = "Periode: $tgl1 - $tgl2";
                    }
                    break;
            }
        }

        $semuaTransaksi = $query->latest()->paginate(10)->withQueryString(); 

        // Kirim variabel $labelFilter ke View
        return view('nasabah.riwayat', compact('nasabah', 'semuaTransaksi', 'labelFilter'));
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

    // --- PROSES SIMPAN REQUEST (DENGAN WA BROADCAST) ---
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

        // 1. Simpan ke Database
        Penjemputan::create([
            'nasabah_id' => $nasabah->id,
            'alamat_penjemputan' => $validated['alamat_penjemputan'],
            'usulan_tanggal' => $validated['usulan_tanggal'],
            'jenis_sampah_id' => $validated['jenis_sampah_id'],
            'estimasi_berat' => $validated['estimasi_berat'],
            'catatan_nasabah' => $validated['catatan_nasabah'],
            'status' => 'Menunggu Konfirmasi',
        ]);

        // 2. BROADCAST WA KE SEMUA PETUGAS (NEW FEATURE) 🚀
        try {
            // Ambil semua petugas yg punya nomor HP
            $allPetugas = User::where('role', 'petugas')
                              ->whereNotNull('telepon')
                              ->get();

            // Siapkan pesan
            $pesanBroadcast = "*📢 ORDERAN JEMPUT BARU!* \n\n" .
                              "Halo Petugas, ada permintaan jemput sampah nih!\n\n" .
                              "👤 Nama: {$nasabah->nama}\n" .
                              "📍 Lokasi: {$validated['alamat_penjemputan']}\n" .
                              "⚖️ Estimasi: {$validated['estimasi_berat']} Kg\n" .
                              "📝 Catatan: {$validated['catatan_nasabah']}\n\n" .
                              "Segera buka aplikasi dan ambil tugasnya! 🚀";

            // Kirim ke setiap petugas
            foreach ($allPetugas as $petugas) {
                WA::kirim($petugas->telepon, $pesanBroadcast);
                // sleep(1); // Opsional: Jeda 1 detik
            }

        } catch (\Exception $e) {
            // Error WA dicuekin aja biar nasabah tetap sukses request
        }

        return back()->with('success', 'Permintaan penjemputan berhasil diajukan! Petugas sudah dikabari.');
    }
    // --- HALAMAN SEMUA EDUKASI ---
    public function edukasiIndex()
    {
        // Ambil nasabah untuk layout sidebar
        $nasabah = Auth::guard('nasabah')->user();
        
        // Ambil semua data edukasi, urutkan terbaru, paginasi 6 per halaman
        $semuaEdukasi = Edukasi::latest()->paginate(6);
        
        return view('nasabah.edukasi', compact('nasabah', 'semuaEdukasi'));
    }
}