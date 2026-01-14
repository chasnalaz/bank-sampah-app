<?php

namespace App\Http\Controllers;

use App\Models\Penjemputan;
use App\Models\JenisSampah;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\Pengaturan; // <--- WAJIB: Import Model Pengaturan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // <--- WAJIB: Import Carbon
use Exception;

class PenjemputanController extends Controller
{
    // --- METHOD HELPER (PRIVATE) UNTUK CEK JADWAL ---
    private function cekJadwalOperasional()
    {
        // 1. Ambil Data Jadwal
        $tanggalBuka = Pengaturan::where('key', 'tanggal_buka')->value('value');
        $jamBuka     = Pengaturan::where('key', 'jam_buka')->value('value') ?? '08:00';
        $jamTutup    = Pengaturan::where('key', 'jam_tutup')->value('value') ?? '16:00';

        // 2. Cek Tanggal
        $hariIni = Carbon::now()->format('Y-m-d');
        if (!$tanggalBuka || $hariIni != $tanggalBuka) {
            $infoTanggal = $tanggalBuka ? Carbon::parse($tanggalBuka)->translatedFormat('d F Y') : 'Belum Diatur';
            return "AKSI DITOLAK! Bank Sampah sedang tutup. Jadwal buka berikutnya: " . $infoTanggal;
        }

        // 3. Cek Jam
        $sekarang   = Carbon::now();
        $waktuBuka  = Carbon::createFromTimeString($jamBuka);
        $waktuTutup = Carbon::createFromTimeString($jamTutup);

        if (!$sekarang->between($waktuBuka, $waktuTutup)) {
            return "AKSI DITOLAK! Jam operasional hari ini hanya pukul $jamBuka - $jamTutup WIB.";
        }

        return null; // Aman (Buka)
    }

    // --- METHOD PETUGAS: TERIMA TUGAS ---
    public function terima(Penjemputan $penjemputan)
    {
        // 1. CEK SATPAM DULU!
        $errorJadwal = $this->cekJadwalOperasional();
        if ($errorJadwal) {
            return back()->with('error', $errorJadwal);
        }

        // 2. Cek Validasi Lain
        if ($penjemputan->petugas_id !== null) {
            return back()->with('error', 'Tugas ini sudah diambil oleh petugas lain.');
        }

        // 3. Eksekusi
        $penjemputan->petugas_id = Auth::id();
        $penjemputan->status = 'Diterima';
        $penjemputan->save();

        return redirect()->route('penjemputan.tugas')->with('success', 'Anda berhasil mengambil tugas penjemputan ini.');
    }

    public function index()
    {
        $petugasId = Auth::id();

        // 1. Data Tab "Permintaan Baru"
        $permintaanBaru = Penjemputan::whereNull('petugas_id')
                                ->where('status', 'Menunggu Konfirmasi')
                                ->with('nasabah', 'jenisSampah')
                                ->latest('usulan_tanggal')
                                ->get();
                                
        // 2. Data Tab "Tugas Aktif Saya"
        $tugasAktif = Penjemputan::where('status', 'Diterima')
                                    ->where('petugas_id', $petugasId)
                                    ->with('nasabah', 'jenisSampah')
                                    ->latest('usulan_tanggal')
                                    ->get();

        // 3. Data Tab "Riwayat Selesai Saya"
        $riwayatSelesai = Penjemputan::where('status', 'Selesai')
                                    ->where('petugas_id', $petugasId)
                                    ->with('nasabah')
                                    ->latest('updated_at')
                                    ->get();
        
        $allJenisSampah = JenisSampah::orderBy('nama_sampah', 'asc')->get();

        return view('tugas-penjemputan.index', [
            'permintaanBaruList' => $permintaanBaru,
            'tugasAktifList' => $tugasAktif,
            'riwayatSelesaiList' => $riwayatSelesai,
            'allJenisSampah' => $allJenisSampah, 
        ]);
    }

