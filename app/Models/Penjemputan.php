<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Penjemputan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nasabah_id',
        'alamat_penjemputan',
        'usulan_tanggal',
        'status',
        'jenis_sampah_id',
        'estimasi_berat',
        'catatan_nasabah',
    ];

    // --- TAMBAHKAN FUNGSI INI DI BAWAH ---
    
    /**
     * Mendapatkan data nasabah yang memiliki permintaan penjemputan ini.
     */
    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    // --- TAMBAHKAN FUNGSI INI ---
    
    /**
     * Mendapatkan data jenis sampah utama.
     */
    public function jenisSampah()
    {
        return $this->belongsTo(JenisSampah::class);
    }

    public function petugas()
    {
        // Asumsi: 'petugas_id' di tabel penjemputans
        // merujuk ke 'id' di tabel users.
        return $this->belongsTo(User::class, 'petugas_id');
    }
}