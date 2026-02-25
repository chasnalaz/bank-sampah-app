<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status'];

    // Relasi ke User (biar tahu ini absennya siapa)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}