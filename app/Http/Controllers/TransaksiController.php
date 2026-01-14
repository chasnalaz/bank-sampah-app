<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Nasabah; 
use App\Models\JenisSampah;
use App\Models\Pengaturan; // <--- TAMBAHAN: Untuk ambil jadwal
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // <--- TAMBAHAN: Untuk olah waktu

class TransaksiController extends Controller
{
    // --- METHOD HELPER (PRIVATE) UNTUK CEK JADWAL ---
    // Kita buat fungsi sendiri biar kodingan storeSetor & storeTarik tidak kepanjangan
    private function cekJadwalOperasional()
    {
        // 1. Ambil Data dari Database
        $tanggalBuka = Pengaturan::where('key', 'tanggal_buka')->value('value');
        $jamBuka     = Pengaturan::where('key', 'jam_buka')->value('value') ?? '08:00';
        $jamTutup    = Pengaturan::where('key', 'jam_tutup')->value('value') ?? '16:00';

        // 2. Cek Tanggal (Apakah Hari Ini == Tanggal Jadwal?)
        $hariIni = Carbon::now()->format('Y-m-d');
        if (!$tanggalBuka || $hariIni != $tanggalBuka) {
            $infoTanggal = $tanggalBuka ? Carbon::parse($tanggalBuka)->translatedFormat('d F Y') : 'Belum Diatur';
            return "Maaf, Transaksi DITOLAK! Bank Sampah tutup hari ini. Jadwal buka: " . $infoTanggal;
        }

        // 3. Cek Jam (Apakah Jam Sekarang di antara Buka & Tutup?)
        $sekarang   = Carbon::now();
        $waktuBuka  = Carbon::createFromTimeString($jamBuka);
        $waktuTutup = Carbon::createFromTimeString($jamTutup);

        if (!$sekarang->between($waktuBuka, $waktuTutup)) {
            return "Maaf, Bank Sampah tutup. Jam operasional hari ini: $jamBuka - $jamTutup WIB";
        }

        return null; // Null artinya "Aman/Buka"
    }

    // Method untuk menampilkan halaman utama transaksi (Daftar Nasabah & Modal)
    public function index()
    {
        // 1. Ambil Semua Nasabah (Bisa pakai pagination kalau data banyak)
        $nasabahList = Nasabah::all(); 

        // 2. Ambil Semua Jenis Sampah (Untuk isi dropdown di Modal Setor)
        $jenisSampahList = JenisSampah::all();

        // 3. Tampilkan View
        return view('nasabah.index', compact('nasabahList', 'jenisSampahList'));
    }
    // Method untuk halaman "Pilih Transaksi"
    public function pilih($nasabahId)
    {
        $nasabah = Nasabah::findOrFail($nasabahId);
        return view('transaksi.pilih', ['nasabah' => $nasabah]);
    }

    // Method untuk menampilkan form SETOR
    public function createSetor($nasabahId)
    {
        $nasabah = Nasabah::findOrFail($nasabahId);
        return view('transaksi.setor', ['nasabah' => $nasabah]);
    }
    
    // Method untuk menampilkan form TARIK
    public function createTarik($nasabahId)
    {
        $nasabah = Nasabah::findOrFail($nasabahId);
        return view('transaksi.tarik', ['nasabah' => $nasabah]);
    }

    // --- PROSES SETOR SAMPAH (SUDAH DITAMBAH SATPAM) ---
    public function storeSetor(Request $request)
    {
        // 1. JALANKAN SATPAM DULU
        $pesanError = $this->cekJadwalOperasional();
        if ($pesanError) {
            return back()->with('error', $pesanError); // Tendang balik jika tutup
        }

        // 2. VALIDASI INPUT
        $validated = $request->validate([
            'nasabah_id' => 'required|exists:nasabahs,id',
            // 'tanggal_setor' => 'required|date',
            'jenis_sampah' => 'required|string',
            'berat' => 'required|numeric|min:0.1',
        ]);

        $jenisSampah = JenisSampah::where('nama_sampah', $validated['jenis_sampah'])->firstOrFail();
        $totalHarga = $jenisSampah->harga_per_kg * $validated['berat'];

        // 3. SIMPAN DATA (DB TRANSACTION)
        $transaksiBaru = DB::transaction(function () use ($validated, $totalHarga, $jenisSampah) {
            
            $transaksi = Transaksi::create([
                'nasabah_id' => $validated['nasabah_id'],
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'setor',
                'total_harga' => $totalHarga,
                'jenis_sampah' => $jenisSampah->nama_sampah,
                'berat' => $validated['berat'],
            ]);

            $nasabah = Nasabah::find($validated['nasabah_id']);
            $nasabah->saldo += $totalHarga;
            $nasabah->save();

            return $transaksi;
        });

        return redirect()->route('nasabah.index')
            ->with('success', 'Transaksi setor sampah berhasil dicatat!')
            ->with('trx_id', $transaksiBaru->id);
    }

    // --- PROSES TARIK SALDO (SUDAH DITAMBAH SATPAM) ---
    public function storeTarik(Request $request)
    {
        // 1. JALANKAN SATPAM DULU
        $pesanError = $this->cekJadwalOperasional();
        if ($pesanError) {
            return back()->with('error', $pesanError); // Tendang balik jika tutup
        }

        // 2. VALIDASI INPUT
        $validated = $request->validate([
            'nasabah_id' => 'required|exists:nasabahs,id',
            'tanggal_transaksi' => Carbon::now(),
            'nominal_penarikan' => 'required|numeric|min:1000',
        ]);

        // 3. SIMPAN DATA (DB TRANSACTION)
        $transaksiBaru = DB::transaction(function () use ($validated) {
            $nasabah = Nasabah::findOrFail($validated['nasabah_id']);
            $nominalPenarikan = $validated['nominal_penarikan'];

            if ($nasabah->saldo < $nominalPenarikan) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                   'nominal_penarikan' => 'Saldo nasabah tidak mencukupi.',
                ]);
            }
            
            $transaksi = Transaksi::create([
                'nasabah_id' => $validated['nasabah_id'],
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'tarik',
                'total_harga' => $nominalPenarikan,
            ]);

            $nasabah->saldo -= $nominalPenarikan;
            $nasabah->save();

            return $transaksi;
        });

        return redirect()->route('nasabah.index')
            ->with('success', 'Transaksi penarikan saldo berhasil dicatat!')
            ->with('trx_id', $transaksiBaru->id);
    }

    // --- FUNGSI CETAK STRUK ---
    public function cetakStruk($id)
    {
        $transaksi = Transaksi::with('nasabah')->findOrFail($id);
        return view('admin.struk.transaksi', compact('transaksi'));
    }
}