<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    // 1. TAMPILKAN HALAMAN EDIT
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    // 2. UPDATE INFO (NAMA & EMAIL)
    public function update(Request $request)
    {
        $user = $request->user();

        // Validasi
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        // Simpan Perubahan
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null; // Reset verifikasi jika ganti email
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }

    // 3. UPDATE PASSWORD (OPSIONAL, BISA DIJADIKAN SATU RUTE KHUSUS)
    // Tapi biasanya di Laravel Breeze/Jetstream dipisah. 
    // Untuk simpelnya, kita pakai logika update password di sini jika ada input password.
    // ATAU kita buat method terpisah jika route-nya berbeda.
    
    // Note: Karena route di web.php kamu cuma ada 'update' (PATCH), 
    // kita asumsikan form password dikirim ke rute yang sama atau rute khusus.
    // Di sini saya buatkan method Hapus Akun (Destroy) sesuai web.php kamu.
    
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}