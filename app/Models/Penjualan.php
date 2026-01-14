<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi: Penjualan milik satu Tengkulak
    public function tengkulak()
    {
        return $this->belongsTo(Tengkulak::class);
    }

    // Relasi: Penjualan terdiri dari satu Jenis Sampah
    public function jenisSampah()
    {
        return $this->belongsTo(JenisSampah::class);
    }
}