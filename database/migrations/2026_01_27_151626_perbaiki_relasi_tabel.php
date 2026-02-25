<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. PERBAIKI TABEL TRANSAKSIS
        Schema::table('transaksis', function (Blueprint $table) {
            // Hapus kolom lama yg cuma teks biasa (kalau ada error column not found, hapus baris ini)
            if (Schema::hasColumn('transaksis', 'jenis_sampah')) {
                $table->dropColumn('jenis_sampah'); 
            }
            
            // Tambah kolom baru yg terhubung ke tabel jenis_sampahs (Relasi Foreign Key)
            // 'constrained' artinya nyambung ke id di tabel jenis_sampahs
            // 'onDelete cascade' artinya kalau jenis sampah dihapus, transaksinya ikut hilang (biar ga error)
            $table->foreignId('jenis_sampah_id')
                  ->nullable()
                  ->after('petugas_id') // Posisi kolom (opsional)
                  ->constrained('jenis_sampahs')
                  ->onDelete('cascade');
        });

        // 2. PERBAIKI TABEL EDUKASIS
        Schema::table('edukasis', function (Blueprint $table) {
            // Tambah kolom user_id (siapa yg upload edukasi?)
            $table->foreignId('user_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('users')
                  ->onDelete('set null'); // Kalau admin dihapus, edukasinya tetep ada (user_id jadi null)
        });

        // 3. PERBAIKI TABEL PENGATURANS (Opsional tapi bagus)
        Schema::table('pengaturans', function (Blueprint $table) {
            $table->foreignId('user_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        // Ini buat jaga-jaga kalau mau membatalkan perubahan (Rollback)
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['jenis_sampah_id']);
            $table->dropColumn('jenis_sampah_id');
            $table->string('jenis_sampah')->nullable(); // Balikin kolom lama
        });

        Schema::table('edukasis', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('pengaturans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};