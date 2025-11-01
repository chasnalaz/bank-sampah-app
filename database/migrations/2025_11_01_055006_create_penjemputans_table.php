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
        Schema::create('penjemputans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained('nasabahs');
            $table->text('alamat_penjemputan');
            $table->date('usulan_tanggal');
            $table->string('status')->default('Menunggu Konfirmasi');

            // --- KOLOM BARU KITA SESUAI RENCANA ---
            $table->foreignId('jenis_sampah_id')->nullable()->constrained('jenis_sampahs');
            $table->string('estimasi_berat')->nullable();
            $table->text('catatan_nasabah')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjemputans');
    }
};