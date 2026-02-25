<?php

namespace App\Http\Controllers;

use App\Models\Penjemputan;
use App\Models\JenisSampah;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use App\Services\WA; 

class PenjemputanController extends Controller
{
    private function cekJadwalOperasional()
    {
        $tanggalBuka = Pengaturan::where('key', 'tanggal_buka')->value('value');
        $jamBuka     = Pengaturan::where('key', 'jam_buka')->value('value') ?? '08:00';
        $jamTutup    = Pengaturan::where('key', 'jam_tutup')->value('value') ?? '16:00';

        $hariIni = Carbon::now()->format('Y-m-d');
        if (!$tanggalBuka || $hariIni != $tanggalBuka) {
            $infoTanggal = $tanggalBuka ? Carbon::parse($tanggalBuka)->translatedFormat('d F Y') : 'Belum Diatur';
            return "AKSI DITOLAK! Bank Sampah sedang tutup. Jadwal buka berikutnya: " . $infoTanggal;
        }

        $sekarang   = Carbon::now();
        $waktuBuka  = Carbon::createFromTimeString($jamBuka);
        $waktuTutup = Carbon::createFromTimeString($jamTutup);

        if (!$sekarang->between($waktuBuka, $waktuTutup)) {
            return "AKSI DITOLAK! Jam operasional hari ini hanya pukul $jamBuka - $jamTutup WIB.";
        }
        return null;
    }

    public function terima(Penjemputan $penjemputan)
    {
        $errorJadwal = $this->cekJadwalOperasional();
        if ($errorJadwal) return back()->with('error', $errorJadwal);

        if ($penjemputan->petugas_id !== null) {
            return back()->with('error', 'Tugas ini sudah diambil oleh petugas lain.');
        }

        $penjemputan->petugas_id = Auth::id();
        $penjemputan->status = 'Diterima';
        $penjemputan->save();

        try {
            $nasabah = $penjemputan->nasabah;
            $namaPetugas = Auth::user()->name;

            $pesan = "*JEMPUTAN DITERIMA* 🚚\n\n" .
                     "Halo Kak *{$nasabah->nama}*,\n" .
                     "Permintaan jemput Anda sudah diambil oleh petugas *{$namaPetugas}*.\n\n" .
                     "Mohon pastikan sampah siap! Petugas sedang meluncur ke lokasi. 🚀";

            WA::kirim($nasabah->telepon, $pesan);
        } catch (Exception $e) {}

        return redirect()->route('penjemputan.tugas')->with('success', 'Tugas diambil. Notifikasi dikirim ke nasabah.');
    }

