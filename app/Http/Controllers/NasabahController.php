<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nasabah;
use App\Models\Transaksi;
use App\Models\JenisSampah;
use Illuminate\Support\Facades\Hash;

class NasabahController extends Controller
{
    // --- HALAMAN CATAT TRANSAKSI (Ada Fitur Search) ---
    public function index(Request $request)
    {
        // 1. Ambil keyword pencarian
        $keyword = $request->input('cari');

        // 2. Logika Filter: Jika ada keyword, cari nama/alamat. Jika tidak, ambil semua.
        $semuaNasabah = Nasabah::when($keyword, function ($query, $keyword) {
                return $query->where('nama', 'like', "%{$keyword}%");
            })
            ->orderBy('nama', 'asc')
            ->paginate(5)
            ->withQueryString();
        
        // 3. Ambil Data Jenis Sampah (Penting untuk Modal Setor)
        $semuaJenisSampah = JenisSampah::orderBy('nama_sampah', 'asc')->get();

        // 4. Kirim ke View 'nasabah.index' (Halaman Catat Transaksi)
        return view('nasabah.index', [
            'nasabahList' => $semuaNasabah,
            'jenisSampahList' => $semuaJenisSampah
        ]);
    }

    // --- HALAMAN MANAJEMEN DATA NASABAH (Edit/Hapus) ---
    // Tambahkan 'Request $request' di dalam kurung
    public function showManajemen(Request $request)
    {
        // 1. Siapkan Query
        $query = Nasabah::query();

        // 2. Logika Pencarian (Search)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%$search%")
                  ->orWhere('alamat', 'LIKE', "%$search%")
                  ->orWhere('telepon', 'LIKE', "%$search%");
            });
        }

        // 3. Ambil Data dengan Pagination (10 per halaman)
        // Gunakan withQueryString() agar saat pindah halaman, pencarian tidak hilang
        $semuaNasabah = $query->orderBy('nama', 'asc')
                              ->paginate(10)
                              ->withQueryString(); 
        
        // 4. Return View
        // Pastikan nama view ini sesuai dengan folder kamu. 
        // Berdasarkan kode lamamu, viewnya ada di 'manajemen-nasabah.index'
        return view('manajemen-nasabah.index', ['nasabahList' => $semuaNasabah]);
    }

    public function show($id)
    {
        $nasabah = Nasabah::findOrFail($id);
        $riwayatTransaksi = Transaksi::where('nasabah_id', $id)
                                     ->orderBy('tanggal_transaksi', 'desc')
                                     ->orderBy('created_at', 'desc')
                                     ->get();

        return view('manajemen-nasabah.show', compact('nasabah', 'riwayatTransaksi'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:15',
            // Pastikan ada validasi password jika form inputnya ada password
            // 'password' => 'required', 
        ]);

        // Tambahkan Default Password (atau ambil dari input)
        // Di sini saya set defaultnya nomor telepon biar gampang, tapi di-HASH
        $validated['password'] = Hash::make($request->telepon); 

        Nasabah::create($validated);

        return redirect()->route('nasabah.manajemen')->with('success', 'Nasabah baru berhasil ditambahkan!');
    }

    public function update(Request $request, Nasabah $nasabah)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:15',
        ]);

        $nasabah->update($validated);

        return redirect()->route('nasabah.manajemen')->with('success', 'Data nasabah berhasil diperbarui!');
    }

    public function destroy(Nasabah $nasabah)
    {
        Transaksi::where('nasabah_id', $nasabah->id)->delete();
        $nasabah->delete();

        return redirect()->route('nasabah.manajemen')->with('success', 'Data nasabah dan seluruh riwayat transaksinya berhasil dihapus!');
    }
}