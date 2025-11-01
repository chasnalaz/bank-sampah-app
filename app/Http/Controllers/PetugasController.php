<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Penting untuk password
use Illuminate\Validation\Rules;

class PetugasController extends Controller
{
    public function index()
    {
        $semuaPetugas = User::orderBy('name', 'asc')->get();
        return view('manajemen-petugas.index', ['petugasList' => $semuaPetugas]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('petugas.manajemen')->with('success', 'Petugas baru berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('petugas.manajemen')->with('success', 'Data petugas berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        // Tambahkan logika agar tidak bisa menghapus diri sendiri/admin utama
        // if (auth()->user()->id == $user->id) {
        //     return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        // }
        $user->delete();
        return redirect()->route('petugas.manajemen')->with('success', 'Data petugas berhasil dihapus!');
    }
}