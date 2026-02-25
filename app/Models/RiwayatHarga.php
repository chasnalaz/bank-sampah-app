<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatHarga extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Semua kolom boleh diisi kecuali ID

    // Relasi ke Jenis Sampah (Biar tau ini riwayat sampah apa)
    public function jenisSampah()
    {
        return $this->belongsTo(JenisSampah::class, 'jenis_sampah_id');
    }

    // Relasi ke User (Biar tau siapa admin yang mengubah)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}