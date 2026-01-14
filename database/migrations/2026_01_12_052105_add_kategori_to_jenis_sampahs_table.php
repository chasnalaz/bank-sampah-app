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
        Schema::table('jenis_sampahs', function (Blueprint $table) {
            // Kita pakai ENUM agar datanya konsisten
            // Tambahkan 'Lainnya' untuk jaga-jaga
            $table->enum('kategori', ['Plastik', 'Kertas', 'Logam', 'Elektronik', 'Lainnya'])
                ->after('nama_sampah') // Menaruh kolom setelah nama
                ->default('Lainnya');
        });
    }

    public function down()
    {
        Schema::table('jenis_sampahs', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
