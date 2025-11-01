<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // <-- Pastikan ini ada
use Illuminate\Support\Facades\Hash; // <-- Pastikan ini ada

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Akun Admin
        User::create([
            'name' => 'Chasna Admin',
            'email' => 'chasnalaz@gmail.com',
            'password' => Hash::make('chasnacantik'),
            'role' => 'admin',
        ]);

        // 2. Buat Akun Petugas (Contoh)
        User::create([
            'name' => 'Chasna Petugas',
            'email' => '22106050018@student.uin-suka.ac.id',
            'password' => Hash::make('chasnafis12'),
            'role' => 'petugas',
        ]);
    }
}