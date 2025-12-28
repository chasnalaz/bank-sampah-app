<?php

namespace App\Http\Controllers;

use App\Models\Tengkulak;
use App\Models\JenisSampah;
use Illuminate\Http\Request;

class TengkulakController extends Controller
{
    /**
     * Menampilkan daftar semua tengkulak dan data pendukung modal.
     */
    public function index()
    {
        // Menggunakan nama variabel yang sesuai dengan file Blade sebelumnya
        $tengkulakList = Tengkulak::with('jenisSampah')->get();
        
        // Dibutuhkan untuk dropdown di dalam Modal Tambah & Edit
        $jenisSampahList = JenisSampah::all();

        return view('manajemen-tengkulak.index', compact('tengkulakList', 'jenisSampahList'));
    }

    /**
     * Menyimpan data tengkulak baru dari Modal Tambah.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_tengkulak' => 'required|string|max:255',
            'jenis_sampah_id' => 'required|exists:jenis_sampahs,id',
            'harga_beli' => 'required|numeric',
            'kontak' => 'nullable|string|max:20'
        ]);

        Tengkulak::create($request->all());

        // Mengarahkan kembali ke route manajemen-tengkulak.index
        return redirect()->route('manajemen-tengkulak.index')->with('success', 'Data Tengkulak berhasil ditambahkan.');
    }

    /**
     * Mengupdate data dari Modal Edit.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tengkulak' => 'required|string|max:255',
            'jenis_sampah_id' => 'required|exists:jenis_sampahs,id',
            'harga_beli' => 'required|numeric',
            'kontak' => 'nullable|string|max:20'
        ]);

        $tengkulak = Tengkulak::findOrFail($id);
        $tengkulak->update($request->all());

        return redirect()->route('manajemen-tengkulak.index')->with('success', 'Data Tengkulak berhasil diupdate.');
    }

    /**
     * Menghapus data melalui Modal Hapus.
     */
    public function destroy($id)
    {
        $tengkulak = Tengkulak::findOrFail($id);
        $tengkulak->delete();

        return redirect()->route('manajemen-tengkulak.index')->with('success', 'Data Tengkulak berhasil dihapus.');
    }
}