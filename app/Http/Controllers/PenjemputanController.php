<?php

namespace App\Http\Controllers;

use App\Models\Penjemputan;
use Illuminate\Http\Request;

class PenjemputanController extends Controller
{
    // Aksi untuk menerima permintaan
    public function terima(Penjemputan $penjemputan)
    {
        $penjemputan->status = 'Diterima';
        $penjemputan->save();

        return back()->with('success', 'Permintaan penjemputan berhasil diterima.');
    }

    public function index()
    {
        // 1. Data untuk Tab "Tugas Aktif" (Hanya yang statusnya Diterima)
        $tugasAktif = Penjemputan::where('status', 'Diterima')
                                    ->with('nasabah', 'jenisSampah')
                                    ->latest('usulan_tanggal')
                                    ->get();

        // 2. Data untuk Tab "Riwayat Selesai" (Yang Selesai atau Ditolak)
        $riwayatSelesai = Penjemputan::whereIn('status', ['Selesai', 'Ditolak'])
                                    ->with('nasabah')
                                    ->latest('updated_at') // Urutkan berdasarkan kapan terakhir diubah
                                    ->get();

        return view('tugas-penjemputan.index', [
            'tugasAktifList' => $tugasAktif,
            'riwayatSelesaiList' => $riwayatSelesai,
        ]);
    }

    /**
     * Menandai permintaan sebagai "Selesai".
     */
    public function selesaikan(Penjemputan $penjemputan)
    {
        $penjemputan->status = 'Selesai';
        $penjemputan->save();

        return back()->with('success', 'Tugas penjemputan telah ditandai sebagai Selesai.');
    }

    // Aksi untuk menolak permintaan
    public function tolak(Penjemputan $penjemputan)
    {
        $penjemputan->status = 'Ditolak';
        $penjemputan->save();

        return back()->with('success', 'Permintaan penjemputan telah ditolak.');
    }
}