    // --- METHOD PETUGAS: SELESAIKAN TUGAS ---
    public function selesaikan(Request $request, Penjemputan $penjemputan)
    {
        // 1. CEK SATPAM DULU!
        $errorJadwal = $this->cekJadwalOperasional();
        if ($errorJadwal) {
            return back()->with('error', $errorJadwal);
        }

        $request->validate([
            'jenis_sampah_id' => 'required|exists:jenis_sampahs,id',
            'berat_aktual' => 'required|numeric|min:0.01',
        ]);

        if ($penjemputan->petugas_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak menyelesaikan tugas ini.');
        }

        DB::beginTransaction();
        try {
            
            $jenisSampah = JenisSampah::find($request->jenis_sampah_id);
            $nasabah = $penjemputan->nasabah;
            $berat = $request->berat_aktual;
            $total_harga = $jenisSampah->harga_per_kg * $berat;

            // Simpan ke Transaksi
            Transaksi::create([
                'nasabah_id' => $nasabah->id,
                'petugas_id' => Auth::id(), // Pastikan kolom ini ada di tabel transaksi
                'jenis_transaksi' => 'setor',
                'jenis_sampah' => $jenisSampah->nama_sampah, // Sesuaikan dg kolom di tabel (nama atau id)
                'berat' => $berat,
                'total_harga' => $total_harga,
                'tanggal_transaksi' => now(), 
            ]);

            // Update Saldo
            $nasabah->saldo += $total_harga;
            $nasabah->save();

            // Update Status Penjemputan
            $penjemputan->status = 'Selesai';
            // Pastikan kolom ini ada di tabel penjemputans, kalau tidak ada hapus saja baris ini:
            // $penjemputan->jenis_sampah_id = $jenisSampah->id; 
            // $penjemputan->estimasi_berat = $berat;
            $penjemputan->save();

            DB::commit();

            return back()->with('success', 'Tugas berhasil diselesaikan dan transaksi telah dicatat.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan transaksi. Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function batalkan(Penjemputan $penjemputan)
    {
        if ($penjemputan->petugas_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak membatalkan tugas ini.');
        }
        $penjemputan->petugas_id = null;
        $penjemputan->status = 'Menunggu Konfirmasi';
        $penjemputan->save();
        return redirect()->route('penjemputan.tugas')->with('success', 'Tugas telah dibatalkan dan dikembalikan ke daftar.');
    }

    // --- METHOD ADMIN ---
    public function adminIndex()
    {
        $permintaanBaru = Penjemputan::whereNull('petugas_id')
                                ->where('status', 'Menunggu Konfirmasi')
                                ->with('nasabah', 'jenisSampah')
                                ->latest('usulan_tanggal')
                                ->get();
                                
        $tugasBerlangsung = Penjemputan::where('status', 'Diterima')
                                    ->whereNotNull('petugas_id')
                                    ->with('nasabah', 'jenisSampah', 'petugas') 
                                    ->latest('usulan_tanggal')
                                    ->get();

        $riwayatTugas = Penjemputan::where('status', 'Selesai')
                                    ->whereNotNull('petugas_id')
                                    ->with('nasabah', 'petugas')
                                    ->latest('updated_at')
                                    ->get();
        
        // Cukup ambil petugas yang SIAP hari ini
        $daftarPetugas = User::where('role', 'petugas')
                             ->where('status_tugas', 'siap')
                             ->whereDate('updated_at', Carbon::today()) // Tambahan filter expired
                             ->orderBy('name', 'asc')
                             ->get();

        return view('admin.penjemputan.index', [
            'permintaanBaruList' => $permintaanBaru,
            'tugasBerlangsungList' => $tugasBerlangsung,
            'riwayatTugasList' => $riwayatTugas,
            'daftarPetugas' => $daftarPetugas, 
        ]);
    }

    public function adminAssign(Request $request, Penjemputan $penjemputan)
    {
        $request->validate([
            'petugas_id' => 'required|exists:users,id',
        ]);

        if ($penjemputan->petugas_id !== null || $penjemputan->status !== 'Menunggu Konfirmasi') {
            return back()->with('error', 'Tugas ini sudah diambil atau sedang diproses.');
        }

        $penjemputan->petugas_id = $request->petugas_id;
        $penjemputan->status = 'Diterima'; 
        $penjemputan->save();

        return back()->with('success', 'Petugas berhasil ditugaskan.');
    }

    public function adminDestroy(Penjemputan $penjemputan)
    {
        try {
            if ($penjemputan->status == 'Selesai') {
                return back()->with('error', 'Tidak dapat menghapus tugas yang sudah selesai.');
            }
            $penjemputan->delete();
            return back()->with('success', 'Permintaan penjemputan telah dihapus.');

        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}