<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Transaksi;
use App\Models\JenisSampah;
use App\Models\Penjemputan;
use App\Models\User;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'admin' || $user->role == 'ketua') {
            
            $jemputanPending = Penjemputan::where('status', 'Menunggu Konfirmasi')
                                          ->whereNull('petugas_id')
                                          ->count();

            $totalSaldo = Nasabah::sum('saldo');

            // --- [INI BAGIAN YANG KITA TAMBAHKAN] ---
            
            // 1. Hitung Berat Bulan Ini (Untuk Angka Utama)
            $totalBeratBulanIni = Transaksi::where('jenis_transaksi', 'setor')
                                           ->whereMonth('created_at', Carbon::now()->month)
                                           ->whereYear('created_at', Carbon::now()->year)
                                           ->sum('berat');

            // 2. Hitung Berat Selamanya (Untuk Info Kecil di Bawah)
            $totalBeratAllTime = Transaksi::where('jenis_transaksi', 'setor')->sum('berat');
            
            // Kita pakai variabel ini untuk kompatibilitas jika ada view lama yg pakai $totalBerat
            $totalBerat = $totalBeratAllTime; 

            // ----------------------------------------

            $petugasHadir = User::where('role', 'petugas')
                                ->where('status_tugas', 'siap')
                                ->whereDate('updated_at', Carbon::today())
                                ->count();

            $totalPetugas = User::where('role', 'petugas')->count();

            $dataKomposisi = DB::table('transaksis')
                ->join('jenis_sampahs', 'transaksis.jenis_sampah_id', '=', 'jenis_sampahs.id')
                ->select('jenis_sampahs.nama_sampah as jenis_sampah', DB::raw('SUM(transaksis.berat) as total_berat'))
                ->where('transaksis.jenis_transaksi', 'setor')
                ->groupBy('jenis_sampahs.nama_sampah')
                ->orderByDesc('total_berat')
                ->limit(5)
                ->get();
            
            $chartLabels = $dataKomposisi->pluck('jenis_sampah');
            $chartValues = $dataKomposisi->pluck('total_berat');

            $tglBuka  = Pengaturan::where('key', 'tanggal_buka')->value('value');
            $jamBuka  = Pengaturan::where('key', 'jam_buka')->value('value') ?? '08:00';
            $jamTutup = Pengaturan::where('key', 'jam_tutup')->value('value') ?? '16:00';

            return view('dashboard', [
                'jemputanPending' => $jemputanPending,
                'totalSaldo'      => $totalSaldo,
                
                // JANGAN LUPA DITAMBAHKAN DI SINI JUGA:
                'totalBerat'          => $totalBerat, 
                'totalBeratBulanIni'  => $totalBeratBulanIni, // <--- INI OBATNYA
                'totalBeratAllTime'   => $totalBeratAllTime,  // <--- INI JUGA

                'petugasHadir'    => $petugasHadir,
                'totalPetugas' => $totalPetugas,
                'chartLabels'     => $chartLabels,
                'chartValues'     => $chartValues,
                'tglBuka'         => $tglBuka,
                'jamBuka'         => $jamBuka,
                'jamTutup'        => $jamTutup,
            ]);

        } else {
            // --- LOGIKA PETUGAS (TIDAK BERUBAH) ---
            
            if ($user->status_tugas == 'siap' && !$user->updated_at->isToday()) {
                $user->status_tugas = 'izin';
                $user->save();
                $user = $user->fresh(); 
            }

            $permintaanBaruCount = Penjemputan::where('status', 'Menunggu Konfirmasi')->count();
            $tugasAktifCount = Penjemputan::where('petugas_id', $user->id)
                                        ->where('status', 'Diterima')
                                        ->count();

            $totalBeratHariIni = Transaksi::where('petugas_id', $user->id)
                                          ->whereDate('created_at', Carbon::today())
                                          ->sum('berat'); 

            $totalUangHariIni = Transaksi::where('petugas_id', $user->id)
                                         ->whereDate('created_at', Carbon::today())
                                         ->where('jenis_transaksi', 'setor')
                                         ->sum('total_harga');

            $daftarHargaSampah = JenisSampah::orderBy('nama_sampah', 'asc')->get(); 
            
            return view('dashboard-petugas', [
                'permintaanBaruCount' => $permintaanBaruCount,
                'tugasAktifCount'     => $tugasAktifCount,
                'daftarHargaSampah'   => $daftarHargaSampah,
                'totalBeratHariIni'   => $totalBeratHariIni, 
                'totalUangHariIni'    => $totalUangHariIni   
            ]);
        }
    }

    public function updatePengaturan(Request $request)
    {
        $request->validate([
            'tanggal_buka' => 'required|date',
            'jam_buka'     => 'required',
            'jam_tutup'    => 'required',
        ]);

        Pengaturan::updateOrCreate(
            ['key' => 'tanggal_buka'],
            ['value' => $request->tanggal_buka, 'user_id' => Auth::id()]
        );

        Pengaturan::updateOrCreate(
            ['key' => 'jam_buka'],
            ['value' => $request->jam_buka, 'user_id' => Auth::id()]
        );

        Pengaturan::updateOrCreate(
            ['key' => 'jam_tutup'],
            ['value' => $request->jam_tutup, 'user_id' => Auth::id()]
        );

        return redirect()->back()->with('success', 'Jadwal Berhasil Disimpan!');
    }
}