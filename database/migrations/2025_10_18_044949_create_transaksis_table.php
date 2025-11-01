<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('transaksis', function (Blueprint $table) {
        $table->id();
        $table->foreignId('nasabah_id')->constrained('nasabahs'); // Terhubung ke tabel nasabah
        $table->date('tanggal_transaksi');
        $table->enum('jenis_transaksi', ['setor', 'tarik']);
        $table->integer('total_harga'); // Untuk setor, ini harga sampah. Untuk tarik, ini nominal penarikan.
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
