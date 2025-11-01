<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Nasabah; // Mungkin sudah ada
use App\Models\JenisSampah;
use Illuminate\Support\Facades\DB; // Untuk transaksi database yang aman

class TransaksiController extends Controller
{
    // Method untuk halaman "Pilih Transaksi"
    public function pilih($nasabahId)
    {
        // Nanti kita cari nasabah dari database berdasarkan $nasabahId
        // Untuk sekarang kita buat data dummy lagi
        $nasabah = (object)['id' => $nasabahId, 'nama' => 'Budi Santoso', 'saldo' => 50000];
        return view('transaksi.pilih', ['nasabah' => $nasabah]);
    }

    // Method untuk menampilkan form SETOR
    public function createSetor($nasabahId)
    {
        $nasabah = (object)['id' => $nasabahId, 'nama' => 'Budi Santoso', 'saldo' => 50000];
        return view('transaksi.setor', ['nasabah' => $nasabah]);
    }
    
    // Method untuk menampilkan form TARIK
    public function createTarik($nasabahId)
    {
        $nasabah = (object)['id' => $nasabahId, 'nama' => 'Budi Santoso', 'saldo' => 50000];
        return view('transaksi.tarik', ['nasabah' => $nasabah]);
    }

    // ... (method lainnya yang sudah ada)

// Method untuk MEMPROSES data dari form setor


public function storeSetor(Request $request)
{
    // 1. Validasi Input (tidak berubah)
    $validated = $request->validate([
        'nasabah_id' => 'required|exists:nasabahs,id',
        'tanggal_setor' => 'required|date',
        'jenis_sampah' => 'required|string',
        'berat' => 'required|numeric|min:0.1',
    ]);

    // 2. Cari data sampah di DATABASE untuk mendapatkan harganya
    $jenisSampah = JenisSampah::where('nama_sampah', $validated['jenis_sampah'])->firstOrFail();
    
    // 3. Hitung total harga menggunakan harga dari database
    $totalHarga = $jenisSampah->harga_per_kg * $validated['berat'];

    // 4. Simpan ke database menggunakan Transaction (tidak berubah)
    DB::transaction(function () use ($validated, $totalHarga, $jenisSampah) {
        // Simpan riwayat transaksi
        Transaksi::create([
            'nasabah_id' => $validated['nasabah_id'],
            'tanggal_transaksi' => $validated['tanggal_setor'],
            'jenis_transaksi' => 'setor',
            'total_harga' => $totalHarga,
            'jenis_sampah' => $jenisSampah->nama_sampah, // <-- TAMBAHKAN INI
            'berat' => $validated['berat'],
        ]);

        // Update saldo nasabah
        $nasabah = Nasabah::find($validated['nasabah_id']);
        $nasabah->saldo += $totalHarga;
        $nasabah->save();
    });

    // 5. Redirect kembali dengan pesan sukses (tidak berubah)
    return redirect()->route('nasabah.index')->with('success', 'Transaksi setor sampah berhasil dicatat!');
}

// app/Http/Controllers/TransaksiController.php

public function storeTarik(Request $request)
{
    // 1. Validasi Input
    $validated = $request->validate([
        'nasabah_id' => 'required|exists:nasabahs,id',
        'tanggal_tarik' => 'required|date',
        'nominal_penarikan' => 'required|numeric|min:1000',
    ]);

    // 2. Simpan ke database menggunakan Transaction
    DB::transaction(function () use ($validated) {
        // Ambil data nasabah
        $nasabah = Nasabah::findOrFail($validated['nasabah_id']);
        $nominalPenarikan = $validated['nominal_penarikan'];

        // Cek apakah saldo mencukupi
        if ($nasabah->saldo < $nominalPenarikan) {
            // Jika tidak cukup, batalkan transaksi dan kirim error
            throw \Illuminate\Validation\ValidationException::withMessages([
               'nominal_penarikan' => 'Saldo nasabah tidak mencukupi untuk melakukan penarikan ini.',
            ]);
        }
        
        // Simpan riwayat transaksi
        Transaksi::create([
            'nasabah_id' => $validated['nasabah_id'],
            'tanggal_transaksi' => $validated['tanggal_tarik'],
            'jenis_transaksi' => 'tarik',
            'total_harga' => $nominalPenarikan,
        ]);

        // Kurangi saldo nasabah
        $nasabah->saldo -= $nominalPenarikan;
        $nasabah->save();
    });

    // 3. Redirect kembali dengan pesan sukses
    return redirect()->route('nasabah.index')->with('success', 'Transaksi penarikan saldo berhasil dicatat!');
}
}