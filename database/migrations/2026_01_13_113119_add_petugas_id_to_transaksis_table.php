<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Menambahkan kolom petugas_id setelah nasabah_id
            // nullable() dipasang biar transaksi lama yang gapunya petugas tidak error
            $table->foreignId('petugas_id')
                  ->nullable()
                  ->after('nasabah_id')
                  ->constrained('users')
                  ->onDelete('set null'); 
        });
    }

    public function down()
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['petugas_id']);
            $table->dropColumn('petugas_id');
        });
    }
};