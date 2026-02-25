<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Penjemputan;
use App\Models\JenisSampah;
use App\Models\Transaksi;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Tambahkan ini untuk cek tanggal

class PetugasController extends Controller
{
    // --- METHOD DASHBOARD PETUGAS ---
    public function dashboard()
    {
        $user = Auth::user();

        // 1. LOGIKA RESET OTOMATIS (AUTO-EXPIRE)
        // Cek apakah status 'siap' tapi updated_at bukan hari ini
        if ($user->status_tugas == 'siap' && !$user->updated_at->isToday()) {
            
            // Reset jadi izin
            $user->status_tugas = 'izin';
            $user->save();
            
            // Refresh data user
            $user = $user->fresh(); 
        }

        // 2. Hitung Tugas Baru (Waiting List)
        // Sesuaikan status 'pending' atau 'Menunggu Konfirmasi' dengan database kamu
        $permintaanBaruCount = Penjemputan::where('status', 'Menunggu Konfirmasi')->count(); 

        // 3. Hitung Tugas Aktif Saya
        // Sesuaikan status 'diproses' atau 'Diterima' dengan database kamu
        $tugasAktifCount = Penjemputan::where('petugas_id', $user->id)
                                      ->where('status', 'Diterima')->count();

                                      // 2. [BARU] Hitung Kinerja Hari Ini (Rapor Harian)
        // Hitung total kg sampah yang diproses petugas ini HARI INI
        $totalBeratHariIni = Transaksi::where('petugas_id', $user->id)
                                    ->whereDate('created_at', Carbon::today())
                                    ->sum('berat'); // Pastikan nama kolom di DB 'berat' atau 'berat_kg'

        // Hitung total uang yang dikeluarkan (transaksi setor) HARI INI
        $totalUangHariIni = Transaksi::where('petugas_id', $user->id)
                                    ->whereDate('created_at', Carbon::today())
                                    ->where('jenis_transaksi', 'setor')
                                    ->sum('total_harga');
        // 4. Ambil Daftar Harga
        $daftarHargaSampah = JenisSampah::all();

        return view('dashboard-petugas', compact('permintaanBaruCount', 'tugasAktifCount', 'daftarHargaSampah', 'totalBeratHariIni', 'totalUangHariIni'));
    } // <--- PASTIKAN KURUNG INI ADA! (Ini penutup fungsi dashboard)

    // --- METHOD ADMIN: MANAJEMEN PETUGAS ---
    public function index()
    {
        $semuaPetugas = User::where('role', 'petugas')->orderBy('name', 'asc')->get();
        return view('manajemen-petugas.index', ['petugasList' => $semuaPetugas]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'telepon' => ['nullable', 'string', 'max:15'], // Validasi telepon
            'alamat' => ['nullable', 'string'],            // Validasi alamat
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'petugas',
            'telepon' => $request->telepon, // <--- Simpan
            'alamat' => $request->alamat,   // <--- Simpan
        ]);

        return redirect()->route('petugas.manajemen')->with('success', 'Petugas baru berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'telepon' => ['nullable', 'string', 'max:15'], // Validasi
            'alamat' => ['nullable', 'string'],            // Validasi
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->telepon = $request->telepon; // <--- Update
        $user->alamat = $request->alamat;   // <--- Update

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('petugas.manajemen')->with('success', 'Data petugas berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('petugas.manajemen')->with('success', 'Data petugas berhasil dihapus!');
    }

    // --- METHOD PETUGAS: UPDATE STATUS (TOMBOL ABSEN) ---
    // --- METHOD PETUGAS: UPDATE STATUS & ABSENSI ---
    public function updateStatus(Request $request)
    {
        $request->validate([
            'status_tugas' => 'required|in:siap,izin'
        ]);

        $user = $request->user();
        
        // 1. Update Status Tombol (Realtime Status)
        $user->update(['status_tugas' => $request->status_tugas]);

        $pesan = '';

        // 2. LOGIKA ABSENSI (Hanya jika status jadi 'SIAP')
        if ($request->status_tugas == 'siap') {
            
            // Cek apakah hari ini SUDAH absen?
            $sudahAbsen = Absensi::where('user_id', $user->id)
                            ->whereDate('created_at', Carbon::today())
                            ->exists();

            if (!$sudahAbsen) {
                // Kalau belum, catat di buku absen!
                Absensi::create([
                    'user_id' => $user->id,
                    'status' => 'Hadir'
                ]);
                $pesan = 'Absensi berhasil dicatat! Semangat bertugas.';
            } else {
                // Kalau sudah, cuma ganti status tombol aja
                $pesan = 'Status kembali SIAP. (Anda sudah absen hari ini)';
            }

        } else {
            $pesan = 'Status Anda sekarang IZIN. Selamat beristirahat.';
        }

        return back()->with('status_updated', $pesan);
    }
}