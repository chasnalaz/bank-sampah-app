<?php

namespace App\Http\Controllers;

use App\Models\JenisSampah;
use Illuminate\Http\Request;

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
            'harga_per_kg' => 'required|integer|min:0',
        ]);

        $jenisSampah->update($validated);
        return redirect()->route('sampah.manajemen')->with('success', 'Data sampah berhasil diperbarui!');
    }

    // Menghapus data sampah
    public function destroy(JenisSampah $jenisSampah)
    {
        $jenisSampah->delete();
        return redirect()->route('sampah.manajemen')->with('success', 'Data sampah berhasil dihapus!');
    }
}