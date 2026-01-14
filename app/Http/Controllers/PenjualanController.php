<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Tengkulak;
use App\Models\JenisSampah;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index()
    {
        // Ambil data penjualan untuk tabel riwayat
        $penjualans = Penjualan::with(['tengkulak', 'jenisSampah'])
                        ->latest('tanggal_jual')
                        ->get();

        // Data pendukung untuk Modal (Kita kirim semua data mentahnya)
        // Kita load relasi jenisSampah agar nanti di Javascript bisa tampil namanya jika perlu
        $tengkulakList = Tengkulak::with('jenisSampah')->get(); 
        $jenisSampahList = JenisSampah::all();

        return view('admin.penjualan.index', compact('penjualans', 'tengkulakList', 'jenisSampahList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tengkulak_id' => 'required',
            'jenis_sampah_id' => 'required',
            'berat_kg' => 'required|numeric|min:0.1',
            'harga_per_kg' => 'required|numeric',
            'tanggal_jual' => 'required|date',
        ]);

        // Hitung total pendapatan otomatis
        $total = $request->berat_kg * $request->harga_per_kg;

        // --- BAGIAN INI YANG DIUBAH ---
        // Kita tampung hasilnya ke variabel $penjualanBaru
        $penjualanBaru = Penjualan::create([
            'tengkulak_id' => $request->tengkulak_id,
            'jenis_sampah_id' => $request->jenis_sampah_id,
            'berat_kg' => $request->berat_kg,
            'harga_per_kg' => $request->harga_per_kg,
            'total_pendapatan' => $total,
            'tanggal_jual' => $request->tanggal_jual,
            'catatan' => $request->catatan,
        ]);

        // Saat redirect, kita kirimkan ID transaksi baru tersebut
        return redirect()->route('penjualan.index')
            ->with('success', 'Data penjualan berhasil disimpan!')
            ->with('trx_id', $penjualanBaru->id); // <--- KUNCI FITUR CETAK
    }

    public function cetakStruk($id)
    {
        // Ambil data penjualan beserta relasi tengkulak dan jenis sampah
        $penjualan = Penjualan::with(['tengkulak', 'jenisSampah'])->findOrFail($id);
        
        // Load view struk
        return view('admin.struk.penjualan', compact('penjualan'));
    }
    
    public function destroy($id)
    {
        Penjualan::findOrFail($id)->delete();
        return redirect()->route('penjualan.index')->with('success', 'Data penjualan dihapus.');
    }
}