    public function selesaikan(Request $request, Penjemputan $penjemputan)
    {
        $errorJadwal = $this->cekJadwalOperasional();
        if ($errorJadwal) return back()->with('error', $errorJadwal);

        $request->validate([
            'jenis_sampah_id' => 'required|exists:jenis_sampahs,id',
            'berat_aktual' => 'required|numeric|min:0.01',
        ]);

        if ($penjemputan->petugas_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak menyelesaikan tugas ini.');
        }

        DB::beginTransaction();
        try {
            $jenisSampah = JenisSampah::find($request->jenis_sampah_id);
            $nasabah = $penjemputan->nasabah;
            $berat = $request->berat_aktual;
            $total_harga = $jenisSampah->harga_per_kg * $berat;

            Transaksi::create([
                'nasabah_id' => $penjemputan->nasabah_id,
                'petugas_id' => Auth::id(),
                'jenis_transaksi' => 'setor',

                'jenis_sampah_id' => $penjemputan->jenis_sampah_id, 
                
                'berat' => (float) $penjemputan->estimasi_berat,
                'total_harga' => $total_harga,
                'tanggal_transaksi' => now(),
            ]);

            $nasabah->saldo += $total_harga;
            $nasabah->save();

            $penjemputan->status = 'Selesai';
            $penjemputan->save();

            DB::commit();

            try {
                $pesan = "*JEMPUTAN SELESAI* ✅\n\n" .
                         "Halo Kak *{$nasabah->nama}*,\n" .
                         "Sampah jemputan Anda sudah berhasil kami angkut & timbang.\n\n" .
                         "⚖️ Berat: {$berat} Kg\n" .
                         "💰 Nilai: Rp " . number_format($total_harga, 0, ',', '.') . "\n" .
                         "💳 *Saldo Total: Rp " . number_format($nasabah->saldo, 0, ',', '.') . "*\n\n" .
                         "Terima kasih sudah memilah sampah! 🌍";
                WA::kirim($nasabah->telepon, $pesan);
            } catch (Exception $e) {}

            return back()->with('success', 'Tugas selesai, saldo masuk, notifikasi terkirim.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function batalkan(Penjemputan $penjemputan)
    {
        if ($penjemputan->petugas_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak membatalkan tugas ini.');
        }
        $penjemputan->petugas_id = null;
        $penjemputan->status = 'Menunggu Konfirmasi';
        $penjemputan->save();
        return redirect()->route('penjemputan.tugas')->with('success', 'Tugas dibatalkan, kembali ke daftar tunggu.');
    }

    public function index()
    {
        $petugasId = Auth::id();
        $permintaanBaru = Penjemputan::whereNull('petugas_id')->where('status', 'Menunggu Konfirmasi')->with('nasabah', 'jenisSampah')->latest('usulan_tanggal')->get();            
        $tugasAktif = Penjemputan::where('status', 'Diterima')->where('petugas_id', $petugasId)->with('nasabah', 'jenisSampah')->latest('usulan_tanggal')->get();
        $riwayatSelesai = Penjemputan::where('status', 'Selesai')->where('petugas_id', $petugasId)->with('nasabah')->latest('updated_at')->get();
        $allJenisSampah = JenisSampah::orderBy('nama_sampah', 'asc')->get();

        // GANTI BARIS return view(...) MENJADI:
        return view('tugas-penjemputan.index', [
            'permintaanBaruList' => $permintaanBaru, 
            'tugasAktifList'     => $tugasAktif,      // <--- Ini kuncinya! Kita ubah namanya buat View
            'riwayatSelesaiList' => $riwayatSelesai,
            'allJenisSampah'     => $allJenisSampah, 
        ]);
    }

    public function adminIndex()
    {
        $permintaanBaru = Penjemputan::whereNull('petugas_id')->where('status', 'Menunggu Konfirmasi')->with('nasabah', 'jenisSampah')->latest('usulan_tanggal')->get();
        $tugasBerlangsung = Penjemputan::where('status', 'Diterima')->whereNotNull('petugas_id')->with('nasabah', 'jenisSampah', 'petugas')->latest('usulan_tanggal')->get();
        $riwayatTugas = Penjemputan::where('status', 'Selesai')->whereNotNull('petugas_id')->with('nasabah', 'petugas')->latest('updated_at')->get();

        $daftarPetugas = User::where('role', 'petugas')
            ->where('status_tugas', 'siap')
            // TAMBAHAN: Pastikan status 'siap' itu adalah update HARI INI
            ->whereDate('updated_at', Carbon::today()) 
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.penjemputan.index', compact('permintaanBaru', 'tugasBerlangsung', 'riwayatTugas', 'daftarPetugas'));
    }

    public function adminAssign(Request $request, Penjemputan $penjemputan)
    {
        $request->validate(['petugas_id' => 'required|exists:users,id']);

        if ($penjemputan->petugas_id !== null || $penjemputan->status !== 'Menunggu Konfirmasi') {
            return back()->with('error', 'Tugas ini sudah diambil.');
        }

        $penjemputan->petugas_id = $request->petugas_id;
        $penjemputan->status = 'Diterima'; 
        $penjemputan->save();

        // --- UPDATE NOTIFIKASI WA (KE NASABAH & PETUGAS) ---
        try {
            $nasabah = $penjemputan->nasabah;
            $petugas = User::find($request->petugas_id); // Ambil data petugas

            // 1. KIRIM KE NASABAH (Info bahwa petugas sudah OTW)
            $pesanNasabah = "*JEMPUTAN DITERIMA* 🚚\n\n" .
                     "Halo Kak *{$nasabah->nama}*,\n" .
                     "Permintaan jemput Anda ditugaskan ke *{$petugas->name}*.\n" .
                     "Petugas segera meluncur! 🌱";
            
            WA::kirim($nasabah->telepon, $pesanNasabah);

            // 2. KIRIM KE PETUGAS (Info ada tugas baru dari Admin) <-- TAMBAHAN BARU
            // Pastikan tabel 'users' punya kolom 'telepon'
            if ($petugas->telepon) {
                $pesanPetugas = "*TUGAS PENJEMPUTAN BARU* 🚨\n\n" .
                        "Halo *{$petugas->name}*,\n" .
                        "Admin baru saja menugaskan Anda untuk menjemput sampah.\n\n" .
                        "👤 Nasabah: {$nasabah->nama}\n" .
                        "📍 Lokasi: {$penjemputan->alamat_penjemputan}\n" .
                        "📦 Sampah: " . ($penjemputan->jenisSampah->nama_sampah ?? 'Campur') . "\n\n" .
                        "Segera cek aplikasi menu 'Tugas Penjemputan'! 🛵";
                
                WA::kirim($petugas->telepon, $pesanPetugas);
            }

        } catch (Exception $e) {
            // Log error jika WA gagal, tapi jangan hentikan proses assign
        }

        return back()->with('success', 'Petugas ditugaskan. Notifikasi dikirim ke Nasabah & Petugas.');
    }

    public function adminDestroy(Penjemputan $penjemputan)
    {
        if ($penjemputan->status == 'Selesai') return back()->with('error', 'Tidak bisa hapus tugas selesai.');
        $penjemputan->delete();
        return back()->with('success', 'Permintaan dihapus.');
    }
}