<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pengaturan;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Default Jam Operasional
        Pengaturan::create(['key' => 'jam_buka', 'value' => '08:00']);
        Pengaturan::create(['key' => 'jam_tutup', 'value' => '16:00']);
        Pengaturan::create(['key' => 'hari_libur', 'value' => 'Minggu']);
        Pengaturan::create(['key' => 'lokasi_bank', 'value' => 'Jl. Berseri No. 1 Cilacap']);
    }
}
