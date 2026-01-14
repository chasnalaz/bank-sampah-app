<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class NasabahProfileController extends Controller
{
    // Tampilkan Halaman Profil
    public function index()
    {
        $nasabah = Auth::guard('nasabah')->user();
        return view('nasabah.profil', compact('nasabah'));
    }

    // Proses Update Profil
    public function update(Request $request)
    {
        $nasabah = Auth::guard('nasabah')->user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            // Validasi telepon unik, tapi abaikan punya diri sendiri
            'telepon' => ['required', 'string', Rule::unique('nasabahs')->ignore($nasabah->id)],
            // Password opsional (nullable), kalau diisi harus confirmed
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Update Data Dasar
        $nasabah->nama = $request->nama;
        $nasabah->alamat = $request->alamat;
        $nasabah->telepon = $request->telepon;

        // Update Password (Hanya jika diisi)
        if ($request->filled('password')) {
            $nasabah->password = Hash::make($request->password);
        }

        $nasabah->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}