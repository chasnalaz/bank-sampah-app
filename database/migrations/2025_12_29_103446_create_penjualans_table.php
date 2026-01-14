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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel tengkulak & jenis sampah
            $table->foreignId('tengkulak_id')->constrained('tengkulaks')->onDelete('cascade');
            $table->foreignId('jenis_sampah_id')->constrained('jenis_sampahs')->onDelete('cascade');
            
            $table->decimal('berat_kg', 10, 2);
            $table->decimal('harga_per_kg', 15, 2); // Harga deal saat transaksi terjadi
            $table->decimal('total_pendapatan', 15, 2); // berat * harga
            $table->date('tanggal_jual');
            $table->text('catatan')->nullable(); // Opsional, misal: "Diangkut truk A"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
