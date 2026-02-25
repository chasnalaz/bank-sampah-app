<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Nasabah; 
use App\Models\JenisSampah;
use App\Models\Pengaturan;
use App\Models\Penjualan; // <--- TAMBAHAN PENTING
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\WA; 
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    // --- METHOD HELPER (PRIVATE) UNTUK CEK JADWAL ---
    private function cekJadwalOperasional()
    {
        $tanggalBuka = Pengaturan::where('key', 'tanggal_buka')->value('value');
        $jamBuka     = Pengaturan::where('key', 'jam_buka')->value('value') ?? '08:00';
        $jamTutup    = Pengaturan::where('key', 'jam_tutup')->value('value') ?? '16:00';

        $hariIni = Carbon::now()->format('Y-m-d');
        
        if ($tanggalBuka && $hariIni != $tanggalBuka) {
            $infoTanggal = Carbon::parse($tanggalBuka)->translatedFormat('d F Y');
            return "Maaf, Transaksi DITOLAK! Bank Sampah tutup hari ini. Jadwal buka berikutnya: " . $infoTanggal;
        }

        $sekarang   = Carbon::now();
        $waktuBuka  = Carbon::createFromTimeString($jamBuka);
        $waktuTutup = Carbon::createFromTimeString($jamTutup);

        if (!$sekarang->between($waktuBuka, $waktuTutup)) {
            return "Maaf, Bank Sampah tutup. Jam operasional hari ini: $jamBuka - $jamTutup WIB";
        }

        return null;
    }

    // Method untuk menampilkan halaman utama transaksi
    public function index()
    {
        $nasabahList = Nasabah::orderBy('nama', 'asc')->get(); 
        $jenisSampahList = JenisSampah::all();
        return view('nasabah.index', compact('nasabahList', 'jenisSampahList'));
    }

    public function pilih($nasabahId)
    {
        $nasabah = Nasabah::findOrFail($nasabahId);
        return view('transaksi.pilih', ['nasabah' => $nasabah]);
    }

    public function createSetor($nasabahId)
    {
        $nasabah = Nasabah::findOrFail($nasabahId);
        $jenisSampah = JenisSampah::orderBy('nama_sampah', 'asc')->get(); 
        return view('transaksi.setor', ['nasabah' => $nasabah, 'jenisSampah' => $jenisSampah]);
    }
    
    public function createTarik($nasabahId)
    {
        $nasabah = Nasabah::findOrFail($nasabahId);
        return view('transaksi.tarik', ['nasabah' => $nasabah]);
    }

    public function storeSetor(Request $request)
    {
        $pesanError = $this->cekJadwalOperasional();
        if ($pesanError) return back()->with('error', $pesanError);

        $validated = $request->validate([
            'nasabah_id' => 'required|exists:nasabahs,id',
            'jenis_sampah' => 'required|string', 
            'berat' => 'required|numeric|min:0.1',
        ]);

        $jenisSampah = JenisSampah::where('nama_sampah', $validated['jenis_sampah'])->firstOrFail();
        $totalHarga = $jenisSampah->harga_per_kg * $validated['berat'];

        $transaksiBaru = DB::transaction(function () use ($validated, $totalHarga, $jenisSampah) {
            $transaksi = Transaksi::create([
                'nasabah_id' => $validated['nasabah_id'],
                'petugas_id' => Auth::id(), 
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'setor',
                'jenis_sampah_id' => $jenisSampah->id, 
                'berat' => $validated['berat'],
                'total_harga' => $totalHarga,
            ]);

            $nasabah = Nasabah::find($validated['nasabah_id']);
            $nasabah->saldo += $totalHarga;
            $nasabah->save();

            return $transaksi;
        });

        try {
            $nasabah = Nasabah::find($validated['nasabah_id']);
            $pesan = "Halo " . $nasabah->nama . "!\n"
                    . "Transaksi setor sampah berhasil dicatat.\n\n"
                    . "📅 Tanggal: " . Carbon::now()->translatedFormat('d F Y') . "\n"
                    . "♻️ Jenis: " . $jenisSampah->nama_sampah . "\n"
                    . "⚖️ Berat: " . $request->berat . " kg\n"
                    . "💰 Total: Rp " . number_format($totalHarga, 0, ',', '.') . "\n\n"
                    . "💳 Saldo Sekarang: Rp " . number_format($nasabah->saldo, 0, ',', '.') . "\n\n"
                    . "Terima Kasih!";
            if($nasabah->telepon) WA::kirim($nasabah->telepon, $pesan);
        } catch (\Exception $e) {}
        return redirect()->route('nasabah.index')
            ->with('success', 'Transaksi berhasil!')
            ->with('trx_id', $transaksiBaru->id);
    }

    public function storeTarik(Request $request)
    {
        $pesanError = $this->cekJadwalOperasional();
        if ($pesanError) return back()->with('error', $pesanError);

        $validated = $request->validate([
            'nasabah_id' => 'required|exists:nasabahs,id',
            'nominal_penarikan' => 'required|numeric|min:1000',
        ]);

        $transaksiBaru = DB::transaction(function () use ($validated) {
            $nasabah = Nasabah::findOrFail($validated['nasabah_id']);
            if ($nasabah->saldo < $validated['nominal_penarikan']) {
                throw \Illuminate\Validation\ValidationException::withMessages(['nominal_penarikan' => 'Saldo tidak cukup.']);
            }
            
            $transaksi = Transaksi::create([
                'nasabah_id' => $validated['nasabah_id'],
                'petugas_id' => Auth::id(),
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'tarik',
                'total_harga' => $validated['nominal_penarikan'],
                'jenis_sampah_id' => null, 
                'berat' => 0,
            ]);

            $nasabah->saldo -= $validated['nominal_penarikan'];
            $nasabah->save();

            return $transaksi;
        });

        try {
            $nasabah = Nasabah::find($validated['nasabah_id']);
            $pesan = "*PENARIKAN SALDO*\nSisa Saldo: Rp " . number_format($nasabah->saldo, 0, ',', '.');
            if($nasabah->telepon) WA::kirim($nasabah->telepon, $pesan);
        } catch (\Exception $e) {}

        return redirect()->route('nasabah.index')
            ->with('success', 'Penarikan berhasil!')
            ->with('trx_id', $transaksiBaru->id);
    }

    public function cetakStruk($id)
    {
        $transaksi = Transaksi::with(['nasabah', 'jenisSampah', 'petugas'])->findOrFail($id);
        return view('admin.struk.transaksi', compact('transaksi'));
    }

    // --- [BARU] METHOD UNTUK HALAMAN LAPORAN ---
    // --- METHOD UNTUK HALAMAN LAPORAN ---
    public function laporan(Request $request)
    {
        // 1. DEFAULT FILTER: BULAN INI
        if (!$request->has('filter_jenis')) {
            $startDate = Carbon::now()->startOfMonth();
            $endDate   = Carbon::now()->endOfMonth();
            $labelFilter = "Bulan Ini (" . $startDate->translatedFormat('F Y') . ")";
            
            $request->merge(['filter_jenis' => 'bulan', 'bulan' => date('m'), 'tahun' => date('Y')]);
        } 
        else {
            switch ($request->filter_jenis) {
                case 'hari_ini':
                    $startDate = Carbon::today();
                    $endDate   = Carbon::today();
                    $labelFilter = "Hari Ini (" . $startDate->translatedFormat('d M Y') . ")";
                    break;
                case '7_hari':
                    $startDate = Carbon::today()->subDays(6);
                    $endDate   = Carbon::today();
                    $labelFilter = "7 Hari Terakhir";
                    break;
                case 'bulan':
                    $startDate = Carbon::createFromDate($request->tahun, $request->bulan, 1)->startOfMonth();
                    $endDate   = Carbon::createFromDate($request->tahun, $request->bulan, 1)->endOfMonth();
                    $labelFilter = "Bulan " . $startDate->translatedFormat('F Y');
                    break;
                case 'custom':
                    $startDate = Carbon::parse($request->tgl_awal);
                    $endDate   = Carbon::parse($request->tgl_akhir);
                    $labelFilter = "Periode: " . $startDate->format('d/m/Y') . " - " . $endDate->format('d/m/Y');
                    break;
                default:
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate   = Carbon::now()->endOfMonth();
                    $labelFilter = "Bulan Ini";
            }
        }

        // 2. QUERY DATA DENGAN PAGINATION
        $transaksi = Transaksi::with('nasabah')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->latest()
            ->paginate(10, ['*'], 'page_trx')
            ->withQueryString();

        $penjualan = Penjualan::with('tengkulak')
            ->whereBetween('tanggal_jual', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->latest('tanggal_jual')
            ->paginate(10, ['*'], 'page_jual')
            ->withQueryString();

        // 3. HITUNG TOTAL
        $totalPemasukan = Penjualan::whereBetween('tanggal_jual', [$startDate->startOfDay(), $endDate->endOfDay()])->sum('total_pendapatan');
        $totalPengeluaran = Transaksi::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])->sum('total_harga');
        $keuntungan = $totalPemasukan - $totalPengeluaran;

        // --- PERBAIKAN DI SINI: GUNAKAN 'admin.laporan.transaksi' ---
        return view('admin.laporan.transaksi', compact(
            'transaksi', 'penjualan', 
            'totalPemasukan', 'totalPengeluaran', 'keuntungan', 
            'labelFilter'
        ));
    }
}