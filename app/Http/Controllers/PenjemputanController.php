<?php

namespace App\Http\Controllers;

use App\Models\Penjemputan;
use App\Models\JenisSampah;
use App\Models\Transaksi;
use App\Models\User; // <-- Pastikan User di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PenjemputanController extends Controller
{
    /**
     * ====================================================================
     * FUNGSI UNTUK PETUGAS
     * ====================================================================
     */

    /**
     * Aksi untuk MENGAMBIL (CLAIM) permintaan penjemputan.
     */
    public function terima(Penjemputan $penjemputan)
    {
        if ($penjemputan->petugas_id !== null) {
            return back()->with('error', 'Tugas ini sudah diambil oleh petugas lain.');
        }
        $penjemputan->petugas_id = Auth::id();
        $penjemputan->status = 'Diterima';
        $penjemputan->save();
        return redirect()->route('penjemputan.tugas')->with('success', 'Anda berhasil mengambil tugas penjemputan ini.');
    }

    /**
     * Menampilkan halaman "Tugas Penjemputan" (dengan 3 Tab)
     */
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
        
        // 4. Ambil SEMUA jenis sampah untuk modal
        $allJenisSampah = JenisSampah::orderBy('nama_sampah', 'asc')->get(); // Menggunakan 'nama_sampah'

        return view('tugas-penjemputan.index', [
            'permintaanBaruList' => $permintaanBaru,
            'tugasAktifList' => $tugasAktif,
            'riwayatSelesaiList' => $riwayatSelesai,
            'allJenisSampah' => $allJenisSampah, 
        ]);
    }

    /**
     * Menandai permintaan sebagai "Selesai" DAN MEMBUAT TRANSAKSI.
     */
    public function selesaikan(Request $request, Penjemputan $penjemputan)
    {
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

            Transaksi::create([
                'nasabah_id' => $nasabah->id,
                'petugas_id' => Auth::id(),
                'jenis_transaksi' => 'setor',
                'jenis_sampah_id' => $jenisSampah->id,
                'berat_kg' => $berat,
                'total_harga' => $total_harga,
                'tanggal_transaksi' => now(), 
            ]);

            $nasabah->saldo += $total_harga;
            $nasabah->save();

            $penjemputan->status = 'Selesai';
            $penjemputan->jenis_sampah_id = $jenisSampah->id;
            $penjemputan->estimasi_berat = $berat;
            $penjemputan->save();

            DB::commit();

            return back()->with('success', 'Tugas berhasil diselesaikan dan transaksi telah dicatat.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan transaksi. Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Aksi untuk MEMBATALKAN tugas yang sudah diambil.
     */
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


    /**
     * ====================================================================
     * FUNGSI UNTUK ADMIN
     * ====================================================================
     */

    /**
     * Menampilkan halaman monitoring penjemputan untuk Admin.
     */
    public function adminIndex()
    {
        // 1. Data "Permintaan Baru" (Unclaimed)
        $permintaanBaru = Penjemputan::whereNull('petugas_id')
                                ->where('status', 'Menunggu Konfirmasi')
                                ->with('nasabah', 'jenisSampah')
                                ->latest('usulan_tanggal')
                                ->get();
                                
        // 2. Data "Tugas Berlangsung" (Claimed & Diterima)
        $tugasBerlangsung = Penjemputan::where('status', 'Diterima')
                                    ->whereNotNull('petugas_id')
                                    ->with('nasabah', 'jenisSampah', 'petugas') 
                                    ->latest('usulan_tanggal')
                                    ->get();

        // 3. Data "Riwayat Tugas" (Hanya Selesai)
        $riwayatTugas = Penjemputan::where('status', 'Selesai')
                                    ->whereNotNull('petugas_id')
                                    ->with('nasabah', 'petugas')
                                    ->latest('updated_at')
                                    ->get();
        
        // 4. Ambil daftar petugas untuk modal "Tugaskan"
        $daftarPetugas = User::where('role', 'petugas')->orderBy('name', 'asc')->get();

        return view('admin.penjemputan.index', [
            'permintaanBaruList' => $permintaanBaru,
            'tugasBerlangsungList' => $tugasBerlangsung,
            'riwayatTugasList' => $riwayatTugas,
            'daftarPetugas' => $daftarPetugas, 
        ]);
    }

    /**
     * [BARU] Menugaskan petugas secara paksa (Admin Override).
     */
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

    /**
     * [BARU] Menghapus permintaan penjemputan (misal: spam).
     */
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