<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Nasabah;
use App\Models\Transaksi;
use App\Models\JenisSampah;

class NasabahController extends Controller
{
    public function index()
    {
        // Mengambil SEMUA data dari tabel nasabahs
        $semuaNasabah = Nasabah::all();
        
        // MENGAMBIL SEMUA DATA JENIS SAMPAH
        $semuaJenisSampah = JenisSampah::orderBy('nama_sampah', 'asc')->get();

        // Kirim kedua data ke view
        return view('nasabah.index', [
            'nasabahList' => $semuaNasabah,
            'jenisSampahList' => $semuaJenisSampah
        ]);
    }

    public function showManajemen()
    {
    // Mengambil semua nasabah, diurutkan berdasarkan nama
    $semuaNasabah = Nasabah::orderBy('nama', 'asc')->get(); 
    return view('manajemen-nasabah.index', ['nasabahList' => $semuaNasabah]);
    }

    public function store(Request $request)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:15',
        ]);

        // 2. Simpan data ke database
        Nasabah::create($validated);

        // 3. Redirect kembali ke halaman manajemen dengan pesan sukses
        return redirect()->route('nasabah.manajemen')->with('success', 'Nasabah baru berhasil ditambahkan!');
    }

    public function update(Request $request, Nasabah $nasabah)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:15',
        ]);

        // 2. Update data di database
        $nasabah->update($validated);

        // 3. Redirect kembali dengan pesan sukses
        return redirect()->route('nasabah.manajemen')->with('success', 'Data nasabah berhasil diperbarui!');
    }

    public function destroy(Nasabah $nasabah)
    {
        // 1. Hapus semua transaksi yang terkait dengan nasabah ini
        Transaksi::where('nasabah_id', $nasabah->id)->delete();

        // 2. Setelah transaksinya bersih, baru hapus data nasabahnya
        $nasabah->delete();

        // 3. Redirect kembali dengan pesan sukses
        return redirect()->route('nasabah.manajemen')->with('success', 'Data nasabah dan seluruh riwayat transaksinya berhasil dihapus!');
    }
}