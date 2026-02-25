<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nasabah_id',
        'petugas_id', // <--- WAJIB ADA: Agar ID petugas bisa disimpan
        'tanggal_transaksi',
        'jenis_transaksi',
        'total_harga',
        'jenis_sampah',
        'berat',
    ];

    // ==========================================
    // RELASI TABEL
    // ==========================================

    /**
     * Relasi: Transaksi dimiliki oleh satu User (Nasabah)
     */
    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'nasabah_id');
    }

    /**
     * Relasi: Transaksi dicatat oleh satu User (Petugas)
     * PENTING: Ini agar fitur Analisis Kinerja Petugas berjalan!
     */
    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Relasi: Transaksi memiliki satu Jenis Sampah
     */

    // Relasi ke Jenis Sampah (Baru)
    public function jenisSampah()
    {
        return $this->belongsTo(JenisSampah::class, 'jenis_sampah_id');
    }
}