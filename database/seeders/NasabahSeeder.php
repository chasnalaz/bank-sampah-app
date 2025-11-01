<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // <-- TAMBAHKAN INI

class NasabahSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('nasabahs')->insert([
            [
                'nama' => 'Budi Santoso',
                'alamat' => 'Jl. Merdeka No. 10',
                'telepon' => '081234567890',
                'password' => Hash::make('password'), // <-- TAMBAHKAN INI
                'saldo' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Citra Lestari',
                'alamat' => 'Jl. Pahlawan No. 25',
                'telepon' => '081223344556',
                'password' => Hash::make('password'), // <-- TAMBAHKAN INI
                'saldo' => 75000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Ahmad Yani',
                'alamat' => 'Jl. Mawar No. 5',
                'telepon' => '081987654321',
                'password' => Hash::make('password'), // <-- TAMBAHKAN INI
                'saldo' => 32500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}