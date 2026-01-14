<?php

namespace App\Http\Controllers;

use App\Models\Edukasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EdukasiController extends Controller
{
    // Halaman Admin
    public function index()
    {
        $edukasiList = Edukasi::latest()->get();
        return view('admin.edukasi.index', compact('edukasiList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'kategori' => 'required',
            'gambar' => 'nullable|image|max:2048', // Max 2MB
            'link_url' => 'nullable|url',
        ]);

        $pathGambar = null;
        if ($request->hasFile('gambar')) {
            // Simpan ke folder 'public/edukasi'
            $pathGambar = $request->file('gambar')->store('edukasi', 'public');
        }

        Edukasi::create([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'gambar' => $pathGambar,
            'link_url' => $request->link_url,
            'deskripsi' => $request->deskripsi
        ]);

        return redirect()->back()->with('success', 'Konten edukasi berhasil diterbitkan!');
    }

    public function destroy($id)
    {
        $item = Edukasi::findOrFail($id);
        if ($item->gambar) {
            Storage::disk('public')->delete($item->gambar);
        }
        $item->delete();
        return redirect()->back()->with('success', 'Konten dihapus.');
    }
}