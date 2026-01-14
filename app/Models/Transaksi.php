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
        'tanggal_transaksi',
        'jenis_transaksi',
        'total_harga',
        'jenis_sampah', // Ini kemungkinan Foreign Key ke tabel jenis_sampahs
        'berat',        // Perhatikan nama kolom ini (nanti harus disesuaikan di controller)
    ];

    // ==========================================
    // TAMBAHKAN DUA FUNGSI RELASI DI BAWAH INI
    // ==========================================

    /**
     * Relasi: Transaksi dimiliki oleh satu User (Nasabah)
     */
    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'nasabah_id');
    }

    /**
     * Relasi: Transaksi memiliki satu Jenis Sampah
     */
    public function jenisSampah()
    {
        // Kita asumsikan kolom 'jenis_sampah' di tabel transaksis menyimpan ID jenis sampah
        return $this->belongsTo(JenisSampah::class, 'jenis_sampah');
    }
}