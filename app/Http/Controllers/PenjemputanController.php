<?php

namespace App\Http\Controllers;

use App\Models\Penjemputan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pastikan ini di-import

class PenjemputanController extends Controller
{
    /**
     * Aksi untuk MENGAMBIL (CLAIM) permintaan penjemputan.
     */
    public function terima(Penjemputan $penjemputan)
    {
        // CEK "REBUTAN": Cek apakah tugas masih tersedia (petugas_id masih null)
        if ($penjemputan->petugas_id !== null) {
            return back()->with('error', 'Tugas ini sudah diambil oleh petugas lain.');
        }

        // JIKA MASIH KOSONG: Ambil tugas ini
        $penjemputan->petugas_id = Auth::id(); // Set ID petugas yang login
        $penjemputan->status = 'Diterima';     // Ubah status
        $penjemputan->save();

        // Kembalikan ke halaman 'Tugas Penjemputan' dengan pesan sukses
        return redirect()->route('penjemputan.tugas')->with('success', 'Anda berhasil mengambil tugas penjemputan ini.');
    }

    /**
     * Menampilkan halaman "Tugas Penjemputan" (dengan 3 Tab)
     */
    public function index()
    {
        $petugasId = Auth::id();

        // 1. Data untuk Tab "Permintaan Baru" (Belum diambil siapa-siapa)
        $permintaanBaru = Penjemputan::whereNull('petugas_id')
                                ->where('status', 'Menunggu Konfirmasi') // Sesuai default migrasi
                                ->with('nasabah', 'jenisSampah')
                                ->latest('usulan_tanggal')
                                ->get();
                                
        // 2. Data untuk Tab "Tugas Aktif Saya" (Hanya yang Diterima oleh SAYA)
        $tugasAktif = Penjemputan::where('status', 'Diterima')
                                    ->where('petugas_id', $petugasId) // FILTER KUNCI
                                    ->with('nasabah', 'jenisSampah')
                                    ->latest('usulan_tanggal')
                                    ->get();

        // 3. Data untuk Tab "Riwayat Selesai Saya" (Yang Selesai atau Ditolak oleh SAYA)
        $riwayatSelesai = Penjemputan::whereIn('status', ['Selesai', 'Ditolak'])
                                    ->where('petugas_id', $petugasId) // FILTER KUNCI
                                    ->with('nasabah')
                                    ->latest('updated_at')
                                    ->get();

        return view('tugas-penjemputan.index', [
            'permintaanBaruList' => $permintaanBaru, // Data baru untuk Tab 1
            'tugasAktifList' => $tugasAktif,
            'riwayatSelesaiList' => $riwayatSelesai,
        ]);
    }

    /**
     * Menandai permintaan sebagai "Selesai".
     */
    public function selesaikan(Penjemputan $penjemputan)
    {
        // Proteksi: Pastikan hanya petugas yang bertanggung jawab yang bisa selesaikan
        if ($penjemputan->petugas_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak menyelesaikan tugas ini.');
        }

        $penjemputan->status = 'Selesai';
        $penjemputan->save();

        return back()->with('success', 'Tugas penjemputan telah ditandai sebagai Selesai.');
    }

    /**
     * Aksi untuk MENOLAK permintaan penjemputan.
     */
    public function tolak(Penjemputan $penjemputan)
    {
        // Cek "rebutan"
        if ($penjemputan->petugas_id !== null) {
            return back()->with('error', 'Tugas ini sudah diproses oleh petugas lain.');
        }

        // Catat siapa yang menolak
        $penjemputan->petugas_id = Auth::id();
        $penjemputan->status = 'Ditolak';
        $penjemputan->save();

        return redirect()->route('penjemputan.tugas')->with('success', 'Permintaan penjemputan telah ditolak.');
    }
}