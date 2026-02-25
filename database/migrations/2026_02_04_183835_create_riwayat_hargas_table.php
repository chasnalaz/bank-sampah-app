<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('riwayat_hargas', function (Blueprint $table) {
            $table->id();
            // Menyimpan ID Sampah (Relasi ke tabel jenis_sampahs)
            $table->foreignId('jenis_sampah_id')->constrained('jenis_sampahs')->onDelete('cascade');
            
            // Menyimpan Jejak Harga
            $table->integer('harga_lama'); // Harga sebelum diedit
            $table->integer('harga_baru'); // Harga setelah diedit
            
            // Menyimpan Siapa Admin yang mengubah (Relasi ke users)
            $table->foreignId('user_id')->constrained('users');
            
            $table->timestamps(); // created_at akan jadi "Tanggal Perubahan"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_hargas');
    }
};
