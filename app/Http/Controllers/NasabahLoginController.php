<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class NasabahLoginController extends Controller
{
    /**
     * Menampilkan halaman form login untuk nasabah.
     */
    public function showLoginForm()
    {
        return view('auth.login-nasabah');
    }

    /**
     * Memproses data login dari form.
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'telepon' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Cari nasabah berdasarkan nomor telepon
        $nasabah = Nasabah::where('telepon', $credentials['telepon'])->first();

        // 3. Cek apakah nasabah ada & password cocok
        if (!$nasabah || !Hash::check($credentials['password'], $nasabah->password)) {
            // Jika gagal, kembali ke halaman login dengan pesan error
            return back()->withErrors([
                'telepon' => 'Nomor telepon atau password salah.',
            ])->onlyInput('telepon');
        }

        // 4. Jika berhasil, LOGIN-kan nasabah menggunakan guard 'nasabah'
            Auth::guard('nasabah')->login($nasabah);
            $request->session()->regenerate();

            // 5. Arahkan ke dashboard nasabah
            return redirect()->intended('/nasabah/dashboard');
    }

    /**
     * Proses logout nasabah.
     */
    public function logout(Request $request)
    {
        Auth::guard('nasabah')->logout(); // Gunakan metode logout resmi

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('nasabah.login');
    }
}