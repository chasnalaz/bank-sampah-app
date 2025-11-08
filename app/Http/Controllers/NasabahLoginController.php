<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

// --- TAMBAHAN UNTUK REGISTRASI ---
use App\Models\Nasabah;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
// ---------------------------------

class NasabahLoginController extends Controller
{
    /**
     * Menampilkan form login nasabah.
     */
    public function showLoginForm()
    {
        return view('auth.login-nasabah');
    }

    /**
     * Menangani percobaan login nasabah (MENGGUNAKAN NOMOR TELEPON).
     */
    public function login(Request $request): RedirectResponse
    {
        // 1. UBAH VALIDASI: dari 'email' menjadi 'nomor_telepon'
        $credentials = $request->validate([
            'telepon' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // 2. UBAH ATTEMPT: dari 'email' menjadi 'nomor_telepon'
        if (Auth::guard('nasabah')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('nasabah.dashboard'));
        }

        // 3. UBAH PESAN ERROR: dari 'email' menjadi 'nomor_telepon'
        throw ValidationException::withMessages([
            'telepon' => __('auth.failed'),
        ]);
    }

    /**
     * Menangani logout nasabah.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('nasabah')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }


    /**
     * ==================================================
     * FUNGSI UNTUK REGISTRASI NASABAH (TANPA EMAIL)
     * ==================================================
     */

    /**
     * Menampilkan formulir registrasi nasabah.
     */
    public function showRegistrationForm()
    {
        return view('auth.register-nasabah');
    }

    /**
     * Menyimpan data nasabah baru (TANPA EMAIL).
     */
    public function storeRegistration(Request $request): RedirectResponse
    {
        // 1. UBAH VALIDASI: Hapus 'email'
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:255'],
            'telepon' => ['required', 'string', 'max:15', 'unique:nasabahs'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. UBAH CREATE: Hapus 'email'
        $nasabah = Nasabah::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'password' => Hash::make($request->password),
            'saldo' => 0,
        ]);

        // 3. Langsung login-kan nasabah
        Auth::guard('nasabah')->login($nasabah);

        // 4. Redirect ke dashboard nasabah
        return redirect()->route('nasabah.dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang.');
    }
}