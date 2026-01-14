<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaturan;

class PengaturanController extends Controller
{
    public function update(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'tanggal_buka' => 'required|date', // Wajib pilih tanggal
            'jam_buka'     => 'required',
            'jam_tutup'    => 'required',
        ]);

        // 2. Simpan ke Database (Update atau Buat Baru)
        // Kita simpan 3 data kunci: Tanggal, Jam Buka, Jam Tutup
        Pengaturan::updateOrCreate(['key' => 'tanggal_buka'], ['value' => $request->tanggal_buka]);
        Pengaturan::updateOrCreate(['key' => 'jam_buka'],     ['value' => $request->jam_buka]);
        Pengaturan::updateOrCreate(['key' => 'jam_tutup'],    ['value' => $request->jam_tutup]);

        return back()->with('success', 'Jadwal operasional berhasil diatur! Sistem akan otomatis BUKA pada tanggal tersebut.');
    }
}
