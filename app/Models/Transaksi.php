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
        'jenis_sampah',
        'berat',
    ];
}