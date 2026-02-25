<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Nasabah;
use App\Models\JenisSampah;
use App\Models\Transaksi;
use App\Models\Penjemputan;
use App\Models\RiwayatHarga;
use App\Models\Absensi;
use App\Models\Tengkulak;
use App\Models\Penjualan;
use App\Models\Edukasi;

class SeederManipulasi extends Seeder
{
    public function run()
    {
        // ==========================================
        // 1. PERSIAPAN DATA MASTER
        // ==========================================
        
        // Buat Admin & Petugas (Pakai firstOrCreate biar aman)
        $admin = User::firstOrCreate(
            ['email' => 'admin@berseri.com'], 
            ['name' => 'Chasna Admin', 'password' => Hash::make('password'), 'role' => 'admin']
        );
        
        $petugas = User::firstOrCreate(
            ['email' => 'petugas_demo@berseri.com'],
            ['name' => 'Budi Petugas Demo', 'password' => Hash::make('password'), 'role' => 'petugas']
        );

        // Ambil data sampah
        $allSampah = JenisSampah::all();
        if($allSampah->isEmpty()) {
            $this->command->error('Tabel jenis_sampahs KOSONG! Mohon isi data sampah manual terlebih dahulu.');
            return;
        }

        // Buat Tengkulak (Jika belum ada)
        if(Tengkulak::count() == 0) {
            Tengkulak::create(['nama_tengkulak' => 'Pak Haji Pengepul', 'jenis_sampah_id' => $allSampah->first()->id, 'harga_beli' => 5000, 'kontak' => '08111']);
            Tengkulak::create(['nama_tengkulak' => 'CV. Daur Ulang Jaya', 'jenis_sampah_id' => $allSampah->last()->id, 'harga_beli' => 7000, 'kontak' => '08222']);
        }
        $allTengkulak = Tengkulak::all();

        // ==========================================
        // 2. INJEKSI NASABAH & EDUKASI
        // ==========================================
        
        // Tambah 15 Nasabah Fiktif
        // REVISI: Hapus kolom 'status' karena tidak ada di tabel nasabahs
        $nasabahIds = [];
        for ($i = 1; $i <= 15; $i++) {
            $n = Nasabah::create([
                'nama' => 'Warga Demo ' . $i,
                'alamat' => 'Komplek Skripsi Blok ' . chr(64 + $i),
                'telepon' => '0812345678' . $i,
                'saldo' => rand(20000, 150000),
                // 'status' => 'aktif', <--- DIHAPUS
                'password' => Hash::make('password'), // Tambahkan password default jika perlu
                'created_at' => Carbon::now()->subMonths(rand(1, 24))
            ]);
            $nasabahIds[] = $n->id;
        }
        $allNasabahIds = Nasabah::pluck('id')->toArray();

        // Tambah Edukasi
        $judulEdukasi = ['Cara Memilah Sampah', 'Bahaya Limbah B3', 'Mengolah Kompos', 'Bank Sampah 101', 'Ekonomi Sirkular'];
        foreach($judulEdukasi as $judul) {
            Edukasi::firstOrCreate(['judul' => $judul], [
                'user_id' => $admin->id,
                'kategori' => 'artikel',
                'deskripsi' => 'Konten edukasi dummy.',
                'created_at' => Carbon::now()->subDays(rand(1, 60))
            ]);
        }

        // ==========================================
        // 3. MESIN WAKTU (LOOPING TRANSAKSI 2025-2026)
        // ==========================================
        
        $currentDate = Carbon::create(2025, 1, 1);
        $endDate = Carbon::now();
        $totalSampahTerkumpul = []; 

        $this->command->info('Memulai simulasi transaksi dari 2025...');

        while ($currentDate <= $endDate) {
            
            // --- A. TRANSAKSI HARIAN ---
            if (rand(1, 100) <= 40) { // 40% chance ada transaksi
                $trxCount = rand(1, 5); 
                
                for ($k = 0; $k < $trxCount; $k++) {
                    $sampah = $allSampah->random();
                    $berat = rand(2, 25);
                    $total = $berat * $sampah->harga_per_kg;

                    // REVISI: Sesuaikan kolom dengan tabel 'transaksis' (Hapus status & harga_per_kg)
                    Transaksi::create([
                        'nasabah_id' => $allNasabahIds[array_rand($allNasabahIds)],
                        'petugas_id' => $petugas->id,
                        'jenis_sampah_id' => $sampah->id,
                        'jenis_transaksi' => 'setor',
                        'tanggal_transaksi' => $currentDate->format('Y-m-d'), // Tambahkan ini
                        'berat' => $berat,
                        // 'harga_per_kg' => ..., <--- DIHAPUS (Tidak ada di DB)
                        'total_harga' => $total,
                        // 'status' => 'selesai', <--- DIHAPUS (Tidak ada di DB)
                        'created_at' => $currentDate->copy()->addHours(rand(8, 14)),
                        'updated_at' => $currentDate->copy()->addHours(rand(14, 16)),
                    ]);

                    if (!isset($totalSampahTerkumpul[$sampah->id])) $totalSampahTerkumpul[$sampah->id] = 0;
                    $totalSampahTerkumpul[$sampah->id] += $berat;
                }
            }

            // --- B. ABSENSI PETUGAS ---
            if ($currentDate->isWeekday()) {
                Absensi::create([
                    'user_id' => $petugas->id,
                    'status' => 'Hadir',
                    // 'keterangan' => '...', <--- Dihapus jika tidak ada kolom keterangan di DB
                    'created_at' => $currentDate->copy()->setTime(8, 0),
                    'updated_at' => $currentDate->copy()->setTime(16, 0),
                ]);
            }

            // --- C. PENJUALAN KE TENGKULAK ---
            if ($currentDate->format('d') == '28') { 
                foreach ($allSampah as $s) {
                    if (isset($totalSampahTerkumpul[$s->id]) && $totalSampahTerkumpul[$s->id] > 50) {
                        $beratJual = $totalSampahTerkumpul[$s->id] * 0.8;
                        $hargaJual = $s->harga_per_kg * (1 + (rand(20, 40) / 100)); 
                        
                        Penjualan::create([
                            'tengkulak_id' => $allTengkulak->random()->id,
                            'jenis_sampah_id' => $s->id,
                            'berat_kg' => $beratJual,
                            'harga_per_kg' => $hargaJual,
                            'total_pendapatan' => $beratJual * $hargaJual,
                            'tanggal_jual' => $currentDate->format('Y-m-d'),
                            'created_at' => $currentDate,
                        ]);
                        $totalSampahTerkumpul[$s->id] -= $beratJual;
                    }
                }
            }

            $currentDate->addDay();
        }

        // ==========================================
        // 4. INJEKSI PENJEMPUTAN
        // ==========================================
        
        // REVISI: Ganti 'catatan' jadi 'catatan_nasabah' & sesuaikan status
        for($j=1; $j<=3; $j++) {
            Penjemputan::create([
                'nasabah_id' => $allNasabahIds[array_rand($allNasabahIds)],
                'alamat_penjemputan' => 'Jl. Demo Sidang No. '.$j,
                'usulan_tanggal' => Carbon::now()->addDays(rand(1, 3)),
                'status' => 'Menunggu Konfirmasi',
                'petugas_id' => null, 
                'catatan_nasabah' => 'Sampah kardus banyak mas.', // Ganti nama kolom
                'created_at' => Carbon::now()
            ]);
        }

        // ==========================================
        // 5. RIWAYAT HARGA
        // ==========================================
        foreach ($allSampah as $s) {
            if (RiwayatHarga::where('jenis_sampah_id', $s->id)->count() < 2) {
                RiwayatHarga::create([
                    'jenis_sampah_id' => $s->id,
                    'user_id' => $admin->id,
                    'harga_lama' => $s->harga_per_kg - 1000,
                    'harga_baru' => $s->harga_per_kg - 500,
                    'created_at' => Carbon::now()->subYear()
                ]);
                RiwayatHarga::create([
                    'jenis_sampah_id' => $s->id,
                    'user_id' => $admin->id,
                    'harga_lama' => $s->harga_per_kg - 500,
                    'harga_baru' => $s->harga_per_kg,
                    'created_at' => Carbon::now()->subMonths(6)
                ]);
            }
        }
    }
}