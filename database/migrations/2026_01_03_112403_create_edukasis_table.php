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
        Schema::create('edukasis', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->enum('kategori', ['video', 'poster', 'artikel']); // Pilihan tipe
            $table->string('gambar')->nullable(); // Path file gambar
            $table->string('link_url')->nullable(); // Link Youtube
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edukasis');
    }
};
