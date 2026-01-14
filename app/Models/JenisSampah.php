<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSampah extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_sampah',
        'kategori',
        'harga_per_kg',
    ];

    public function tengkulaks()
    {
        return $this->hasMany(Tengkulak::class, 'jenis_sampah_id');
    }
}

