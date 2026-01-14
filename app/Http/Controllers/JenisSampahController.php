<?php

namespace App\Http\Controllers;

use App\Models\JenisSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JenisSampahController extends Controller
{
    // Menampilkan halaman manajemen sampah
    public function index()
    {
        $semuaSampah = JenisSampah::orderBy('nama_sampah', 'asc')->get();
        return view('manajemen-sampah.index', ['sampahList' => $semuaSampah]);
    }

    // Menyimpan data sampah baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sampah' => 'required|string|unique:jenis_sampahs|max:255',
            'kategori' => 'required',
            'harga_per_kg' => 'required|integer|min:0',
        ]);

        JenisSampah::create($validated);
        return redirect()->route('sampah.manajemen')->with('success', 'Jenis sampah baru berhasil ditambahkan!');
    }

    // Memperbarui data sampah
    public function update(Request $request, JenisSampah $jenisSampah)
    {
        $validated = $request->validate([
            'nama_sampah' => 'required|string|max:255|unique:jenis_sampahs,nama_sampah,' . $jenisSampah->id,
            'kategori' => 'required',
            'harga_per_kg' => 'required|integer|min:0',
        ]);

        $jenisSampah->update($validated);
        return redirect()->route('sampah.manajemen')->with('success', 'Data sampah berhasil diperbarui!');
    }

    // Menghapus data sampah
    public function destroy($id)
    {
        // 1. Cari data sampahnya dulu
        $jenisSampah = JenisSampah::findOrFail($id);

        // 2. Cek apakah sampah ini dipakai di tabel 'penjemputans'?
        // (Sesuai error log kamu tadi: integrity constraint violation di penjemputans)
        $terpakaiDiPenjemputan = DB::table('penjemputans')
                                   ->where('jenis_sampah_id', $id)
                                   ->exists();

        // 3. Cek apakah sampah ini dipakai di tabel 'transaksis'?
        // (Jaga-jaga biar ga error juga di sini)
        $terpakaiDiTransaksi = DB::table('transaksis')
                                 ->where('jenis_sampah', $id)
                                 ->exists();

        // 4. Logika Pengecekan
        if ($terpakaiDiPenjemputan || $terpakaiDiTransaksi) {
            // JIKA TERPAKAI: Jangan dihapus, kembalikan dengan pesan error merah
            return redirect()->route('sampah.manajemen')
                             ->with('error', 'Gagal dihapus! Jenis sampah ini masih tercatat dalam riwayat Transaksi atau Penjemputan. Hapus data riwayatnya dulu jika ingin menghapus jenis ini.');
        }

        // 5. JIKA AMAN (TIDAK TERPAKAI): Baru boleh hapus
        $jenisSampah->delete();

        return redirect()->route('sampah.manajemen')
                         ->with('success', 'Jenis sampah berhasil dihapus!');
    }

}