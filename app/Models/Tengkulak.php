<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tengkulak extends Model
{
    use HasFactory;

    // Menentukan kolom mana saja yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'nama_tengkulak',
        'jenis_sampah_id',
        'harga_beli',
        'kontak',
    ];

    /**
     * Relasi ke Model JenisSampah
     * Satu data harga tengkulak merujuk ke satu jenis sampah tertentu.
     */
    public function jenisSampah()
    {
        return $this->belongsTo(JenisSampah::class, 'jenis_sampah_id');
    }
}