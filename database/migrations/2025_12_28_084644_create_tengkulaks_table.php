<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tengkulaks', function (Blueprint $table) {
            $table->id();
            // Nama pengepul/tengkulak (misal: Pengepul Jaya, Lapak Barokah)
            $table->string('nama_tengkulak'); 
            
            // Relasi ke jenis_sampahs agar kita tahu harga ini untuk sampah apa
            $table->foreignId('jenis_sampah_id')->constrained('jenis_sampahs')->onDelete('cascade');
            
            // Harga yang ditawarkan oleh tengkulak tersebut
            $table->integer('harga_beli'); 
            
            // Lokasi atau nomor telepon (optional, untuk membantu admin menghubungi)
            $table->string('kontak')->nullable();
            
            // Kolom created_at & updated_at untuk tahu kapan terakhir harga diupdate
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tengkulaks');
    }
};