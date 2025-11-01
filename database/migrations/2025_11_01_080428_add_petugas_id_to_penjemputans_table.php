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
        Schema::table('penjemputans', function (Blueprint $table) {
            // Tambahkan kolom 'petugas_id' yang terhubung ke tabel 'users'
            $table->foreignId('petugas_id')
                  ->nullable() // Boleh kosong, karena awalnya belum ada yang klaim
                  ->after('status') // Posisikan setelah kolom status
                  ->constrained('users'); // Terhubung ke tabel 'users'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjemputans', function (Blueprint $table) {
            // Hapus foreign key dan kolom jika di-rollback
            $table->dropForeign(['petugas_id']);
            $table->dropColumn('petugas_id');
        });
    }
};